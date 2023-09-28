<?php

declare(strict_types=1);

namespace App\Domain\Folder\Exceptions;

use Exception;

class AssetDownloadException extends Exception
{
    public function __construct(string $message = 'Please check if assets are active or published; the zip is empty')
    {
        parent::__construct(message: $message, code: 422);
    }
}
