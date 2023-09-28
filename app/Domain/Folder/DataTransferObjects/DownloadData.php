<?php

declare(strict_types=1);

namespace App\Domain\Folder\DataTransferObjects;

class DownloadData
{
    public function __construct(
        public readonly string $user_type,
        public readonly array $directories,
        public readonly int|null $asset_id,
        public readonly string|null $asset_type = null,
        public readonly string|null $ip,
        public readonly string|null $device,
        public readonly int|null $user_id = null,
        public readonly int|null $admin_id = null,
        public readonly ?string $downloadlink = null,
        public readonly array $files = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            directories: $data['directories'] ?? [],
            files: $data['files'] ?? [],
            device: $data['device'] = $data['device'] ?? request()->userAgent(),
            ip: $data['ip'] ?? request()->ip(),
            user_type: $data['user_type'],
            asset_type: $data['asset_type'] ?? null,
            user_id: $data['user_id'] ?? null,
            admin_id: $data['admin_id'] ?? null,
            asset_id: $data['asset_id'] ?? null,
            downloadlink: $data['downloadlink'] ?? null,
        );
    }
}
