<?php

declare(strict_types=1);

namespace App\Domain\Folder\Actions;

use App\Domain\Folder\Models\Folder;

class DeleteFolderAction
{
    public function execute(Folder $folder): ?bool
    {
        return $folder->delete();
    }
}
