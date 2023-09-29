<?php

namespace App\Filament\Admin\Resources\AssetResource\Pages;

use App\Filament\Admin\Resources\AssetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;

class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    public static string | Alignment $formActionsAlignment = Alignment::Right;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
