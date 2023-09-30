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
            // Syllabus
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
            // Lesson Plans
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
            // Lecture Slides
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Lecture Slides',
                'slug' => 'lecture-slides',
                'path' => '/lecture-slides',
                'is_private' => false,
            ],
            // Assignments
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Assignments',
                'slug' => 'assignments',
                'path' => '/assignments',
                'is_private' => false,
            ],
            // Exams & Quizzes
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Exams & Quizzes',
                'slug' => 'exams-quizzes',
                'path' => '/exams-quizzes',
                'is_private' => false,
            ],
            // Lab Exercises
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Lab Exercises',
                'slug' => 'lab-exercises',
                'path' => '/lab-exercises',
                'is_private' => false,
            ],
            // Reading Materials
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Reading Materials',
                'slug' => 'reading-materials',
                'path' => '/reading-materials',
                'is_private' => false,
            ],
            // Code Samples
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Code Samples',
                'slug' => 'code-samples',
                'path' => '/code-samples',
                'is_private' => false,
            ],
            // Reference Guides
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Reference Guides',
                'slug' => 'reference-guides',
                'path' => '/reference-guides',
                'is_private' => false,
            ],
            // Grading Sheets
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Grading Sheets',
                'slug' => 'grading-sheets',
                'path' => '/grading-sheets',
                'is_private' => false,
            ],
            // Rubrics
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
            // Subjects
            [
                'uuid' => Str::uuid()->toString(),
                'author_id' => null,
                'author_type' => null,
                'folder_id' => null,
                'name' => 'Subjects',
                'slug' => 'subjects',
                'path' => '/subjects',
                'is_private' => false,
            ]
        ];

        foreach ($folderData as $folders) {
            Folder::create($folders);
        }
    }
}
