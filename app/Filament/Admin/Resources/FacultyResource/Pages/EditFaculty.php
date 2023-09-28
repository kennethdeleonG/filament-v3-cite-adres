<?php

namespace App\Filament\Admin\Resources\FacultyResource\Pages;

use App\Domain\Faculty\Actions\UpdateFacultyAction;
use App\Domain\Faculty\DataTransferObjects\FacultyData;
use App\Filament\Admin\Resources\FacultyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditFaculty extends EditRecord
{
    protected static string $resource = FacultyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        dd($data);
        return DB::transaction(
            fn () => app(UpdateFacultyAction::class)
                ->execute($record, FacultyData::fromArray($data))
        );
    }
}