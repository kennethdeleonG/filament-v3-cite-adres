<?php

namespace App\Filament\Faculty\Pages;

use App\Domain\Asset\Actions\DownloadSingleFileAction;
use App\Domain\Asset\Models\Asset;
use App\Domain\Faculty\Models\Faculty;
use App\Domain\Folder\Models\Folder as FolderModel;
use App\Domain\Folder\Actions\CreateFolderAction;
use App\Domain\Folder\Actions\DownloadFolderAction;
use App\Domain\Folder\DataTransferObjects\DownloadData;
use App\Domain\Folder\DataTransferObjects\FolderData;
use App\Livewire\AssetModal;
use App\Livewire\FolderModal;
use App\Support\Concerns\AssetTrait;
use App\Support\Concerns\CustomFormatHelper;
use App\Support\Concerns\CustomPagination;
use App\Support\Concerns\FolderTrait;
use App\Support\Enums\UserType;
use Filament\Pages\Page;
use Filament\Forms;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Throwable;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class DocumentManagement extends Page
{
    use FolderTrait;
    use AssetTrait;
    use CustomPagination;
    use CustomFormatHelper;

    public ?int $folder_id = null;

    protected ?string $heading = 'Documents';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Documents';

    protected static string $view = 'filament.pages.faculty.document';

    protected static ?string $slug = '/documents/{folderId?}';

    /** @var array */
    protected $listeners = [
        'refreshPage' => 'refreshingFolder',
        'folderIdUpdated' => 'getFolderWithId',
    ];

    public function mount(string $folderId = null): void
    {
        $this->folder_id = intval($folderId);

        $this->fetchData();
    }

    //custom header
    public function getHeader(): ?View
    {
        return view('filament.components.custom-header');
    }

    public function getHeading(): string | Htmlable
    {
        if ($this->folder_id) {
            $folder = FolderModel::find($this->folder_id);
            return $folder->name;
        }

        return "Documents";
    }

    //for breadcrumbs in body
    public function getBreadcrumbsMenu(): array
    {
        if (is_null($this->folder_id) || !empty($this->search) || !empty($this->filterBy)) {
            return [];
        }

        $parentFolder = FolderModel::find($this->folder_id);

        if (is_null($parentFolder) || is_null($parentFolder->ancestors)) {
            return [];
        }

        $folderAncestors = $parentFolder->ancestors->toArray();

        $crumbs = [];
        foreach ($folderAncestors as $node) {
            $truncatedName = Str::limit($node['name'], 20, '...');
            $tooltip = $node['name'];
            $crumbs[self::getUrl() . '/' . $node['id']] = new HtmlString('<span title="' . $tooltip . '">' . $truncatedName . '</span>');
        }
        $truncatedParentFolderName = Str::limit($parentFolder->name, 20, '...');
        $crumbs[self::getUrl() . '/' . $parentFolder->id] = new HtmlString('<span title="' . $parentFolder->name . '">' . $truncatedParentFolderName . '</span>');

        // $rootMenu = [
        //     self::getUrl() => trans('Root Directory'),
        // ];

        // $mergedCrumbs = array_merge($rootMenu, $crumbs);

        return $crumbs;
    }

    public function getBreadcrumbs(): array
    {
        return [
            self::getUrl() => trans('Document'),
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.faculty.resources.documents.*')
                    || request()->routeIs("filament.faculty.pages..documents.*"))
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

    /** @return LengthAwarePaginator<Asset> */
    public function getAssets(int $page = 1): LengthAwarePaginator
    {

        $result = Asset::with('folder')->where(function ($query) {
            if ($this->folder_id) {
                $query->where('folder_id', $this->folder_id);
            } else {
                $query->whereNull('folder_id');
            }
        })
            ->where(function ($query) {
                $query->where('author_type', UserType::FACULTY->value)
                    ->where('author_id', auth()->user()->id);
            })
            ->orWhere(function ($query) {
                $query->where('is_private', false)->where('folder_id', $this->folder_id);
            })
            ->orderBy('name')
            ->paginate(32, page: $page);

        return $result;
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
        })->orderBy('name')
            ->paginate(32, page: $page);

        return $result;
    }

    //right actions
    protected function getHeaderActions(): array
    {

        if ($this->folder_id) {
            return [
                ActionGroup::make([
                    Action::make('new-folder')
                        ->label('New Folder')
                        ->modalHeading('New Folder')
                        ->modalWidth('md')
                        ->form([
                            Forms\Components\TextInput::make('name')
                                ->label(''),
                            Forms\Components\Toggle::make('is_private')->label('Private')
                                ->default(false)->hidden(),
                        ])
                        ->modalFooterActionsAlignment('right')
                        ->action(function (array $data) {
                            $this->createFolder($data);
                        }),
                    Action::make('new-asset')
                        ->label('New File')
                        ->action(function () {
                            $folder = FolderModel::find($this->folder_id);

                            return redirect()->route(
                                'filament.faculty.resources.documents.create',
                                ['ownerRecord' => $folder, 'label' => $this->getFileLabel()]
                            );
                        }),
                ])
                    ->view('filament.components.custom-action-group.index')
                    ->label('Create New'),
            ];
        }

        return [];
    }

    public function getFileLabel()
    {
        return $this->getHeading();
    }

    public function getDocumentLabel()
    {
        return "New " . $this->getFileLabel();
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
        $data['author_type'] = UserType::FACULTY->value;
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

    public function refreshingFolder(string $action, mixed $record): void
    {
        switch ($action) {
            case 'create': {
                    $this->fetchData();

                    break;
                }
            case 'update': {
                    $this->fetchData();

                    break;
                }
            case 'delete': {
                    $folderToDelete = json_decode($record);

                    $this->folderList = $this->folderList->reject(function ($item) use ($folderToDelete) {
                        return $item->id === $folderToDelete->id;
                    });

                    break;
                }
            case 'move': {
                    $folderToMoved = json_decode($record);

                    $this->folderList = $this->folderList->reject(function ($item) use ($folderToMoved) {
                        return $item->id === $folderToMoved->id;
                    });

                    break;
                }
            case 'delete-asset':
            case 'move-asset': {
                    $assetToDelete = json_decode($record);

                    $this->assetList = $this->assetList->reject(function ($item) use ($assetToDelete) {
                        return $item->id === $assetToDelete->id;
                    });
                }
            default: {
                    break;
                }
        }
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

    public function mountActionFolder(string $action, int $folderId): void
    {
        $folder = $this->folderList->where('id', $folderId)->first();

        if ($folder) {
            switch ($action) {
                case 'open': {
                        $this->getFolderWithId($folder->id);

                        break;
                    }
                case 'download': {
                        if (!Storage::disk('s3')->exists($folder->path)) {
                            Notification::make()
                                ->title("Can't download. This directory is empty.")
                                ->warning()
                                ->send();
                        } else {

                            try {
                                $url = app(DownloadFolderAction::class)->execute(
                                    DownloadData::fromArray(
                                        [
                                            'directories' => [$folder->path],
                                            'files' => [''],
                                            'user_type' => 'admin',
                                            'admin_id' => auth()->user()?->id,
                                            'asset_type' => 'directory',
                                            'asset_id' => $folder->id,
                                        ]
                                    )
                                );

                                redirect($url);
                            } catch (Throwable $th) {

                                Notification::make()
                                    ->title($th->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }

                        break;
                    }
                case 'edit': {
                        $this->dispatch('editFolder', $folder)->to(FolderModal::class);

                        break;
                    }
                case 'delete': {
                        $user = Auth::user();

                        if ($folder->author_type == 'admin') {
                            Notification::make()
                                ->title('You cant delete this folder')
                                ->danger()
                                ->send();
                        } else if ($folder->author_id != $user->id) {
                            Notification::make()
                                ->title('You cant delete this folder')
                                ->danger()
                                ->send();
                        } else {
                            $this->dispatch('deleteFolder', $folder)->to(FolderModal::class);
                        }
                        break;
                    }
                case 'move-to': {
                        $this->dispatch('moveFolder', $folder, $this->folder_id)->to(FolderModal::class);

                        break;
                    }
                case 'show-history': {
                        redirect(route('filament.faculty.pages..history.{subjectType?}.{subjectId?}', ['subjectType' => 'folders', 'subjectId' => $folder->id]));

                        break;
                    }
                default: {
                        break;
                    }
            }
        }
    }


    public function mountActionAsset(string $action, int $assetId)
    {
        $asset = Asset::find($assetId);

        if ($asset) {
            switch ($action) {
                case 'open':
                    redirect(route(
                        'filament.faculty.resources.documents.edit',
                        [
                            'record' => $asset,
                            'ownerRecord' => $asset->folder ?? null,
                            'label' => $this->getFileLabel(),
                        ]
                    ));

                    break;
                case 'download':
                    app(DownloadSingleFileAction::class)->execute(
                        $asset,
                        DownloadData::fromArray(
                            [
                                'files' => [$asset->file],
                                'user_type' => 'faculty',
                                'admin_id' => auth()->user()?->id,
                                'asset_type' => 'asset',
                                'asset_id' => $asset->id,
                            ]
                        )
                    );

                    break;
                case 'delete':
                    $user = Auth::user();
                    if ($asset->author_type == 'admin' || $asset->author_id != $user->id) {
                        Notification::make()
                            ->title('You can\'t delete this file')
                            ->danger()
                            ->send();
                    } else {
                        $this->dispatch('deleteAsset', $asset)->to(AssetModal::class);
                    }
                    break;
                case 'edit':
                    redirect(route(
                        'filament.faculty.resources.documents.edit',
                        [
                            'record' => $asset,
                            'ownerRecord' => $asset->folder,
                            'label' => $this->getFileLabel(),
                        ]
                    ));
                    break;
                case 'move-to':
                    $this->dispatch('moveAssetToFolder', $asset, $this->folder_id)->to(AssetModal::class);
                    break;
                case 'show-history':
                    redirect(route('filament.faculty.pages..history.{subjectType?}.{subjectId?}', ['subjectType' => 'assets', 'subjectId' => $asset->id]));
                    break;
                default:
                    break;
            }
        }

        return null;
    }

    /**
     * Get an instance of the redirector.
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function getFolderWithId(int $folderId)
    {
        return redirect()->to(self::getUrl() . '/' . $folderId);
    }

    public function getAuthor(FolderModel|Asset $model)
    {

        if ($model->author_type == UserType::ADMIN->value) {
            return "Admin";
        } else {
            $faculty = Faculty::find($model->author_id);

            return $faculty->first_name . ' ' . $faculty->last_name;
        }
    }

    public function getRedirectUrl($folderId)
    {
        return self::getUrl() . '/' . $folderId;
    }
}