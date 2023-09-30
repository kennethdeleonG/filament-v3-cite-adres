<?php

declare(strict_types=1);

namespace App\Support\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case FACULTY = 'faculty';
}
