<?php

namespace App\Filament\Admin\Resources\AnnouncementResource\Pages;

use App\Domain\Announcement\Actions\CreateAnnouncementAction;
use App\Domain\Announcement\DataTransferObjects\AnnouncementData;
use App\Filament\Admin\Resources\AnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

    public static string | Alignment $formActionsAlignment = Alignment::Right;

    public static function canCreateAnother(): bool
    {
        return false;
    }


    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(
            fn () => app(CreateAnnouncementAction::class)
                ->execute(AnnouncementData::fromArray($data))
        );
    }
}
