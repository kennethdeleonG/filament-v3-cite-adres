<?php

declare(strict_types=1);

namespace App\Support\Concerns;

use DateTimeZone;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

trait CustomFormatHelper
{
    public function convertedDate(Carbon $date): string
    {
        $format = "M j, Y H:i:s";

        $carbonDate = Carbon::parse($date);

        $userTimezone = 'Asia/Manila';
        if (!empty($userTimezone) && in_array($userTimezone, DateTimeZone::listIdentifiers())) {
            $carbonDate->setTimezone($userTimezone);
        }

        return $carbonDate->translatedFormat($format);
    }

    public function convertedAssetSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
