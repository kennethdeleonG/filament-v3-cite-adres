<?php

declare(strict_types=1);

namespace App\Domain\Faculty\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyEmail extends \Illuminate\Auth\Notifications\VerifyEmail implements ShouldQueue
{
    use Queueable;
}
