<?php

namespace App\Filament\Admin\Pages;

use App\Domain\Folder\Actions\CreateFolderAction;
use App\Domain\Folder\DataTransferObjects\FolderData;
use Filament\Navigation\NavigationItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Domain\Folder\Models\Folder as FolderModel;
use App\Support\Enums\UserType;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;

class ReadingMaterials extends Document
{
    protected static ?int $navigationSort = 7;

    protected static bool $shouldRegisterNavigation = true;

    public ?int $folder_id = null;

    protected ?string $heading = 'Reading Materials';

    protected static ?string $navigationLabel = 'Reading Materials';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Documents';

    protected static ?string $slug = '/reading-materials/{folderId?}';

    public function mount(string $folderId = null): void
    {
        $this->folder_id = $folderId == null ? 7 : intval($folderId);

        $this->fetchData();
    }

    public function getDocumentLabel()
    {
        return "New Reading Material";
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn (): bool => request()->routeIs("filament.admin.pages..reading-materials.*"))
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
}
