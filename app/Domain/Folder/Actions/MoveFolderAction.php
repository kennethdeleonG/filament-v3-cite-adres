<?php

declare(strict_types=1);

namespace App\Domain\Folder\Actions;

use App\Domain\Folder\DataTransferObjects\FolderData;
use App\Domain\Folder\Models\Folder;

class MoveFolderAction
{
    public function execute(Folder $folder, FolderData $folderData): Folder
    {
        $folderIdToLog = $folder->folder_id;
        $folderPathToLog = $folder->path;

        activity()->withoutLogs(function () use ($folder, $folderData) {
            if (!is_null($folderData->folder_id)) {

                $parentFolder = Folder::find($folderData->folder_id);

                $parentFolderIsPrivate = !is_null($parentFolder) ? $parentFolder->is_private : true;

                if (!$parentFolderIsPrivate) {

                    $folder->update(
                        [
                            'name' => $folderData->name,
                            'slug' => $folderData->slug,
                            'folder_id' => $folderData->folder_id,
                            'path' => $folderData->path,
                            'is_private' => false,

                        ]
                    );
                } else {

                    $folder->update(
                        [
                            'name' => $folderData->name,
                            'slug' => $folderData->slug,
                            'folder_id' => $folderData->folder_id,
                            'path' => $folderData->path,

                        ]
                    );
                }
            } else {
                $folder->update(
                    [
                        'name' => $folderData->name,
                        'slug' => $folderData->slug,
                        'folder_id' => $folderData->folder_id,
                        'path' => $folderData->path,
                    ]
                );

                $folder->saveAsRoot();
            }

            Folder::fixTree();
        });

        activity()
            ->causedBy(auth()->user())
            ->performedOn($folder)
            ->withProperties([
                'old' => [
                    'folder_id' => $folderIdToLog,
                    'path' => $folderPathToLog,
                ], 'attributes' => [
                    'folder_id' => $folderData->folder_id,
                    'path' => $folderData->path,
                ],
            ])
            ->event('moved')
            ->log('moved');

        return $folder;
    }
}
