<?php

declare(strict_types=1);

namespace App\Domain\Announcement\Events;

use Illuminate\Queue\SerializesModels;

class FacultyAnnouncementNotificationEvent
{
    use SerializesModels;

    public string $title;
    public string $body;

    public function __construct(
        string $title,
        string $body
    ) {
        $this->title = $title;
        $this->body = $body;
    }
}
