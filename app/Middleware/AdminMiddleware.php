<?php

declare(strict_types=1); // Enforce strict types

namespace App\Middleware;

use App\Helpers\Session;
use App\Helpers\Response;

/**
 * Admin Authorization Middleware
 *
 * Checks if the currently logged-in user has the 'admin' role.
 * If the user is not logged in or does not have the admin role,
 * it redirects them to an appropriate page (login or dashboard)
 * with a flash message. This middleware should be applied *after*
 * the AuthMiddleware to routes restricted to administrators.
 */
class AdminMiddleware
{
    /**
     * Handle the incoming request.
     * معالجة الطلب الوارد.
     *
     * Verifies that the user is logged in AND has the 'admin' role.
     * يتحقق من أن المستخدم مسجل دخوله ولديه دور 'admin'.
     *
     * @return void
     */
    public static function handle(): void
    {
        // 1. First, ensure the user is logged in.
        //    (Alternatively, assume AuthMiddleware ran before this)
        // ١. أولاً، التأكد من أن المستخدم مسجل دخوله.
        //    (أو افتراض أن AuthMiddleware قد تم تشغيله قبل هذا)
        if (!Session::has('user_id')) {
            // Not logged in - redirect to login
            // غير مسجل دخوله - إعادة توجيه إلى صفحة الدخول
            Response::redirect('/login')
                    ->withFlash('error', 'Please log in to access this area.');
            // Response::redirect()->withFlash() handles exit()
            return; // Return added for clarity, though exit() is called
        }

        // 2. Check if the logged-in user's role is 'admin'.
        // ٢. التحقق مما إذا كان دور المستخدم المسجل دخوله هو 'admin'.
        $userRole = Session::get('user_role'); // Get role from session

        if (strtolower($userRole ?? '') !== 'admin') {
            // User is logged in, but not an admin
            // المستخدم مسجل دخوله، لكنه ليس مديرًا
            
            // Redirect to the main dashboard (or a 'forbidden' page)
            // إعادة التوجيه إلى لوحة التحكم الرئيسية (أو صفحة 'ممنوع')
            Response::redirect('/dashboard') 
                    ->withFlash('error', 'You do not have permission to access this page.');
            // Response::redirect()->withFlash() handles exit()
            return; // Return added for clarity
        }

        // If the user is logged in AND is an admin, do nothing and let the request proceed.
        // إذا كان المستخدم مسجل دخوله وهو مدير، لا تفعل شيئًا ودع الطلب يستمر.
    }
}