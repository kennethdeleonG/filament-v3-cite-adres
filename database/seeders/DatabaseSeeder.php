<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Domain\Faculty\Models\Faculty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Faculty::create([
            'name' => 'John Doe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@bulsu.edu.ph',
            'password' => Hash::make('12341234'),
            'address' => 'Bustos Bulacan',
            'gender' => 'Male',
            'mobile' => null,
            'designation' => 'Web Development Instructor',
        ]);

        $this->call([
            FolderSeeder::class,
        ]);
    }
}
