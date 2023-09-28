<?php

declare(strict_types=1);

namespace App\Domain\Faculty\Actions;

use App\Domain\Faculty\DataTransferObjects\FacultyData;
use App\Domain\Faculty\Models\Faculty;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class CreateFacultyAction
{
    public function execute(FacultyData $facultyData): Faculty
    {
        $faculty =  Faculty::create([
            'name' => $facultyData->first_name . ' ' . $facultyData->last_name,
            'first_name' => $facultyData->first_name,
            'last_name' => $facultyData->last_name,
            'email' => $facultyData->email,
            'password' => Hash::make($facultyData->password),
            'address' => $facultyData->address,
            'gender' => $facultyData->gender,
            'mobile' => $facultyData->mobile,
            'designation' => $facultyData->designation,
        ]);

        if ($facultyData->image) {

            $faculty->addMediaFromDisk($facultyData->image->getRealPath(), 's3')->toMediaCollection('image');
        }

        event(new Registered($faculty));

        return $faculty;
    }
}
