<?php

namespace App\Filament\Admin\Resources\AdminResource\Pages;

use App\Filament\Admin\Resources\AdminResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Alignment;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;

    public static string | Alignment $formActionsAlignment = Alignment::Right;

    public static function canCreateAnother(): bool
    {
        return false;
    }
}
