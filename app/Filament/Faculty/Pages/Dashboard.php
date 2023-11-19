<?php

namespace App\Filament\Faculty\Pages;

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Asset\Actions\DownloadSingleFileAction;
use App\Domain\Asset\Models\Asset;
use App\Domain\Faculty\Models\Faculty;
use App\Domain\Folder\DataTransferObjects\DownloadData;
use App\Domain\Folder\Models\Folder;
use App\Filament\Faculty\Widgets\StatsOverview;
use App\Livewire\AssetModal;
use App\Support\Concerns\AssetTrait;
use App\Support\Concerns\CustomFormatHelper;
use App\Support\Concerns\FolderTrait;
use App\Support\Enums\UserType;
use Carbon\Carbon;
use DateTimeZone;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    use FolderTrait;
    use AssetTrait;
    use CustomFormatHelper;

    protected ?string $heading = 'Dashboard';

    protected static string $view = 'filament.pages.faculty.dashboard';

    public mixed $assetList;

    public mixed $announcementList;

    public function mount(): void
    {
        $this->fetchData();
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            StatsOverview::class
        ];
    }

    private function fetchData(): void
    {
        $assetQueryData = $this->getAssets();
        $this->assetList = new Collection($assetQueryData->items());

        $this->announcementList = $this->getAnnouncements();
    }

    public function getAnnouncements()
    {
        $result = Announcement::orderBy('created_at', 'desc')->get();

        return $result;
    }

    public function dateFrom(Carbon $date): string
    {
        $carbonDate = Carbon::parse($date);

        $userTimezone = 'Asia/Manila';
        if (!empty($userTimezone) && in_array($userTimezone, DateTimeZone::listIdentifiers())) {
            $carbonDate->setTimezone($userTimezone);
        }

        return $carbonDate->diffForHumans();
    }

    /** @return LengthAwarePaginator<Asset> */
    public function getAssets(int $page = 1): LengthAwarePaginator
    {
        $result = Asset::with('folder')
            ->where(function ($query) {
                $query->where('author_type', UserType::FACULTY->value)
                    ->where('author_id', auth()->user()->id);
            })
            ->orWhere(function ($query) {
                $query->where('is_private', false);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5, page: $page);

        return $result;
    }

    public function mountActionAsset(string $action, int $assetId)
    {
        $asset = Asset::find($assetId);

        if ($asset) {
            $result = null;

            switch ($action) {
                case 'open':
                    $result = redirect(route(
                        'filament.faculty.resources.documents.edit',
                        ['record' => $asset, 'ownerRecord' => $asset->folder ?? null]
                    ));
                    break;

                case 'download':
                    $result = app(DownloadSingleFileAction::class)->execute(
                        $asset,
                        DownloadData::fromArray([
                            'files' => [$asset->file],
                            'user_type' => 'faculty',
                            'admin_id' => auth()->user()?->id,
                            'asset_type' => 'asset',
                            'asset_id' => $asset->id,
                        ])
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
                    $result = redirect(route(
                        'filament.faculty.resources.documents.edit',
                        ['record' => $asset, 'ownerRecord' => $asset->folder]
                    ));
                    break;

                case 'move-to':
                    $this->dispatch('moveAssetToFolder', $asset, null)->to(AssetModal::class);
                    break;

                case 'show-history':
                    $result = redirect(route('filament.faculty.pages..history.{subjectType?}.{subjectId?}', [
                        'subjectType' => 'assets',
                        'subjectId' => $asset->id,
                    ]));
                    break;
                default:
                    break;
            }

            return $result;
        }

        return null;
    }

    public function getAssetActions(): array
    {
        return [
            [
                'action' => 'open',
                'label' => 'Open',
            ],
            [
                'action' => 'download',
                'label' => 'Download',
            ],
            [
                'action' => 'delete',
                'label' => 'Delete',
            ],
            [
                'action' => 'edit',
                'label' => 'Edit',
            ],
            [
                'action' => 'show-history',
                'label' => 'Show History',
            ],
        ];
    }

    public function getAuthor(Folder|Asset $model)
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
