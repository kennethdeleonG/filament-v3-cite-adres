<?php

namespace App\Filament\Faculty\Pages;

use App\Domain\Announcement\Models\Announcement;
use Filament\Pages\Page;
use DateTimeZone;
use Illuminate\Support\Carbon;

class Announcements extends Page
{
    public mixed $announcementList;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static string $view = 'filament.pages.faculty.announcements';

    public function mount(): void
    {
        $this->fetchData();
    }

    public function fetchData(): void
    {
        $this->announcementList = $this->getAnnouncements();
    }

    public function getAnnouncements()
    {
        $result = Announcement::orderBy('created_at', 'desc')->get();

        return $result;
    }

    public function dateFrom(Carbon $date): string
    {
        $carbonDate = Carbon::parse($date);

        $userTimezone = 'Asia/Manila';
        if (!empty($userTimezone) && in_array($userTimezone, DateTimeZone::listIdentifiers())) {
            $carbonDate->setTimezone($userTimezone);
        }

        return $carbonDate->diffForHumans();
    }
}
