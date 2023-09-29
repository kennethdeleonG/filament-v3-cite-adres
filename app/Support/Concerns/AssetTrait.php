<?php

declare(strict_types=1);

namespace App\Support\Concerns;

use App\Domain\Asset\Models\Asset;

trait AssetTrait
{
    /** @var array */
    private $extensions = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'tif', 'tiff'],
        'doc' => ['doc', 'docx', 'pdf', 'txt', 'odt', 'rtf', 'wpd', 'wps'],
        'spreadsheet' => ['xls', 'xlsx', 'ods', 'csv'],
        'presentation' => ['ppt', 'pptx', 'odp'],
        'archive' => ['zip', 'rar', 'tar', 'gz'],
        'video' => ['mp4', 'avi', 'wmv', 'mov', 'flv', 'm4v', 'mkv', 'webm'],
    ];

    public function getAssetActions(): array
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

    public function getAssetIconImage(Asset $asset): string
    {
        foreach ($this->extensions as $type => $typeExtensions) {
            if (in_array($asset->file_type, $typeExtensions)) {
                return match ($type) {
                    'image' => 'image.svg',
                    'doc' => 'doc.svg',
                    'spreadsheet' => 'xls.svg',
                    'presentation' => 'ppt.svg',
                    'archive' => 'archive.svg',
                    'video' => 'video.svg',
                    default => 'file.svg'
                };
            }
        }

        return 'file.svg';
    }
}
