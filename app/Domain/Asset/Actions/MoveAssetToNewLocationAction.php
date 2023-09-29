<?php

declare(strict_types=1);

namespace App\Domain\Asset\Actions;

use App\Domain\Asset\Models\Asset;
use Illuminate\Support\Facades\Storage;

class MoveAssetToNewLocationAction
{
    public function execute(Asset $asset): void
    {
        if (
            !is_null($asset->file) &&
            Storage::disk('s3')->exists($asset->file)
        ) {
            $to = $asset->folder?->path . '/' . basename($asset->file);

            Storage::disk('s3')->move($asset->file, $to);

            $asset->path = $asset->folder?->path;
            $asset->file = $to;

            $asset->save();
        }
    }
}
