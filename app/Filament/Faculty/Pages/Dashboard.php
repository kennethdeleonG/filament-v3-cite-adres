<?php

namespace App\Filament\Faculty\Pages;

use App\Domain\Asset\Actions\DownloadSingleFileAction;
use App\Domain\Asset\Models\Asset;
use App\Domain\Folder\DataTransferObjects\DownloadData;
use App\Filament\Faculty\Widgets\StatsOverview;
use App\Support\Concerns\AssetTrait;
use App\Support\Concerns\CustomFormatHelper;
use App\Support\Concerns\FolderTrait;
use App\Support\Enums\UserType;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class Dashboard extends BaseDashboard
{
    use FolderTrait;
    use AssetTrait;
    use CustomFormatHelper;

    protected ?string $heading = 'Dashboard';

    protected static string $view = 'filament.pages.admin.dashboard';

    public mixed $assetList;

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
        // $asset = Asset::find($assetId);

        // if ($asset) {
        //     return match ($action) {
        //         'open' => redirect(route('filament.admin.resources.documents.edit', ['record' => $asset, 'ownerRecord' => $asset->folder ?? null])),
        //         'download' => app(DownloadSingleFileAction::class)->execute(
        //             $asset,
        //             DownloadData::fromArray(
        //                 [
        //                     'files' => [$asset->file],
        //                     'user_type' => 'admin',
        //                     'admin_id' => auth()->user()?->id,
        //                     'asset_type' => 'asset',
        //                     'asset_id' => $asset->id,
        //                 ]
        //             )
        //         ),
        //         'delete' => $this->dispatch('deleteAsset', $asset)->to(AssetModal::class),
        //         'edit' => redirect(route('filament.admin.resources.documents.edit', ['record' => $asset, 'ownerRecord' => $asset->folder])),
        //         'move-to' => $this->dispatch('moveAssetToFolder', $asset)->to(AssetModal::class),
        //         'show-history' => redirect(route('filament.admin.pages..documents.history.{subjectType?}.{subjectId?}', ['subjectType' => 'assets', 'subjectId' => $asset->id])),
        //         default => null
        //     };
        // }

        // return null;
    }
}
