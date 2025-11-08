<?php

namespace App\Helpers;

/**
 * CSRF (Cross-Site Request Forgery) Protection Helper
 *
 * Provides methods to generate, validate, and manage CSRF tokens
 * stored in the user's session to protect forms against CSRF attacks.
 * Uses one-time tokens for enhanced security.
 */
class CSRF
{
    /**
     * The session key used to store the CSRF token.
     * مفتاح الجلسة المستخدم لتخزين رمز CSRF.
     * @var string
     */
    private const SESSION_KEY = '_csrf_token';

    /**
     * The name attribute for the hidden input field.
     * اسم حقل الإدخال المخفي.
     * @var string
     */
    public const INPUT_NAME = '_csrf_token';

    /**
     * Generate a new CSRF token and store it in the session.
     * If a token already exists, it will be overwritten.
     * إنشاء رمز CSRF جديد وتخزينه في الجلسة. إذا كان هناك رمز موجود، سيتم استبداله.
     *
     * @return string The newly generated token.
     */
    public static function generateToken(): string
    {
        // Ensure session is started
        // التأكد من بدء الجلسة
        if (session_status() === PHP_SESSION_NONE) {
            // @codeCoverageIgnoreStart
            session_start();
            // @codeCoverageIgnoreEnd
        }

        // Generate a cryptographically secure random token
        // إنشاء رمز عشوائي آمن تشفيرياً
        $token = hash_hmac('sha256', bin2hex(random_bytes(32)), 'your-secret-csrf-key'); // Replace with a strong secret key, possibly from Env::get()

        $_SESSION[self::SESSION_KEY] = $token;

        return $token;
    }

    /**
     * Retrieve the current CSRF token from the session.
     * If no token exists, it generates a new one.
     * استرجاع رمز CSRF الحالي من الجلسة. إذا لم يكن هناك رمز، يتم إنشاء واحد جديد.
     *
     * @return string The CSRF token.
     */
    public static function getToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            // @codeCoverageIgnoreStart
            session_start();
            // @codeCoverageIgnoreEnd
        }

        if (!isset($_SESSION[self::SESSION_KEY])) {
            return self::generateToken();
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Validate a submitted CSRF token against the one stored in the session.
     * This uses hash_equals for timing attack resistance.
     * For one-time use, the token is removed from the session after successful validation.
     * التحقق من رمز CSRF المُرسل مقارنة بالرمز المخزن في الجلسة.
     * يستخدم hash_equals للحماية من هجمات التوقيت.
     * للاستخدام لمرة واحدة، يتم حذف الرمز من الجلسة بعد التحقق الناجح.
     *
     * @param string|null $submittedToken The token received from the request (e.g., $_POST[self::INPUT_NAME]).
     * @return bool True if the token is valid, false otherwise.
     */
    public static function validateToken(?string $submittedToken): bool
    {
        if ($submittedToken === null) {
            return false;
        }
        
        if (session_status() === PHP_SESSION_NONE) {
             // @codeCoverageIgnoreStart
             session_start();
             // @codeCoverageIgnoreEnd
        }

        $sessionToken = $_SESSION[self::SESSION_KEY] ?? null;

        if ($sessionToken === null) {
            // No token in session, validation fails
            // لا يوجد رمز في الجلسة، فشل التحقق
            return false;
        }

        $isValid = hash_equals($sessionToken, $submittedToken);

        // Remove the token after validation attempt (one-time use)
        // حذف الرمز بعد محاولة التحقق (استخدام لمرة واحدة)
        unset($_SESSION[self::SESSION_KEY]); 
        
        // Regenerate a new token for the next request immediately
        // إعادة إنشاء رمز جديد للطلب التالي فورًا
        self::generateToken(); 

        return $isValid;
    }

    /**
     * Generate a hidden HTML input field containing the current CSRF token.
     * إنشاء حقل HTML مخفي يحتوي على رمز CSRF الحالي.
     *
     * @return string The HTML input field markup.
     */
    public static function inputField(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="' . self::INPUT_NAME . '" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Regenerate the CSRF token.
     * This is useful after a successful action or login/logout.
     * إعادة إنشاء رمز CSRF. مفيد بعد عملية ناجحة أو تسجيل الدخول/الخروج.
     *
     * @return string The newly generated token.
     */
    public static function regenerateToken(): string
    {
        // Simply calls generateToken which overwrites the existing one
        // يستدعي ببساطة generateToken التي تستبدل الرمز الموجود
        return self::generateToken();
    }
}