<?php

// Note: Standalone function files cannot use declare(strict_types=1);

namespace App\Helpers;

use DateTime;
use DateTimeZone;
use Exception;

/**
 * Date & Time Helper Functions
 *
 * Provides convenient functions for formatting dates, times,
 * and calculating relative time differences.
 */


/**
 * Formats a date string into a specified format.
 * تنسيق التاريخ إلى صيغة محددة.
 * Handles potential invalid date strings gracefully.
 * يتعامل مع صيغ التاريخ غير الصالحة بأمان.
 *
 * @param string|null $date The date string (e.g., '2024-10-26', '2024-10-26 15:30:00').
 * @param string $format The desired output format (PHP date format string).
 * @return string The formatted date string, or an empty string on failure or null input.
 */
function formatDate(?string $date, string $format = 'd-m-Y'): string
{
    if (empty($date)) {
        return '';
    }
    try {
        // Create DateTime object, assumes system default timezone if not specified
        // إنشاء كائن DateTime، يفترض المنطقة الزمنية الافتراضية للنظام إذا لم يتم تحديدها
        $dateTime = new DateTime($date);
        return $dateTime->format($format);
    } catch (Exception $e) {
        // Log error or handle invalid date string if necessary
        // تسجيل الخطأ أو التعامل مع صيغة التاريخ غير الصالحة
        // error_log("Invalid date format in formatDate: " . $e->getMessage());
        return ''; // Return empty for invalid dates
    }
}

/**
 * Formats a date and time string into a specified format.
 * تنسيق التاريخ والوقت إلى صيغة محددة.
 *
 * @param string|null $dateTime The date-time string (e.g., '2024-10-26 15:30:00').
 * @param string $format The desired output format (PHP date format string).
 * @return string The formatted date-time string, or an empty string on failure or null input.
 */
function formatDateTime(?string $dateTime, string $format = 'd-m-Y H:i'): string
{
    // Reuse formatDate as it handles DateTime creation and errors
    // إعادة استخدام formatDate لأنها تتعامل مع إنشاء DateTime والأخطاء
    return formatDate($dateTime, $format);
}

/**
 * Calculates the time elapsed since a given datetime string (e.g., "2 hours ago").
 * حساب الوقت المنقضي منذ تاريخ ووقت معين (مثل "قبل ساعتين").
 *
 * @param string|null $datetime The past datetime string.
 * @return string A human-readable relative time difference, or an empty string on failure.
 */
function timeAgo(?string $datetime): string
{
    if (empty($datetime)) {
        return '';
    }

    try {
        $time = strtotime($datetime);
        if ($time === false) {
             // @codeCoverageIgnoreStart
            throw new Exception('Invalid datetime string provided to timeAgo.');
             // @codeCoverageIgnoreEnd
        }

        $currentTime = time();
        $timeDifference = $currentTime - $time;

        // If the time is in the future, return 'in the future' or similar
        // إذا كان الوقت في المستقبل، أرجع 'في المستقبل' أو ما شابه
        if ($timeDifference < 1) {
            return 'just now'; // Or 'in the future' depending on preference
        }

        $periods = [
            31536000 => 'year',    // 60 * 60 * 24 * 365
            2592000  => 'month',   // 60 * 60 * 24 * 30
            604800   => 'week',    // 60 * 60 * 24 * 7
            86400    => 'day',     // 60 * 60 * 24
            3600     => 'hour',    // 60 * 60
            60       => 'minute',
            1        => 'second'
        ];
        $pluralSuffix = 's';

        foreach ($periods as $seconds => $unit) {
            $duration = $timeDifference / $seconds;
            if ($duration >= 1) {
                $roundedDuration = round($duration);
                return $roundedDuration . ' ' . $unit . ($roundedDuration > 1 ? $pluralSuffix : '') . ' ago';
            }
        }

        return 'just now'; // Fallback for very recent times
         // @codeCoverageIgnoreStart
    } catch (Exception $e) {
        // error_log("Error in timeAgo: " . $e->getMessage());
        return ''; // Return empty on error
    }
     // @codeCoverageIgnoreEnd
}


/**
 * Gets the current date formatted according to the specified format.
 * الحصول على التاريخ الحالي منسقًا حسب الصيغة المحددة.
 *
 * @param string $format The desired output format.
 * @return string The formatted current date string.
 */
function currentDate(string $format = 'Y-m-d'): string
{
    try {
        // Ensures it uses the application's default timezone if set
        // يضمن استخدام المنطقة الزمنية الافتراضية للتطبيق إذا تم تعيينها
        return (new DateTime('now'))->format($format);
         // @codeCoverageIgnoreStart
    } catch (Exception $e) {
        // Should generally not happen with 'now'
        // لا ينبغي أن يحدث هذا عادةً مع 'now'
        // error_log("Error getting current date: " . $e->getMessage());
        return date($format); // Fallback to basic date function
    }
     // @codeCoverageIgnoreEnd
}

/**
 * Gets the current date and time formatted according to the specified format.
 * الحصول على التاريخ والوقت الحاليين منسقين حسب الصيغة المحددة.
 *
 * @param string $format The desired output format.
 * @return string The formatted current date-time string.
 */
function currentDateTime(string $format = 'Y-m-d H:i:s'): string
{
    // Reuse currentDate logic
    // إعادة استخدام منطق currentDate
    return currentDate($format);
}

