<?php

namespace App\Filament\Admin\Pages;

use App\Domain\Asset\Models\Asset;
use App\Domain\Folder\Models\Folder;
use App\Support\Concerns\CustomFormatHelper;
use App\Support\Concerns\CustomPagination;
use App\Support\Enums\UserType;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Filament\Notifications\Notification;

class RecycleBin extends Page
{
    use CustomFormatHelper;
    use CustomPagination;

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationIcon = 'heroicon-o-trash';

    protected static string $view = 'filament.pages.admin.recycle-bin';

    /** @var array */
    private $extensions = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'tif', 'tiff'],
        'doc' => ['doc', 'docx', 'pdf', 'txt', 'odt', 'rtf', 'wpd', 'wps'],
        'spreadsheet' => ['xls', 'xlsx', 'ods', 'csv'],
        'presentation' => ['ppt', 'pptx', 'odp'],
        'archive' => ['zip', 'rar', 'tar', 'gz'],
        'video' => ['mp4', 'avi', 'wmv', 'mov', 'flv', 'm4v', 'mkv', 'webm'],
    ];

    public function mount(): void
    {
        $this->fetchData();
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

    public function getFolderActions(): array
    {
        return [
            [
                'action' => 'restore',
                'label' => 'Restore',
            ],
            [
                'action' => 'purge',
                'label' => 'Purge',
            ],
        ];
    }

    /** @return LengthAwarePaginator<FolderModel> */
    public function getFolders(int $page = 1): LengthAwarePaginator
    {
        $result = Folder::onlyTrashed()
            ->where(function ($query) {
                $query->whereNull('author_type')
                    ->where('author_id', auth()->user()->id);
            })
            ->orderBy('deleted_at')
            ->paginate(32, page: $page);

        return $result;
    }

    public function getFolderSize(int $folder_id): string
    {
        $folder = $this->folderList->where('id', $folder_id)->first();

        $folderIds = $folder->descendants->pluck('id')->toArray();

        return $this->convertedAssetSize(
            (int) Asset::whereIn('folder_id', $folderIds)
                ->orWhere('folder_id', $folder_id)
                ->sum('size')
        );
    }

    /** @return LengthAwarePaginator<Asset> */
    public function getAssets(int $page = 1): LengthAwarePaginator
    {
        $result = Asset::onlyTrashed()
            ->where(function ($query) {
                $query->whereNull('author_type')
                    ->where('author_id', auth()->user()->id);
            })
            ->orderBy('name')
            ->paginate(32, page: $page);

        return $result;
    }

    public function getAssetActions(): array
    {
        return [
            [
                'action' => 'restore',
                'label' => 'Restore',
            ],
            [
                'action' => 'purge',
                'label' => 'Purge',
            ],
        ];
    }

    public function getAssetIconImage(Asset $asset): string
    {
        foreach ($this->extensions as $type => $typeExtensions) {
            if (in_array($asset->file_type, $typeExtensions)) {
                return match ($type) {
                    'image' => 'image.svg',
                    'doc' => 'doc.svg',
                    'spreadsheet' => 'xls.svg',
                    'presentation' => 'ppt.svg',
                    'archive' => 'archive.svg',
                    'video' => 'video.svg',
                    default => 'file.svg'
                };
            }
        }

        return 'file.svg';
    }

    public function mountActionFolder(string $action, int $folderId)
    {
        $folder = Folder::withTrashed()->find($folderId);

        if ($folder) {
            switch ($action) {
                case 'restore': {
                        $folder->restore();

                        Notification::make()
                            ->title('Restored Successfully')
                            ->success()
                            ->send();

                        $this->fetchData();
                        break;
                    }
                case 'purge': {
                        $folder->forceDelete();

                        Notification::make()
                            ->title('Purged Successfully')
                            ->success()
                            ->send();

                        $this->fetchData();
                        break;
                    }
                default: {
                        break;
                    }
            }
        }

        return null;
    }

    public function mountActionAsset(string $action, int $assetId)
    {
        $asset = Asset::withTrashed()->find($assetId);

        if ($asset) {
            switch ($action) {
                case 'restore': {
                        $asset->restore();

                        Notification::make()
                            ->title('Restored Successfully')
                            ->success()
                            ->send();

                        $this->fetchData();
                        break;
                    }
                case 'purge': {
                        $asset->forceDelete();

                        Notification::make()
                            ->title('Purged Successfully')
                            ->success()
                            ->send();

                        $this->fetchData();
                        break;
                    }
                default: {
                        break;
                    }
            }
        }

        return null;
    }
}
