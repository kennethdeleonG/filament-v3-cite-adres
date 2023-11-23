<?php

namespace App\Filament\Admin\Resources\AdminResource\Pages;

use App\Domain\Faculty\Enums\FacultyStatuses;
use App\Filament\Admin\Resources\AdminResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAdmin extends EditRecord
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        $user = $this->record;

        $blockAction = Actions\DeleteAction::make('block')
            ->label('Block')
            ->requiresConfirmation()
            ->modalHeading('Block Admin')
            ->action(function () use ($user) {

                $user->update([
                    'status' => FacultyStatuses::BLOCKED->value
                ]);

                Notification::make()
                    ->title('Blocked Successfully')
                    ->success()
                    ->send();
            });

        $unBlockAction = Actions\Action::make('unblock')
            ->label('Unblock')
            ->requiresConfirmation()
            ->modalHeading('Unblock Admin')
            ->action(function () use ($user) {

                $user->update([
                    'status' => FacultyStatuses::ACTIVE->value
                ]);

                Notification::make()
                    ->title('Unblocked Successfully')
                    ->success()
                    ->send();
            });

        return [
            $user->status == FacultyStatuses::ACTIVE ? $blockAction : $unBlockAction,
            Actions\DeleteAction::make(),
        ];
    }
}
