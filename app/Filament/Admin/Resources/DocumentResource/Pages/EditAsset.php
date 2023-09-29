<?php

namespace App\Filament\Admin\Resources\DocumentResource\Pages;

use App\Filament\Admin\Resources\DocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;

class EditAsset extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    public static string | Alignment $formActionsAlignment = Alignment::Right;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
