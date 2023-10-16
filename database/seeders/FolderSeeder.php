<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Folder\Models\Folder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FolderSeeder extends Seeder
{
    public function run(): void
    {
        $folderData = [
            // Accreditation 1
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Accreditation',
                'slug' => 'accreditation',
                'path' => '/accreditation',
                'is_private' => false,
            ],
            // Syllabus 2 
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Syllabus',
                'slug' => 'syllabus',
                'path' => '/syllabus',
                'is_private' => false,
            ],
            // Schedule 3 
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Schedule',
                'slug' => 'schedule',
                'path' => '/schedule',
                'is_private' => false,
            ],
            // Exams 4
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Exams',
                'slug' => 'exams',
                'path' => '/exams',
                'is_private' => false,
            ],
            // Rubrics 4
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Rubrics',
                'slug' => 'rubrics',
                'path' => '/rubrics',
                'is_private' => false,
            ],
        ];

        foreach ($folderData as $folders) {
            Folder::create($folders);
        }
    }
}
