<?php

namespace App\Helpers;

/**
 * Session Management Helper
 *
 * Provides a simple and secure OOP interface for managing PHP sessions,
 * including setting, getting, removing data, handling flash messages,
 * and regenerating session IDs for security.
 */
class Session
{
    /**
     * The session key used to store flash messages.
     * مفتاح الجلسة المستخدم لتخزين رسائل الفلاش.
     * @var string
     */
    private const FLASH_KEY = '_flash';

    /**
     * Start the session if it hasn't been started already.
     * بدء الجلسة إذا لم تكن قد بدأت بالفعل.
     *
     * @return bool True if session was started or already active, false on failure.
     */
    public static function start(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
             // @codeCoverageIgnoreStart
             // Configure session parameters before starting (optional but recommended)
             // Recommended settings from session.php config file should be applied here
             // session_set_cookie_params([...]); // Load from config/session.php
             return session_start();
             // @codeCoverageIgnoreEnd
        }
        return true;
    }

    /**
     * Set a value in the session.
     * تخزين قيمة في الجلسة.
     *
     * @param string $key The key to store the value under.
     * @param mixed $value The value to store.
     * @return void
     */
    public static function set(string $key, $value): void
    {
        self::start(); // Ensure session is active
        $_SESSION[$key] = $value;
    }

    /**
     * Get a value from the session.
     * استرجاع قيمة من الجلسة.
     *
     * @param string $key The key of the value to retrieve.
     * @param mixed $default The default value to return if the key doesn't exist.
     * @return mixed The value from the session or the default value.
     */
    public static function get(string $key, $default = null)
    {
        self::start(); // Ensure session is active
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a key exists in the session.
     * التحقق من وجود مفتاح معين في الجلسة.
     *
     * @param string $key The key to check.
     * @return bool True if the key exists, false otherwise.
     */
    public static function has(string $key): bool
    {
        self::start(); // Ensure session is active
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a specific key and its value from the session.
     * حذف قيمة معينة من الجلسة.
     *
     * @param string $key The key to remove.
     * @return void
     */
    public static function remove(string $key): void
    {
        self::start(); // Ensure session is active
        unset($_SESSION[$key]);
    }

    /**
     * Destroy the entire session.
     * تدمير الجلسة بالكامل (تسجيل الخروج).
     *
     * @return bool True on success, false on failure.
     */
    public static function destroy(): bool
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Unset all session variables
            // إلغاء تعيين جميع متغيرات الجلسة
            $_SESSION = [];

            // If using cookies, delete the session cookie
            // إذا كنت تستخدم الكوكيز، احذف ملف تعريف الارتباط الخاص بالجلسة
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }

            // Finally, destroy the session
            // أخيرًا، تدمير الجلسة
            return session_destroy();
        }
        return false;
    }

    /**
     * Set a flash message (stored for one subsequent request).
     * تخزين رسالة فلاش (تُعرض مرة واحدة فقط).
     *
     * @param string $key The key for the flash message (e.g., 'success', 'error').
     * @param string $message The message to store.
     * @return void
     */
    public static function flash(string $key, string $message): void
    {
        self::start();
        if (!isset($_SESSION[self::FLASH_KEY])) {
            $_SESSION[self::FLASH_KEY] = [];
        }
        $_SESSION[self::FLASH_KEY][$key] = $message;
    }

    /**
     * Get a flash message and remove it from the session.
     * استرجاع رسالة فلاش وحذفها من الجلسة.
     *
     * @param string $key The key of the flash message to retrieve.
     * @param mixed $default Default value if the key doesn't exist.
     * @return mixed The flash message or the default value.
     */
    public static function getFlash(string $key, $default = null)
    {
        self::start();
        if (isset($_SESSION[self::FLASH_KEY][$key])) {
            $message = $_SESSION[self::FLASH_KEY][$key];
            unset($_SESSION[self::FLASH_KEY][$key]); // Remove after retrieving
            // Optionally remove the whole flash array if empty
            if (empty($_SESSION[self::FLASH_KEY])) {
                 unset($_SESSION[self::FLASH_KEY]);
            }
            return $message;
        }
        return $default;
    }
    
    /**
     * Check if a flash message exists for a given key.
     * التحقق من وجود رسالة فلاش لمفتاح معين.
     *
     * @param string $key The key to check.
     * @return bool
     */
    public static function hasFlash(string $key): bool
    {
        self::start();
        return isset($_SESSION[self::FLASH_KEY][$key]);
    }


    /**
     * Regenerate the session ID.
     * Crucial for security after login or privilege level change to prevent session fixation.
     * إعادة إنشاء معرف الجلسة. ضروري للأمان بعد تسجيل الدخول أو تغيير مستوى الصلاحيات.
     *
     * @param bool $deleteOldSession Whether to delete the old session file. Default is true.
     * @return bool True on success, false on failure.
     */
    public static function regenerate(bool $deleteOldSession = true): bool
    {
        // Session must be active to regenerate ID
        // يجب أن تكون الجلسة نشطة لإعادة إنشاء المعرف
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Passing true deletes the old session file associated with the old ID
            // تمرير true يحذف ملف الجلسة القديم المرتبط بالمعرف القديم
            return session_regenerate_id($deleteOldSession);
        }
        return false;
    }
}