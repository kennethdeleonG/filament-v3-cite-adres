<?php

declare(strict_types=1);

namespace App\Domain\Announcement\Listeners;

use App\Domain\Announcement\Events\FacultyAnnouncementNotificationEvent;
use App\Domain\Announcement\Events\FacultyFolderCreatedNotificationEvent;
use App\Domain\Faculty\Models\Faculty;
use App\Filament\Faculty\Pages\DocumentManagement;
use Carbon\Carbon;
use DateTimeZone;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class FacultyFolderCreatedNotificationListener
{

    public function handle(FacultyFolderCreatedNotificationEvent $event): void
    {
        $folderData = $event->folderData;
        $folder = $event->folder;

        $recipients = Faculty::withoutTrashed()->get();

        $dueDate = $this->convertedDueDate($folderData->due_date);

        if (!$folderData->is_private) {

            $recipients->each(function ($recipient) use ($folderData, $folder, $dueDate) {
                $recipient->notify(
                    Notification::make()
                        ->title("New Folder Created with the due date of $dueDate")
                        ->body("Folder named as: $folderData->name")
                        ->actions([
                            Action::make('View')
                                ->url("https://bulsu-cicsadres.net/faculty/documents" . '/' . $folder->id,  true)
                                ->outlined()
                                ->markAsRead(),
                        ])
                        ->toDatabase(),
                );
            });
        }
    }

    public function convertedDueDate(Carbon $date): string
    {
        $format = "M j, Y";

        $carbonDate = Carbon::parse($date);

        $userTimezone = 'Asia/Manila';
        if (!empty($userTimezone) && in_array($userTimezone, DateTimeZone::listIdentifiers())) {
            $carbonDate->setTimezone($userTimezone);
        }

        return $carbonDate->translatedFormat($format);
    }
}
