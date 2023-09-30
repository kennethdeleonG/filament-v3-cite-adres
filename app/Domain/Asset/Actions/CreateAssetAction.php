<?php

declare(strict_types=1);

namespace App\Domain\Asset\Actions;

use App\Domain\Asset\DataTransferObjects\AssetData;
use App\Domain\Asset\Models\Asset;

class CreateAssetAction
{
    public function execute(AssetData $asset): Asset
    {
        $model = Asset::create([
            'author_id' => $asset->author_id,
            'author_type' => $asset->author_type,
            'folder_id' => $asset->folder_id,
            'name' => $asset->name,
            'path' => $asset->path,
            'file' => $asset->file,
            'technical_information' => $asset->technical_information,
            'size' => $asset->size,
            'file_type' => $asset->file_type,
            'is_private' => $asset->is_private,
        ]);

        return $model;
    }
}
