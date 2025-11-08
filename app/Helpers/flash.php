<?php

namespace App\Helpers;

use App\Helpers\Session;

/**
 * Sets a flash message to be displayed on the next request.
 * تخزين رسالة فلاش للعرض في الطلب التالي.
 *
 * Uses the Session helper to store the message.
 * تستخدم مساعد الجلسة لتخزين الرسالة.
 *
 * @param string $type The type of message (e.g., 'success', 'error', 'warning', 'info'). Determines the alert style.
 * @param string $message The message content.
 * @return void
 */
function setFlash(string $type, string $message): void
{
    Session::flash($type, $message);
}

/**
 * Retrieves the first available flash message (and removes it).
 * استرجاع أول رسالة فلاش متاحة (وحذفها).
 *
 * Checks for standard types ('success', 'error', 'warning', 'info') in order.
 * تتحقق من الأنواع القياسية بالترتيب.
 *
 * @return array{type: string, message: string}|null Returns an array with 'type' and 'message', or null if no flash message exists.
 */
function getFlash(): ?array
{
    $types = ['success', 'error', 'warning', 'info']; // Order of priority
    foreach ($types as $type) {
        $message = Session::getFlash($type);
        if ($message !== null) {
            return ['type' => $type, 'message' => $message];
        }
    }
    return null;
}

/**
 * Displays the first available flash message using Bootstrap alert styling.
 * عرض أول رسالة فلاش متاحة باستخدام تنسيق Bootstrap alert.
 *
 * If a flash message exists, it's retrieved, formatted as a dismissible alert, and echoed.
 * إذا كانت هناك رسالة، يتم استرجاعها وتنسيقها وعرضها.
 *
 * @return void
 */
function displayFlash(): void
{
    $flash = getFlash(); // Retrieves and removes the message

    if ($flash) {
        $type = $flash['type'];
        $message = $flash['message'];

        // Map flash types to Bootstrap alert classes
        // ربط أنواع الفلاش بكلاسات Bootstrap alert
        $alertClass = match ($type) {
            'success' => 'alert-success',
            'error'   => 'alert-danger',
            'warning' => 'alert-warning',
            'info'    => 'alert-info',
            default   => 'alert-secondary', // Fallback
        };

        // Output the Bootstrap alert HTML
        // طباعة كود HTML الخاص بـ Bootstrap alert
        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); // Sanitize output
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
}