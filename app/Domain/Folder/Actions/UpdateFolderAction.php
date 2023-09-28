<?php

declare(strict_types=1);

namespace App\Domain\Folder\Actions;

use App\Domain\Asset\Actions\MoveAssetToNewLocationAction;
use App\Domain\Folder\Models\Folder;
use App\Domain\Folder\DataTransferObjects\FolderData;

class UpdateFolderAction
{
    public function execute(Folder $folder, FolderData $folderData): Folder
    {
        $folder->update(
            [
                'name' => $folderData->name,
                'slug' => $folderData->slug,
                'path' => $folderData->path,
                'is_private' => $folderData->is_private,
            ]
        );

        return $folder;
    }

    public function updateDescendantPaths(Folder $folder, string $newPath): void
    {
        activity()->withoutLogs(function () use ($folder, $newPath) {
            foreach ($folder->assets as $parent_asset) {
                app(MoveAssetToNewLocationAction::class)->execute($parent_asset);
            }

            foreach ($folder->folders as $childFolder) {
                $childPath = $newPath . '/' . $childFolder->slug;
                $childFolder->update(['path' => $childPath]);

                foreach ($childFolder->assets as $asset) {
                    app(MoveAssetToNewLocationAction::class)->execute($asset);
                }
                $this->updateDescendantPaths($childFolder, $childPath);
            }
        });
    }
}
