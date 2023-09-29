<?php

declare(strict_types=1);

namespace App\Support\Concerns;

trait FolderTrait
{
    public function getFolderActions(): array
    {
        return [
            [
                'action' => 'open',
                'label' => 'Open',
            ],
            [
                'action' => 'download',
                'label' => 'Download',
            ],
            [
                'action' => 'delete',
                'label' => 'Delete',
            ],
            [
                'action' => 'edit',
                'label' => 'Edit',
            ],
            [
                'action' => 'move-to',
                'label' => 'Move to',
            ],
            [
                'action' => 'show-history',
                'label' => 'Show History',
            ],
        ];
    }
}
