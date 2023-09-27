<?php

declare(strict_types=1);

namespace App\Domain\Faculty\Enums;

enum FacultyStatuses: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BANNED = 'banned';
}
