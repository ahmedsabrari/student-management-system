<?php

declare(strict_types=1); // Enforce strict types

namespace App\Middleware;

use App\Helpers\Session;
use App\Helpers\Response;
// Import the auth helper function
use function App\Helpers\isLoggedIn; 
use function App\Helpers\currentUserRole;

/**
 * Staff Authorization Middleware
 *
 * Checks if the currently logged-in user has the 'staff' role.
 * This middleware assumes that the AuthMiddleware has already run.
 * If the user is not staff, it redirects them to the dashboard.
 */
class StaffMiddleware
{
    /**
     * Handle the incoming request.
     * معالجة الطلب الوارد.
     *
     * Verifies that the logged-in user has the 'staff' role.
     * يتحقق من أن المستخدم المسجل دخوله لديه دور 'staff'.
     *
     * @return void
     */
    public static function handle(): void
    {
        // 1. Check if user is logged in (should be handled by AuthMiddleware first)
        // ١. التحقق من تسجيل الدخول (يجب أن يتم معالجته بواسطة AuthMiddleware أولاً)
        if (!isLoggedIn()) {
             // @codeCoverageIgnoreStart
            Response::redirect('/login')
                    ->withFlash('error', 'Authentication required.');
            return; // Exit handled by withFlash
             // @codeCoverageIgnoreEnd
        }

        // 2. Check if the logged-in user's role is 'staff'.
        // ٢. التحقق مما إذا كان دور المستخدم المسجل دخوله هو 'staff'.
        // We can also allow 'admin' to access staff routes
        // يمكننا أيضًا السماح لـ 'admin' بالوصول إلى مسارات الموظفين
        $userRole = strtolower(currentUserRole() ?? '');
        
        if ($userRole !== 'staff' && $userRole !== 'admin') {
            // User is logged in, but not staff (or admin)
            // المستخدم مسجل دخوله، لكنه ليس موظفًا (أو مديرًا)
            
            Response::redirect('/dashboard') 
                    ->withFlash('error', 'You do not have permission to access this staff-only area.');
            // Response::redirect()->withFlash() handles exit()
            return; // Return added for clarity
        }

        // If the user is logged in AND is staff (or admin), let the request proceed.
        // إذا كان المستخدم مسجل دخوله وهو موظف (أو مدير)، دع الطلب يستمر.
    }
}