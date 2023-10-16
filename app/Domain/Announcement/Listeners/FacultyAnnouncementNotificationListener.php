<?php

declare(strict_types=1);

namespace App\Domain\Announcement\Listeners;

use App\Domain\Announcement\Events\FacultyAnnouncementNotificationEvent;
use App\Domain\Faculty\Models\Faculty;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class FacultyAnnouncementNotificationListener
{
    /**
     * Handle the event.
     *
     * @param  \Domain\Announcement\Events\FacultyAnnouncementNotificationEvent  $event
     * @return void
     */
    public function handle(FacultyAnnouncementNotificationEvent $event): void
    {
        $title = $event->title;
        $body = $event->body;

        $recipients = Faculty::withoutTrashed()->get();

        $recipients->each(function ($recipient) use ($title, $body) {
            $recipient->notify(
                Notification::make()
                    ->title($title)
                    ->body($body)
                    ->actions([
                        Action::make('Mark as read')
                            ->outlined()
                            ->markAsRead(),
                    ])
                    ->toDatabase(),
            );
        });
    }
}
