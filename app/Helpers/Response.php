<?php

namespace App\Helpers;

use App\Helpers\Session;

/**
 * HTTP Response Helper
 *
 * Provides convenient methods for sending common HTTP responses like
 * JSON data, redirects, and standardized success/error messages.
 */
class Response
{
    /**
     * Sends a JSON response.
     * إرسال استجابة JSON.
     *
     * Sets the appropriate headers, encodes the data, outputs it, and terminates the script.
     * يقوم بتعيين الهيدر المناسب، يشفر البيانات، يطبعها، وينهي السكربت.
     *
     * @param mixed $data The data (e.g., array, object) to encode as JSON.
     * @param int $status The HTTP status code (e.g., 200 OK, 404 Not Found).
     * @return void Never returns, script execution stops.
     */
    public static function json($data, int $status = 200): void
    {
        // Set HTTP status code
        // تعيين كود حالة HTTP
        http_response_code($status);
        
        // Set content type header
        // تعيين هيدر نوع المحتوى
        header('Content-Type: application/json; charset=utf-8');
        
        // Output JSON encoded data
        // طباعة البيانات المشفرة بصيغة JSON
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        // Terminate script execution
        // إنهاء تنفيذ السكربت
        exit();
    }

    /**
     * Sends a standardized success JSON response.
     * إرسال استجابة JSON موحدة للنجاح.
     *
     * @param string $message A success message.
     * @param array $data Optional additional data to include in the response.
     * @param int $status The HTTP status code (usually 200 or 201).
     * @return void
     */
    public static function success(string $message, array $data = [], int $status = 200): void
    {
        $response = [
            'status' => 'success',
            'message' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8'), // Sanitize message
            'data' => $data,
        ];
        self::json($response, $status);
    }

    /**
     * Sends a standardized error JSON response.
     * إرسال استجابة JSON موحدة للخطأ.
     *
     * @param string $message An error message.
     * @param int $status The HTTP status code (e.g., 400, 404, 500).
     * @param array $data Optional additional data or error details.
     * @return void
     */
    public static function error(string $message, int $status = 400, array $data = []): void
    {
        $response = [
            'status' => 'error',
            'message' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8'), // Sanitize message
        ];
        // Only include data if it's not empty, often used for validation errors
        // تضمين البيانات فقط إذا لم تكن فارغة، غالباً تُستخدم لأخطاء التحقق
        if (!empty($data)) {
            $response['data'] = $data;
        }
        self::json($response, $status);
    }

    /**
     * Redirects the user to a specified URL and terminates the script.
     * إعادة توجيه المستخدم إلى رابط محدد وإنهاء السكربت.
     *
     * @param string $url The URL to redirect to.
     * @param int $status The HTTP redirect status code (302 for temporary, 301 for permanent).
     * @return self Returns an instance for method chaining (e.g., withFlash).
     */
    public static function redirect(string $url, int $status = 302): self
    {
         // Set the redirect header and status code
         // تعيين هيدر إعادة التوجيه وكود الحالة
        http_response_code($status);
        header('Location: ' . $url);
        // Do NOT exit here if we want to chain `withFlash`
        // لا تنهِ التنفيذ هنا إذا أردنا استخدام `withFlash` بعده
        return new self(); // Return instance for chaining
    }

    /**
     * Sets a flash message in the session before the redirect happens.
     * تخزين رسالة مؤقتة (فلاش) في الجلسة قبل حدوث إعادة التوجيه.
     *
     * This method should be chained after `redirect()`.
     * يجب استخدام هذه الدالة بعد `redirect()`.
     *
     * @param string $type The type of flash message ('success', 'error', 'info', etc.).
     * @param string $message The message content.
     * @return void Terminates the script execution after setting the flash message.
     */
    public function withFlash(string $type, string $message): void
    {
        Session::flash($type, $message);
        exit(); // Terminate execution after setting flash and headers
    }
}