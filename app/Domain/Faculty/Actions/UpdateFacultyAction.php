<?php

declare(strict_types=1);

namespace App\Domain\Faculty\Actions;

use App\Domain\Faculty\DataTransferObjects\FacultyData;
use App\Domain\Faculty\Models\Faculty;

class UpdateFacultyAction
{
    public function execute(Faculty $record, FacultyData $facultyData): Faculty
    {
        $record->update([
            'name' => $facultyData->first_name . ' ' . $facultyData->last_name,
            'first_name' => $facultyData->first_name,
            'last_name' => $facultyData->last_name,
            'address' => $facultyData->address,
            'gender' => $facultyData->gender,
            'mobile' => $facultyData->mobile,
            'designation' => $facultyData->designation,
            'password' => $facultyData->password
        ]);

        return $record;
    }
}
