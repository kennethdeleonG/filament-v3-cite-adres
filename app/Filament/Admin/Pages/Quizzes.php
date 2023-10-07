<?php

namespace App\Filament\Admin\Pages;

use App\Domain\Folder\Actions\CreateFolderAction;
use App\Domain\Folder\DataTransferObjects\FolderData;
use Filament\Navigation\NavigationItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Domain\Folder\Models\Folder as FolderModel;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;

class Quizzes extends Document
{
    protected static ?int $navigationSort = 14;

    protected static bool $shouldRegisterNavigation = true;

    public ?int $folder_id = null;

    protected ?string $heading = 'Quizzes';

    protected static ?string $navigationLabel = 'Quizzes';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Documents';

    protected static ?string $slug = '/quizzes/{folderId?}';

    public function mount(string $folderId = null): void
    {
        $this->folder_id = $folderId == null ? 13 : intval($folderId);

        $this->fetchData();
    }

    public function getFileLabel()
    {
        return "Quiz";
    }

    public function getDocumentLabel()
    {
        return "New " . $this->getFileLabel();
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn (): bool => request()->routeIs("filament.admin.pages..quizzes.*"))
                ->sort(static::getNavigationSort())
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->url(static::getNavigationUrl()),
        ];
    }

    public function fetchData(): void
    {
        $folderQueryData = $this->getFolders();
        $this->folderCount = $folderQueryData->lastPage();
        $this->folderList = new Collection($folderQueryData->items());

        if (count($this->folderList) <= 16) {
            $assetQueryData = $this->getAssets();
            if (count($assetQueryData->items()) > 0) {
                $this->assetCount = $assetQueryData->lastPage();
            } else {
                $this->assetCount = 0;
            }
            $this->assetList = new Collection($assetQueryData->items());
        }
    }

    /** @return LengthAwarePaginator<FolderModel> */
    public function getFolders(int $page = 1): LengthAwarePaginator
    {
        $result = FolderModel::with(['descendants'])->where(function ($query) {
            if ($this->folder_id) {
                $query->where('folder_id', $this->folder_id);
            } else {
                $query->whereNull('folder_id');
            }
        })
            ->orderBy('name')
            ->paginate(32, page: $page);

        return $result;
    }

    public function createFolder(array $data): void
    {
        if (is_null($data['name'])) {
            return;
        }

        $folder = FolderModel::find($this->folder_id);

        $path = '';
        if (!is_null($folder) && !empty($folder->path)) {
            $path = $folder->path;
        }

        $data['author_id'] = auth()->user()->id;
        $data['slug'] = Str::slug($data['name']);
        $data['path'] = $path . '/' . Str::slug($data['name']);
        $data['folder_id'] = $this->folder_id;
        $data['is_private'] = true;

        $result = app(CreateFolderAction::class)
            ->execute(FolderData::fromArray($data));

        if ($result instanceof FolderModel) {
            $this->refreshingFolder('create', $result);
            Notification::make()
                ->title('Folder Created')
                ->success()
                ->send();
        }
    }

    //right actions
    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('new-folder')
                    ->label('New Folder')
                    ->modalHeading('New Folder')
                    ->modalWidth('md')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label(''),
                        Forms\Components\Toggle::make('is_private')
                            ->disabled()
                            ->label('Private')
                            ->default(true),
                    ])
                    ->modalFooterActionsAlignment('right')
                    ->action(function (array $data) {
                        $this->createFolder($data);
                    }),
                Action::make('new-asset')
                    ->label($this->getDocumentLabel())
                    ->action(function () {
                        $folder = FolderModel::find($this->folder_id);

                        return redirect()->route(
                            'filament.admin.resources.documents.create',
                            ['ownerRecord' => $folder, 'label' => $this->getFileLabel()]
                        );
                    }),
            ])
                ->view('filament.components.custom-action-group.index')
                ->label('Create New'),
        ];
    }
}
