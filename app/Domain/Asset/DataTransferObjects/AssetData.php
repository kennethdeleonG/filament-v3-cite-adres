<?php

declare(strict_types=1);

namespace App\Domain\Asset\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;

class AssetData implements Arrayable
{
    public function __construct(

        public readonly string $name,
        public readonly string $path,
        public readonly string $file,
        public readonly array $technical_information,
        public readonly ?int $size = null,
        public readonly ?string $file_type = null,
        public readonly ?int $author_id = null,
        public readonly ?string $author_type = null,
        public readonly ?int $folder_id,
        public readonly ?bool $is_private = false,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            path: $data['path'],
            file: $data['file'],
            technical_information: $data['technical_information'] ?? [],
            size: $data['size'] ? intval($data['size']) : null,
            file_type: $data['file_type'] ?? null,
            author_id: $data['author_id'] ?? null,
            author_type: $data['author_type'] ?? null,
            folder_id: $data['folder_id'] ?? null,
            is_private: $data['is_private'] ?? false,
        );
    }

    /** @return array<string, mixed> */
    public function toArray()
    {
        return (array) $this;
    }
}
