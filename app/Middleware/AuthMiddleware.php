<?php

declare(strict_types=1); // Enforce strict types

namespace App\Middleware;

use App\Helpers\Session;
use App\Helpers\Response;

/**
 * Authentication Middleware
 *
 * Checks if the user is currently authenticated (logged in).
 * If not authenticated, it redirects the user to the login page
 * with a flash message. This middleware should be applied to routes
 * that require a logged-in user.
 */
class AuthMiddleware
{
    /**
     * Handle the incoming request.
     * معالجة الطلب الوارد.
     *
     * Checks for the presence of 'user_id' in the session. If not found,
     * redirects to the login page. Otherwise, allows the request to proceed.
     * يتحقق من وجود 'user_id' في الجلسة. إذا لم يتم العثور عليه،
     * يعيد التوجيه إلى صفحة تسجيل الدخول. خلاف ذلك، يسمح للطلب بالمتابعة.
     *
     * @return void
     */
    public static function handle(): void
    {
        // Ensure session is started (Session helper handles this internally)
        // التأكد من بدء الجلسة (مساعد الجلسة يتعامل مع هذا داخليًا)
        
        if (!Session::has('user_id')) {
            // User is not logged in
            // المستخدم غير مسجل دخوله
            
            // Use the Response helper to redirect with a flash message
            // استخدام مساعد الاستجابة لإعادة التوجيه مع رسالة فلاش
            Response::redirect('/login')
                    ->withFlash('error', 'You must be logged in to access this page.');
                    
            // Response::redirect()->withFlash() handles exit() automatically.
            // الدالة Response::redirect()->withFlash() تتعامل مع exit() تلقائيًا.
        }

        // If Session::has('user_id') is true, do nothing and let the request continue.
        // إذا كانت Session::has('user_id') صحيحة، لا تفعل شيئًا ودع الطلب يستمر.
    }
}