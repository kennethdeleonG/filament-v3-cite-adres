<?php

namespace App\Filament\Admin\Widgets;

use App\Domain\Asset\Models\Asset;
use App\Domain\Faculty\Models\Faculty;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $facultyCount = Faculty::whereNull('deleted_at')->count();
        $assetCount = Asset::whereNull('deleted_at')->count();
        $deletedAssetCount = Asset::onlyTrashed()->count();


        return [
            Stat::make('Faculties', $facultyCount),
            Stat::make('Assets', $assetCount),
            Stat::make('Recycle Bin', $deletedAssetCount),
        ];
    }
}
