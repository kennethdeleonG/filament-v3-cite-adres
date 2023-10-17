<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Domain\Asset\Actions\DeleteAssetAction;
use App\Domain\Asset\Actions\MoveAssetAction;
use App\Domain\Asset\Models\Asset;
use App\Domain\Faculty\Models\Faculty;
use App\Domain\Folder\Models\Folder;
use App\Filament\Admin\Pages\Document;
use App\Filament\Faculty\Resources\DocumentResource;
use App\Support\Enums\UserType;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Notifications;

/**
 * @property \Filament\Forms\ComponentContainer $form
 */
class AssetModal extends Component implements HasForms
{
    use InteractsWithForms;

    public ?Asset $asset = null;
    public mixed $ownerRecordId = null;
    public ?string $assetName = null;
    public ?int $previousFolderId = null;
    public ?int $navigateFolderId = null;
    public bool $navigateRoot = true;
    public ?string $navigateFolderName = null;
    public ?array $data = [];

    /** @var array */
    protected $listeners = [
        'moveAssetToFolder' => 'moveAssetToFolderModal',
        'deleteAsset' => 'deleteAssetModal',
        'commentAsset' => 'commentAssetModal'
    ];

    public function mount(int $folderId = null): void
    {
        $this->ownerRecordId = $folderId;
        $this->form->fill();
    }

    public function render(): View
    {
        return view('filament.components.livewire.asset-modal');
    }

    //delete listener
    public function deleteAssetModal(array $data): void
    {
        $assetModel = Asset::find($data['id']);

        $this->dispatch('open-modal', id: 'delete-asset-modal-handle');

        $this->asset = $assetModel instanceof Asset ? $assetModel : null;
    }

    // the delete handler
    public function deleteAction(): void
    {
        $recordToDelete = $this->asset;

        if (isset($this->asset)) {
            $result = app(DeleteAssetAction::class)->execute($this->asset);

            if ($result) {
                $this->dispatch('refreshPage', 'delete-asset', json_encode($recordToDelete));
                $this->dispatch('close-modal', id: 'delete-asset-modal-handle');
                Notification::make()
                    ->title('Asset Deleted')
                    ->success()
                    ->send();
            }
        }
    }

    // link to listener
    public function moveAssetToFolderModal(array $data, int|null $folderIdParam): void
    {
        $assetModel = Asset::with('folder')->find($data['id']);

        $this->dispatch('open-modal', id: 'move-asset-modal-handle');

        $this->asset = $assetModel instanceof Asset ? $assetModel : null;

        $this->navigateFolderId = $folderIdParam;

        if (!is_null($folderIdParam)) {
            $this->navigateRoot = false;
        }
    }

    //moving of asset
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

    //$moveTo is the id of folder
    public function moveAsset(?int $moveTo = null): void
    {
        /** @var int */
        $folderId = $this->asset?->folder_id;

        if ($folderId === $moveTo) {
            Notification::make()
                ->title("Can't Move. This Asset is already in this directory.")
                ->warning()
                ->send();

            return;
        }

        $oldPath = $this->asset && $this->asset->folder ? $this->asset->folder->path : '';
        $parent = Folder::find($moveTo);

        $newPath = $parent ? $parent->path : '';

        $result = app(MoveAssetAction::class)
            ->execute($this->asset, $moveTo, $oldPath, $newPath);

        if ($result instanceof Asset) {
            $this->dispatch('refreshPage', 'move-asset', json_encode($result));
            $this->dispatch('close-modal', id: 'move-asset-modal-handle');
            Notification::make()
                ->title('Asset Moved')
                ->success()
                ->send();
        }
    }

    public function closeMoveModalAsset(): void
    {
        $this->assetName = null;
        $this->navigateFolderId = null;
        $this->navigateFolderName = null;
        $this->previousFolderId = null;
    }

    //comment modal 
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('comment')
                    ->label('Comment')
            ])
            ->statePath('data');
    }

    //comment listener
    public function commentAssetModal(array $data): void
    {
        $assetModel = Asset::find($data['id']);

        if ($assetModel->author_type != UserType::FACULTY->value) {
            Notification::make()
                ->title("You can't add a comment to this asset.")
                ->warning()
                ->send();

            return;
        }

        $this->form->fill($data);

        $this->dispatch('open-modal', id: 'comment-asset-modal-handle');

        $this->asset = $assetModel instanceof Asset ? $assetModel : null;
    }

    //the comment handler
    public function commentAction(): void
    {
        $comment = $this->form->getState()['comment'];

        if ($comment) {
            $record = $this->asset;

            $record->update([
                'comment' => $comment
            ]);

            $author = Faculty::find($this->asset->author_id);

            $author->notify(
                Notification::make()
                    ->title("Admin added a comment to your file.")
                    ->body($comment)
                    ->actions([
                        Notifications\Actions\Action::make('View')
                            ->outlined()
                            ->url(route('filament.faculty.resources.documents.edit', [
                                'record' => $record,
                                'ownerRecord' => $record->folder ?? null,
                                'label' => $record->folder->name
                            ]), shouldOpenInNewTab: true),
                        // ->url("http://filament-cite.test/faculty/documents/file/$record->slug/edit", true),
                        Notifications\Actions\Action::make('Mark as read')
                            ->outlined()
                            ->markAsRead(),
                    ])
                    ->toDatabase(),
            );

            $this->dispatch('close-modal', id: 'comment-asset-modal-handle');
            Notification::make()
                ->title('Comment updated.')
                ->success()
                ->send();
        }
    }
}
