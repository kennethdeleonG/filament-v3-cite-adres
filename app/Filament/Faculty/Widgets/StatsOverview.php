<?php

namespace App\Filament\Faculty\Widgets;

use App\Domain\Asset\Models\Asset;
use App\Domain\Faculty\Models\Faculty;
use App\Support\Enums\UserType;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $facultyCount = Faculty::whereNull('deleted_at')->count();
        $assetCount = Asset::whereNull('deleted_at')
            ->where(function ($query) {
                $query->where('author_type', UserType::FACULTY->value)
                    ->where('author_id', auth()->user()->id);
            })
            ->orWhere(function ($query) {
                $query->where('is_private', false);
            })
            ->count();
        $deletedAssetCount = Asset::onlyTrashed()
            ->where(function ($query) {
                $query->where('author_type', UserType::FACULTY->value)
                    ->where('author_id', auth()->user()->id);
            })
            ->count();


        return [
            Stat::make('Faculty', $facultyCount),
            Stat::make('Documents', $assetCount),
            Stat::make('Recycle Bin', $deletedAssetCount),
        ];
    }
}
