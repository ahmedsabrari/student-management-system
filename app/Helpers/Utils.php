<?php

declare(strict_types=1); // Enforce strict types for better code quality

namespace App\Helpers;

/**
 * Utility Helper Class
 *
 * Provides various static helper functions for common tasks like data sanitization,
 * date formatting, slug generation, token creation, and route checking.
 */
class Utils
{
    /**
     * Sanitize a string to prevent XSS attacks.
     * تنظيف النص لمنع هجمات XSS.
     *
     * @param string|null $value The input string.
     * @return string The sanitized string.
     */
    public static function sanitize(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        // ENT_QUOTES converts both double and single quotes.
        // UTF-8 ensures proper handling of multi-byte characters.
        // يقوم بتحويل علامات الاقتباس المزدوجة والمفردة ويدعم UTF-8.
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Format a date string into a specified format.
     * تنسيق التاريخ إلى صيغة محددة.
     *
     * @param string|null $date The date string (e.g., '2024-10-26 15:30:00', 'now').
     * @param string $format The desired output format (PHP date format string).
     * @return string The formatted date string, or an empty string on failure.
     */
    public static function formatDate(?string $date, string $format = 'Y-m-d'): string
    {
        if (empty($date)) {
            return '';
        }
        try {
            $dateTime = new \DateTime($date);
            return $dateTime->format($format);
        } catch (\Exception $e) {
            // Log error or handle invalid date string if necessary
            // تسجيل الخطأ أو التعامل مع صيغة التاريخ غير الصالحة
            return ''; // Return empty for invalid dates
        }
    }

    /**
     * Generate a URL-friendly "slug" from a given string.
     * إنشاء "slug" مناسب لعنوان URL من نص معين.
     *
     * @param string $text The input string.
     * @param string $divider The character to use as a separator (default: '-').
     * @return string The generated slug.
     */
    public static function slugify(string $text, string $divider = '-'): string
    {
        // Replace non letter or digits by divider
        // استبدال الأحرف غير الأبجدية أو الرقمية بالفاصل
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // Transliterate (convert non-latin chars to latin approximations) if possible
        // تحويل الأحرف غير اللاتينية إلى ما يقاربها باللاتينية (إذا أمكن)
        if (function_exists('transliterator_transliterate')) {
             // @codeCoverageIgnoreStart
            $text = transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove', $text) ?? $text;
             // @codeCoverageIgnoreEnd
        } elseif (function_exists('iconv')) {
             // @codeCoverageIgnoreStart
             $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text) ?: $text;
             // @codeCoverageIgnoreEnd
        }

        // Remove unwanted characters
        // إزالة الأحرف غير المرغوب فيها
        $text = preg_replace('~[^-\w]+~', '', $text);

        // Trim
        // إزالة الفواصل من البداية والنهاية
        $text = trim($text, $divider);

        // Remove duplicate dividers
        // إزالة الفواصل المكررة
        $text = preg_replace('~-+~', $divider, $text);

        // Lowercase
        // تحويل إلى أحرف صغيرة
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a'; // Return 'n-a' if slug becomes empty
        }

        return $text;
    }

    /**
     * Generate a cryptographically secure random token.
     * إنشاء رمز عشوائي آمن تشفيرياً.
     *
     * @param int $length The desired length of the token (must be an even number).
     * @return string The generated token.
     * @throws \Exception If random_bytes fails or length is invalid.
     */
    public static function generateToken(int $length = 32): string
    {
        if ($length < 4 || $length % 2 !== 0) {
            throw new \InvalidArgumentException('Token length must be an even number greater than or equal to 4.');
        }
        // Each byte converts to 2 hex characters, so divide length by 2
        // كل بايت يتحول إلى حرفين سداسيين عشرين، لذا نقسم الطول على 2
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Check if the current request URI matches a given route pattern.
     * التحقق مما إذا كان مسار URI الحالي يطابق نمط مسار معين.
     * Useful for setting the 'active' class on sidebar links.
     * مفيد لتعيين كلاس 'active' لروابط الشريط الجانبي.
     *
     * @param string $route The route pattern to check (e.g., '/students', '/students/*').
     * @return bool True if the current route matches the pattern, false otherwise.
     */
    public static function isActiveRoute(string $route): bool
    {
        $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '/';
        $currentPath = '/' . trim($currentPath, '/'); // Normalize current path

        $route = '/' . trim($route, '/'); // Normalize route

        // Exact match
        // تطابق تام
        if ($currentPath === $route) {
            return true;
        }

        // Wildcard match (e.g., /students/* should match /students/create)
        // تطابق باستخدام البدل (*)
        if (str_ends_with($route, '/*')) {
            $baseRoute = substr($route, 0, -2); // Remove '/*'
            // Check if current path starts with the base route and is longer
            // التحقق مما إذا كان المسار الحالي يبدأ بالمسار الأساسي وأطول منه
            if (str_starts_with($currentPath, $baseRoute) && strlen($currentPath) > strlen($baseRoute)) {
                 // Ensure it matches the full segment, e.g., /students should not match /students-report
                 // التأكد من تطابق المقطع بالكامل (مثلاً، /students لا تطابق /students-report)
                 return substr($currentPath, strlen($baseRoute), 1) === '/';
            }
        }

        return false;
    }

    /**
     * Redirects the user back to the previous page using the HTTP_REFERER.
     * إعادة توجيه المستخدم إلى الصفحة السابقة باستخدام HTTP_REFERER.
     *
     * Provides a fallback URL if the referer is not set or is external.
     * يوفر رابطًا احتياطيًا إذا لم يتم تعيين المُحيل أو كان خارجيًا.
     *
     * @param string $fallbackUrl The URL to redirect to if HTTP_REFERER is invalid or missing.
     * @return void Never returns, script execution stops.
     */
    public static function redirectBack(string $fallbackUrl = '/'): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;

        // Basic validation to prevent redirecting to external sites if referer is manipulated
        // تحقق أساسي لمنع إعادة التوجيه لمواقع خارجية إذا تم التلاعب بالمُحيل
        if ($referer && parse_url($referer, PHP_URL_HOST) === ($_SERVER['HTTP_HOST'] ?? null)) {
            $url = $referer;
        } else {
            $url = $fallbackUrl;
        }

        header('Location: ' . $url);
        exit();
    }
}