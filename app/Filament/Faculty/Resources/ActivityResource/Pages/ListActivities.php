<?php

namespace App\Filament\Faculty\Resources\ActivityResource\Pages;

use App\Filament\Faculty\Resources\ActivityResource;
use Filament\Resources\Pages\ListRecords;

class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return config('filament-spatie-laravel-activitylog.paginate') ?? parent::getTableRecordsPerPageSelectOptions();
    }
}
