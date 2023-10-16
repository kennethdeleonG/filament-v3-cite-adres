<?php

declare(strict_types=1);

namespace App\Domain\Announcement\Actions;

use App\Domain\Announcement\DataTransferObjects\AnnouncementData;
use App\Domain\Announcement\Events\FacultyAnnouncementNotificationEvent;
use App\Domain\Announcement\Models\Announcement;


class CreateAnnouncementAction
{
    public function execute(AnnouncementData $announcementData): Announcement
    {

        $announcement = Announcement::create([
            'title' => $announcementData->title,
            'content' => $announcementData->content,
        ]);

        if ($announcement) {
            event(new FacultyAnnouncementNotificationEvent(
                $announcementData->title,
                $announcementData->content
            ));
        }

        return $announcement;
    }
}
