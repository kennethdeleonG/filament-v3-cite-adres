<?php

declare(strict_types=1);

namespace App\Domain\Announcement\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
    ];
}
