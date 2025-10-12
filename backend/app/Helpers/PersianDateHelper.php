<?php

namespace App\Helpers;

use Carbon\Carbon;

class PersianDateHelper
{
    /**
     * Convert date to Persian "time ago" format
     *
     * @param Carbon $date
     * @return string
     */
    public static function timeAgo(Carbon $date): string
    {
        $now = Carbon::now();
        $diff = $date->diff($now);

        // Years
        if ($diff->y > 0) {
            return $diff->y === 1 ? 'یک سال پیش' : self::convertToPersianNumber($diff->y) . ' سال پیش';
        }

        // Months
        if ($diff->m > 0) {
            return $diff->m === 1 ? 'یک ماه پیش' : self::convertToPersianNumber($diff->m) . ' ماه پیش';
        }

        // Weeks
        $weeks = floor($diff->days / 7);
        if ($weeks > 0) {
            return $weeks === 1 ? 'یک هفته پیش' : self::convertToPersianNumber($weeks) . ' هفته پیش';
        }

        // Days
        if ($diff->d > 0) {
            return $diff->d === 1 ? 'یک روز پیش' : self::convertToPersianNumber($diff->d) . ' روز پیش';
        }

        // Hours
        if ($diff->h > 0) {
            return $diff->h === 1 ? 'یک ساعت پیش' : self::convertToPersianNumber($diff->h) . ' ساعت پیش';
        }

        // Minutes
        if ($diff->i > 0) {
            return $diff->i === 1 ? 'یک دقیقه پیش' : self::convertToPersianNumber($diff->i) . ' دقیقه پیش';
        }

        // Seconds
        return 'چند لحظه پیش';
    }

    /**
     * Convert English numbers to Persian numbers
     *
     * @param mixed $number
     * @return string
     */
    public static function convertToPersianNumber($number): string
    {
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

        return str_replace($englishNumbers, $persianNumbers, (string) $number);
    }
}

