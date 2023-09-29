<?php

declare(strict_types=1);

namespace App\Domain\Asset\Actions;

use App\Domain\Asset\Models\Asset;

class MoveAssetAction
{
    public function execute(?Asset $asset = null, int|null $parentFolderId, string $oldPath, string $newPath): Asset|null
    {
        if (!is_null($asset)) {
            $folderIdToLog = $asset->folder_id;

            activity()->withoutLogs(function () use ($asset, $parentFolderId) {
                $asset->update(
                    [
                        'folder_id' => $parentFolderId,
                    ]
                );

                $record = Asset::with('folder')->find($asset->id);

                if ($record) {
                    app(MoveAssetToNewLocationAction::class)->execute($record);
                }

                return $asset;
            });

            activity()
                ->causedBy(auth()->user())
                ->performedOn($asset)
                ->withProperties([
                    'old' => [
                        'folder_id' => $folderIdToLog,
                        'path' => $oldPath,
                    ], 'attributes' => [
                        'folder_id' => $parentFolderId,
                        'path' => $newPath,
                    ],
                ])
                ->event('moved')
                ->log('moved');
        }

        return $asset;
    }
}
