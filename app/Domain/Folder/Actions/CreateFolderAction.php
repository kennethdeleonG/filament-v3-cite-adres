<?php

declare(strict_types=1);

namespace App\Domain\Folder\Actions;

use App\Domain\Folder\Models\Folder;
use App\Domain\Folder\DataTransferObjects\FolderData;
use Illuminate\Support\Str;

class CreateFolderAction
{
    public function execute(FolderData $folderData): Folder
    {
        if (!is_null($folderData->folder_id)) {
            $folder = Folder::create([
                'uuid' => Str::uuid()->toString(),
                'author_id' => $folderData->author_id,
                'author_type' => $folderData->author_type,
                'folder_id' => $folderData->folder_id,
                'name' => $folderData->name,
                'slug' => $folderData->slug,
                'path' => $folderData->path,
                'is_private' => $folderData->is_private,
            ]);

            $parent = Folder::find($folderData->folder_id);

            activity()->withoutLogs(function () use ($parent, $folder) {
                if (!is_null($parent)) {
                    $parent->appendNode($folder);
                }
            });

            return $folder;
        } else {
            $folder = Folder::create([
                'uuid' => Str::uuid()->toString(),
                'author_id' => $folderData->author_id,
                'author_type' => $folderData->author_type,
                'name' => $folderData->name,
                'slug' => $folderData->slug,
                'path' => $folderData->path,
                'is_private' => $folderData->is_private,
            ]);

            activity()->withoutLogs(function () use ($folder) {
                $folder->saveAsRoot();
            });

            return $folder;
        }
    }
}
