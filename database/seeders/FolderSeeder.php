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
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Lesson Plans',
                'slug' => 'lesson-plans',
                'path' => '/lesson-plans',
                'is_private' => false,
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Lecture Slides',
                'slug' => 'lecture-slides',
                'path' => '/lecture-slides',
                'is_private' => false,
            ]

        ];

        foreach ($folderData as $folders) {
            Folder::create($folders);
        }
    }
}
