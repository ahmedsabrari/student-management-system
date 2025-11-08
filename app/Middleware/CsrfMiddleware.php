<?php

declare(strict_types=1); // Enforce strict types

namespace App\Middleware;

use App\Helpers\CSRF;
use App\Helpers\Response;
use App\Core\Request; // Use Request helper to get method and input

/**
 * CSRF Protection Middleware
 *
 * Verifies the CSRF token submitted with potentially state-changing requests
 * (POST, PUT, PATCH, DELETE) against the token stored in the user's session.
 * If the token is invalid or missing, it rejects the request and redirects back.
 */
class CsrfMiddleware
{
    /**
     * Handle the incoming request.
     * معالجة الطلب الوارد.
     *
     * Validates the CSRF token for relevant HTTP methods.
     * يتحقق من صحة رمز CSRF لأنواع طلبات HTTP ذات الصلة.
     *
     * @return void
     */
    public static function handle(): void
    {
        // Get the request method
        // الحصول على نوع الطلب
        $requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        // Define methods that need CSRF protection
        // تحديد أنواع الطلبات التي تحتاج إلى حماية CSRF
        $methodsToProtect = ['POST', 'PUT', 'DELETE', 'PATCH'];

        if (in_array($requestMethod, $methodsToProtect)) {
            // This is a request that might change state, validate the token
            // هذا طلب قد يغير الحالة، قم بالتحقق من الرمز

            // Retrieve the submitted token from POST data
            // استرجاع الرمز المرسل من بيانات POST
            // Using $_POST directly here as Request helper might parse JSON body differently
            // استخدام $_POST مباشرة هنا لأن مساعد الطلب قد يحلل جسم JSON بشكل مختلف
            $submittedToken = $_POST[CSRF::INPUT_NAME] ?? null;

            // Validate the token using the CSRF helper
            // التحقق من الرمز باستخدام مساعد CSRF
            if (!CSRF::validateToken($submittedToken)) {
                // CSRF token is invalid or missing
                // رمز CSRF غير صالح أو مفقود

                // Log the failure for security auditing (optional)
                // تسجيل الفشل للتدقيق الأمني (اختياري)
                // error_log('CSRF validation failed for IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN'));

                // Redirect back to the previous page with an error message
                // إعادة التوجيه إلى الصفحة السابقة مع رسالة خطأ
                // Using HTTP_REFERER can be slightly unreliable but is common practice
                // استخدام HTTP_REFERER قد لا يكون موثوقًا تمامًا ولكنه ممارسة شائعة
                $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/'; // Fallback to homepage

                Response::redirect($redirectUrl)
                        ->withFlash('error', 'Invalid security token or session expired. Please try submitting the form again.');
                
                // Response::redirect()->withFlash() handles exit() automatically.
                // الدالة Response::redirect()->withFlash() تتعامل مع exit() تلقائيًا.
                return; // Return added for clarity
            }
        }

        // If the method is GET/HEAD/OPTIONS or if the CSRF token is valid,
        // do nothing and let the request proceed.
        // إذا كان نوع الطلب GET/HEAD/OPTIONS أو إذا كان رمز CSRF صالحًا،
        // لا تفعل شيئًا ودع الطلب يستمر.
    }
}