<?php

namespace App\Livewire;

use App\Domain\Folder\Actions\DeleteFolderAction;
use App\Domain\Folder\Actions\MoveFolderAction;
use App\Domain\Folder\Actions\UpdateFolderAction;
use App\Domain\Folder\DataTransferObjects\FolderData;
use App\Domain\Folder\Models\Folder;
use App\Filament\Admin\Pages\Document;
use Filament\Forms\Concerns\InteractsWithForms;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Filament\Forms;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Form;

class FolderModal extends Component implements HasForms
{
    use InteractsWithForms;

    public ?Folder $folder = null;
    public ?int $folderId = null;
    public ?string $folderName = null;
    public ?int $navigateFolderId = null;
    public bool $navigateRoot = true;
    public ?int $initialFolderIdParam = null;
    public ?string $navigateFolderName = null;
    public ?int $previousFolderId = null;
    public ?int $parentId = null;
    public ?array $data = [];

    /** @var array */
    protected $listeners = [
        'editFolder' => 'editFolderModal',
        'deleteFolder' => 'deleteFolderModal',
        'moveFolder' => 'moveFolderModal',
        'closeMoveModal',
    ];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function render()
    {
        return view('filament.components.livewire.folder-modal');
    }

    // //edit folder modal
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('')
                    ->validationAttribute('name')
                    ->dehydrateStateUsing(function ($state) {
                        $existingRecords = Folder::where('name', 'LIKE', $state . '%')->where('folder_id', $this->parentId)
                            ->whereNot('id', $this->folderId)->count();
                        if ($existingRecords > 0) {
                            return $state . ' - (' . $existingRecords . ')';
                        }

                        return $state;
                    }),
                Forms\Components\Toggle::make('is_private')->label('Private'),
            ])
            ->statePath('data');
    }

    //edit listener
    public function editFolderModal(array $data): void
    {
        $folderModel = Folder::with('assets')->find($data['id']);

        $this->form->fill($data);

        $this->dispatch('open-modal', id: 'edit-folder-modal-handle');

        $this->folder = $folderModel instanceof Folder ? $folderModel : null;
        $this->folderId = $folderModel instanceof Folder ? $folderModel->id : null;
        $this->folderName = $folderModel instanceof Folder ? $folderModel->name : null;
        $this->parentId = $folderModel instanceof Folder && $folderModel->parent ? $folderModel->parent->id : null;
    }

    //the edit handler
    public function editAction(): void
    {
        $folder_name = $this->form->getState()['name'];
        $folder_visibility = $this->form->getState()['is_private'];

        if (is_null($folder_name)) {
            return;
        }

        $updatedPath = '';
        if ($this->folder && !is_null($this->folder->parent)) {
            $folderPath = $this->folder->parent->path . '/' . Str::slug($folder_name);
            $updatedPath = $folderPath;
        } else {
            $updatedPath = '/' . Str::slug($folder_name);
        }

        $data['name'] = $folder_name;
        $data['slug'] = Str::slug($folder_name);
        $data['path'] = $updatedPath;
        $data['is_private'] = $folder_visibility;

        if (isset($this->folder)) {
            $result = app(UpdateFolderAction::class)
                ->execute($this->folder, FolderData::fromArray($data));

            if ($result instanceof Folder) {
                $this->dispatch('refreshPage', 'update', json_encode($result));
                $this->dispatch('close-modal', id: 'edit-folder-modal-handle');
                Notification::make()
                    ->title('Folder Updated')
                    ->success()
                    ->send();
                if ($folder_name != $this->folderName) {
                    app(UpdateFolderAction::class)
                        ->updateDescendantPaths($this->folder, $updatedPath);
                }
            }
        }
    }

    //delete listener
    public function deleteFolderModal(array $data): void
    {
        $folderModel = Folder::find($data['id']);

        $this->dispatch('open-modal', id: 'delete-folder-modal-handle');

        $this->folder = $folderModel instanceof Folder ? $folderModel : null;
    }

    // the delete handler
    public function deleteAction(): void
    {
        $recordToDelete = $this->folder;

        if (isset($this->folder)) {
            $result = app(DeleteFolderAction::class)->execute($this->folder);

            if ($result) {
                $this->dispatch('refreshPage', 'delete', json_encode($recordToDelete));
                $this->dispatch('close-modal', id: 'delete-folder-modal-handle');
                Notification::make()
                    ->title('Folder Deleted')
                    ->success()
                    ->send();
            }
        }
    }

    //move listener
    public function moveFolderModal(array $data, int|null $folderIdParam): void
    {
        $folderModel = Folder::with('assets')->find($data['id']);

        $this->dispatch('open-modal', id: 'move-folder-modal-handle');
        $this->folder = $folderModel instanceof Folder ? $folderModel : null;
        $this->folderName = $folderModel instanceof Folder ? $folderModel->name : null;
        $this->folderId = $folderModel instanceof Folder ? $folderModel->id : null;

        $this->navigateFolderId = $folderIdParam;

        if (!is_null($folderIdParam)) {
            $this->navigateRoot = false;
        }
    }

    //moving of folder
    /** @return Collection<int, Folder> */
    public function getMoveFolders(): Collection
    {
        $result = Folder::where(function ($query) {
            if ($this->navigateFolderId) {
                $query->where('folder_id', $this->navigateFolderId);
            } else {
                $query->whereNull('folder_id');
            }
        })->orderBy('name')->get();

        return $result;
    }

    //$moveTo is the id of folder
    public function moveFolder(?int $moveTo = null): void
    {
        $folderId = $this->folder?->folder_id;

        if ($folderId === $moveTo) {
            Notification::make()
                ->title("Can't Move. This Asset is already in this directory.")
                ->warning()
                ->send();

            return;
        }

        $convertedName = '';
        $convertedSlug = '';
        $updatedPath = '';
        if (is_null($moveTo)) {
            $convertedName = $this->folderName;
            $convertedSlug = $convertedName ? Str::slug($convertedName) : '';
            $updatedPath = $this->folder ? '/' . $this->folder->slug : '';
        } else {

            if (is_null($this->folder)) {
                return;
            }
            //check if this folder name is existing dun sa pagmomove-an na directory
            $existingRecords = DB::table('folders')->where('name', 'LIKE', $this->folderName . '%')->where('folder_id', $moveTo)->count();

            if ($existingRecords > 0) {
                $convertedName = $this->folderName . ' - (' . $existingRecords . ')';
            } else {
                $convertedName = $this->folderName;
            }

            $convertedSlug = $convertedName ? Str::slug($convertedName) : '';
            $folder = Folder::find($moveTo);
            if ($folder && !is_null($folder->ancestors)) {
                $folderPath = $folder->path . '/' . $convertedSlug;
                $updatedPath = $folderPath;
            }
        }

        $data['name'] = $convertedName;
        $data['slug'] = $convertedSlug;
        $data['path'] = $updatedPath;
        $data['folder_id'] = $moveTo;
        $parentId = $this->folder ? $this->folder->folder_id : null;

        if (isset($this->folder)) {
            $result = app(MoveFolderAction::class)
                ->execute($this->folder, FolderData::fromArray($data));

            if ($result instanceof Folder) {
                $this->dispatch('refreshPage', 'move', json_encode($result));
                $this->dispatch('close-modal', id: 'move-folder-modal-handle');
                Notification::make()
                    ->title('Folder Moved')
                    ->success()
                    ->send();
                if ($moveTo != $parentId) {
                    app(UpdateFolderAction::class)
                        ->updateDescendantPaths($this->folder, $updatedPath);
                }
            }
        }
    }

    public function navigateMove(?int $folderId = null): void
    {
        $this->navigateFolderId = $folderId;

        $record = Folder::where('id', $folderId)->with('parent')->first();

        if ($record) {
            $this->navigateFolderName = $record->name;

            if ($record->parent) {
                $this->previousFolderId = $record->parent->id;
            } else {
                $this->previousFolderId = null;
            }
        } else {
            $this->navigateFolderName = '';
            $this->previousFolderId = null;
        }

        $this->getMoveFolders();
    }

    public function closeMoveModal(): void
    {
        $this->folderId = null;
        $this->folderName = null;
        $this->navigateFolderId = null;
        $this->navigateFolderName = null;
        $this->previousFolderId = null;
    }
}
