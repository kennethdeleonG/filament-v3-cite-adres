<?php

declare(strict_types=1);

namespace App\Domain\Folder\DataTransferObjects;

class FolderData
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly string $path,
        public readonly ?int $author_id = null,
        public readonly ?string $author_type = null,
        public readonly ?bool $is_private = false,
        public readonly ?int $folder_id = null,
        public readonly ?int $id = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
            path: $data['path'],
            author_id: $data['author_id'] ?? null,
            author_type: $data['author_type'] ?? null,
            is_private: $data['is_private'] ?? false,
            folder_id: $data['folder_id'] ?? null,
            id: $data['id'] ?? null,
        );
    }
}
