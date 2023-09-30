<?php

namespace App\Filament\Faculty\Resources\DocumentResource\Pages;

use App\Filament\Faculty\Resources\DocumentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssets extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
