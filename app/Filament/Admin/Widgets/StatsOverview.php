<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Faculties', '192.1k'),
            Stat::make('Documents', '21%'),
            Stat::make('Recycle Bin', '3:12'),
        ];
    }
}
