<?php

declare(strict_types=1);

namespace App\Domain\Asset\Actions;

use App\Domain\Asset\Models\Asset;

class DeleteAssetAction
{
    public function execute(Asset $asset): ?bool
    {
        return $asset->delete();
    }
}
