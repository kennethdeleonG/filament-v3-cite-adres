<?php

declare(strict_types=1);

namespace App\Domain\Announcement\DataTransferObjects;

class AnnouncementData
{
    public function __construct(
        public readonly string $title,
        public readonly string $content,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(...$data);
    }
}
