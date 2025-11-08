<?php

declare(strict_types=1); // Enforce strict types

namespace App\Middleware;

use App\Helpers\Session;
use App\Helpers\Response;

/**
 * Teacher Authorization Middleware
 *
 * Checks if the currently logged-in user has the 'teacher' role.
 * This middleware assumes that the AuthMiddleware has already run
 * to ensure the user is logged in. If the user is not a teacher,
 * it redirects them to the dashboard with an error message.
 */
class TeacherMiddleware
{
    /**
     * Handle the incoming request.
     * معالجة الطلب الوارد.
     *
     * Verifies that the logged-in user has the 'teacher' role.
     * يتحقق من أن المستخدم المسجل دخوله لديه دور 'teacher'.
     *
     * @return void
     */
    public static function handle(): void
    {
        // 1. Assume AuthMiddleware has run, so user_id should exist.
        //    However, a redundant check adds robustness.
        // ١. افتراض أن AuthMiddleware قد تم تشغيله، لذا يجب أن يكون user_id موجودًا.
        //    لكن، التحقق الإضافي يزيد من المتانة.
        if (!Session::has('user_id')) {
            // Should not happen if AuthMiddleware is applied first, but handle anyway.
            // لا يفترض أن يحدث هذا إذا تم تطبيق AuthMiddleware أولاً، لكن تعامل معه على أي حال.
             // @codeCoverageIgnoreStart
            Response::redirect('/login')
                    ->withFlash('error', 'Authentication required.');
            return; // Exit handled by withFlash
             // @codeCoverageIgnoreEnd
        }

        // 2. Check if the logged-in user's role is 'teacher'.
        // ٢. التحقق مما إذا كان دور المستخدم المسجل دخوله هو 'teacher'.
        $userRole = Session::get('user_role'); // Get role from session

        if (strtolower($userRole ?? '') !== 'teacher') {
            // User is logged in, but not a teacher
            // المستخدم مسجل دخوله، لكنه ليس معلمًا
            
            // Redirect to the main dashboard (or a '403 Forbidden' page)
            // إعادة التوجيه إلى لوحة التحكم الرئيسية (أو صفحة '403 ممنوع')
            Response::redirect('/dashboard') 
                    ->withFlash('error', 'You do not have permission to access this teacher-specific area.');
            // Response::redirect()->withFlash() handles exit()
            return; // Return added for clarity
        }

        // If the user is logged in AND is a teacher, do nothing and let the request proceed.
        // إذا كان المستخدم مسجل دخوله وهو معلم، لا تفعل شيئًا ودع الطلب يستمر.
    }
}