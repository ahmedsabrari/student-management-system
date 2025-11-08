<?php

declare(strict_types=1); // Enforce strict types

namespace App\Middleware;

/**
 * CORS (Cross-Origin Resource Sharing) Middleware
 *
 * Adds the necessary HTTP headers to allow or restrict cross-origin requests,
 * typically used for API endpoints accessed by front-end applications
 * hosted on different domains.
 */
class CorsMiddleware
{
    /**
     * Handle the incoming request.
     * معالجة الطلب الوارد.
     *
     * Adds appropriate Access-Control-* headers to the response.
     * Handles preflight OPTIONS requests.
     * إضافة رؤوس Access-Control-* المناسبة إلى الاستجابة.
     * معالجة طلبات OPTIONS التمهيدية.
     *
     * @return void
     */
    public static function handle(): void
    {
        // Allowed Origin(s)
        // النطاقات المسموح لها بالوصول
        // Use '*' for development/public API, or specify origins for production.
        // استخدم '*' للتطوير/API العام، أو حدد نطاقات معينة للإنتاج.
        // Example for specific origins: 'https://yourfrontend.com'
        $allowedOrigin = '*'; // Consider making this configurable via Env::get('ALLOWED_ORIGINS', '*')

        // Set the Access-Control-Allow-Origin header
        // تعيين هيدر Access-Control-Allow-Origin
        // This header tells the browser which origins are allowed to access the resource.
        // '*' allows any origin, which is less secure for production APIs handling sensitive data.
        // يخبر هذا الهيدر المتصفح أي النطاقات مسموح لها بالوصول إلى المورد.
        // '*' يسمح بأي نطاق، وهو أقل أمانًا لواجهات برمجة التطبيقات في الإنتاج التي تتعامل مع بيانات حساسة.
        header("Access-Control-Allow-Origin: {$allowedOrigin}");

        // Set the Access-Control-Allow-Methods header
        // تعيين هيدر Access-Control-Allow-Methods
        // Specifies the HTTP methods allowed when accessing the resource.
        // يحدد طرق HTTP المسموح بها عند الوصول إلى المورد.
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");

        // Set the Access-Control-Allow-Headers header
        // تعيين هيدر Access-Control-Allow-Headers
        // Indicates which HTTP headers can be used during the actual request.
        // Crucial for allowing headers like 'Content-Type' (for JSON bodies) and 'Authorization' (for API tokens).
        // يشير إلى رؤوس HTTP التي يمكن استخدامها أثناء الطلب الفعلي.
        // ضروري للسماح برؤوس مثل 'Content-Type' (لأجسام JSON) و 'Authorization' (لرموز API).
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN"); // Added X-CSRF-TOKEN if needed

        // Optional: Set the Access-Control-Allow-Credentials header
        // اختياري: تعيين هيدر Access-Control-Allow-Credentials
        // If your frontend needs to send cookies or use HTTP authentication with the API.
        // Not needed if using stateless tokens (like Bearer tokens).
        // إذا كانت الواجهة الأمامية تحتاج إلى إرسال ملفات تعريف الارتباط أو استخدام مصادقة HTTP مع الـ API.
        // غير ضروري إذا كنت تستخدم رموزًا عديمة الحالة (مثل Bearer tokens).
        // header("Access-Control-Allow-Credentials: true");

        // Optional: Set the Access-Control-Max-Age header
        // اختياري: تعيين هيدر Access-Control-Max-Age
        // Specifies how long the results of a preflight request (OPTIONS) can be cached.
        // يحدد المدة التي يمكن فيها تخزين نتائج الطلب التمهيدي (OPTIONS) مؤقتًا.
        // header("Access-Control-Max-Age: 3600"); // 1 hour

        // Handle preflight OPTIONS requests
        // معالجة طلبات OPTIONS التمهيدية
        // Browsers send an OPTIONS request before POST/PUT/DELETE etc., to check CORS permissions.
        // We respond with the allowed methods/headers and exit immediately with a 200 OK status.
        // ترسل المتصفحات طلب OPTIONS قبل POST/PUT/DELETE إلخ، للتحقق من أذونات CORS.
        // نستجيب بالطرق/الرؤوس المسموح بها ونخرج فورًا بحالة 200 OK.
        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
            http_response_code(200); // OK status for OPTIONS
            exit(); // Terminate script execution for OPTIONS requests
        }

        // If it's not an OPTIONS request, the headers are set, and the script continues to the next middleware or controller.
        // إذا لم يكن طلب OPTIONS، يتم تعيين الرؤوس، ويستمر السكربت إلى الـ middleware أو controller التالي.
    }
}
