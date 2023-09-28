<?php

namespace App\Filament\Admin\Resources\FacultyResource\Pages;

use App\Domain\Faculty\Actions\CreateFacultyAction;
use App\Domain\Faculty\DataTransferObjects\FacultyData;
use App\Filament\Admin\Resources\FacultyResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateFaculty extends CreateRecord
{
    protected static string $resource = FacultyResource::class;

    public static function canCreateAnother(): bool
    {
        return false;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(
            fn () => app(CreateFacultyAction::class)
                ->execute(FacultyData::fromArray($data))
        );
    }
}
