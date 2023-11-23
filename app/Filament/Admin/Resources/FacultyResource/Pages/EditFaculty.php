<?php

namespace App\Filament\Admin\Resources\FacultyResource\Pages;

use App\Domain\Faculty\Actions\UpdateFacultyAction;
use App\Domain\Faculty\DataTransferObjects\FacultyData;
use App\Domain\Faculty\Enums\FacultyStatuses;
use App\Domain\Faculty\Models\Faculty;
use App\Filament\Admin\Resources\FacultyResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Support\Enums\Alignment;

class EditFaculty extends EditRecord
{
    protected static string $resource = FacultyResource::class;

    public static string | Alignment $formActionsAlignment = Alignment::Right;


    protected function getHeaderActions(): array
    {
        $faculty = $this->record;

        $blockAction = Actions\DeleteAction::make('block')
            ->label('Block')
            ->requiresConfirmation()
            ->modalHeading('Block Faculty')
            ->action(function () use ($faculty) {

                $faculty->update([
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
            ->modalHeading('Unblock Faculty')
            ->action(function () use ($faculty) {

                $faculty->update([
                    'status' => FacultyStatuses::ACTIVE->value
                ]);

                Notification::make()
                    ->title('Unblocked Successfully')
                    ->success()
                    ->send();
            });

        return [
            $faculty->status == FacultyStatuses::ACTIVE ? $blockAction : $unBlockAction,
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(
            fn () => app(UpdateFacultyAction::class)
                ->execute($record, FacultyData::fromArray($data))
        );
    }
}
