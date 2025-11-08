<?php

/**
 * Application Routes
 *
 * This file defines all the HTTP routes and applies the necessary
 * middleware (Auth, Admin, CSRF) for each route or group.
 */

// --- Import All Controllers ---
use App\Controllers\Api\CourseApiController;
use App\Controllers\Api\StudentApiController;
use App\Controllers\AttendanceController;
use App\Controllers\AuthController;
use App\Controllers\ClassController;
use App\Controllers\CourseController;
use App\Controllers\DashboardController;
use App\Controllers\DepartmentController;
use App\Controllers\EnrollmentController;
use App\Controllers\GradeController;
use App\Controllers\ReportController;
use App\Controllers\SettingsController;
use App\Controllers\StudentController;
use App\Controllers\TeacherController;

// --- Import All Middleware ---
use App\Middleware\AdminMiddleware;
use App\Middleware\ApiAuthMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\StudentMiddleware;
use App\Middleware\TeacherMiddleware;

// The $router variable is injected by App\Core\App

// =================================================================
// --- Public Routes (No Authentication Needed) ---
// =================================================================

// Homepage / Landing Page
$router->get('/', function() {
    if (\App\Helpers\Session::has('user_id')) {
        // Use call_user_func to ensure $this context is correct if needed, or just instantiate
        return (new DashboardController())->index();
    }
    return (new AuthController())->showLoginForm();
});

// Authentication Routes
$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->get('/register', [AuthController::class, 'showRegistrationForm']);

$router->post('/login', function() {
    CsrfMiddleware::handle();
    return (new AuthController())->login();
});
$router->post('/register', function() {
    CsrfMiddleware::handle();
    return (new AuthController())->register();
});


// =================================================================
// --- Authenticated Routes (Must be logged in) ---
// =================================================================

// Logout (requires auth + csrf)
$router->post('/logout', function() {
    AuthMiddleware::handle();
    CsrfMiddleware::handle();
    return (new AuthController())->logout();
});

// Dashboard (Basic login required)
$router->get('/dashboard', function() {
    AuthMiddleware::handle();
    return (new DashboardController())->index();
});

// User Profile Settings (Any logged-in user)
$router->get('/settings/profile', function() {
    AuthMiddleware::handle();
    // Assuming 'profile' is a method in SettingsController
    return (new SettingsController())->profile(); 
});
$router->post('/settings/profile', function() {
    AuthMiddleware::handle();
    CsrfMiddleware::handle();
     // Assuming 'updateProfile' is a method in SettingsController
    return (new SettingsController())->updateProfile();
});
$router->post('/settings/change-password', function() {
    AuthMiddleware::handle();
    CsrfMiddleware::handle();
     // Assuming 'changePassword' is a method in SettingsController
    return (new SettingsController())->changePassword();
});

// =================================================================
// --- Student-Specific Routes (FIXED: Added missing routes) ---
// --- (إصلاح: تمت إضافة المسارات المفقودة) ---
// =================================================================
$router->get('/my-courses', function() {
    AuthMiddleware::handle();
    StudentMiddleware::handle();
    return (new StudentController())->myCourses(); // Assumes myCourses() exists in StudentController
});
$router->get('/my-grades', function() {
    AuthMiddleware::handle();
    StudentMiddleware::handle();
    return (new StudentController())->myGrades(); // Assumes myGrades() exists
});
$router->get('/my-attendance', function() {
    AuthMiddleware::handle();
    StudentMiddleware::handle();
    return (new StudentController())->myAttendance(); // Assumes myAttendance() exists
});
$router->get('/my-assignments', function() {
    AuthMiddleware::handle();
    StudentMiddleware::handle();
    return (new StudentController())->myAssignments(); // Assumes myAssignments() exists
});


// =================================================================
// --- Teacher Routes (Auth + Teacher Role) ---
// =================================================================

$router->get('/grades/manage/{id}', function($id) { // <-- FIXED: Added dynamic ID
    AuthMiddleware::handle();
    TeacherMiddleware::handle(); // Must be a Teacher
    return (new GradeController())->manage($id); 
});
$router->post('/grades/store/{id}', function($id) { // <-- FIXED: Added dynamic ID
    AuthMiddleware::handle();
    TeacherMiddleware::handle();
    CsrfMiddleware::handle();
    return (new GradeController())->store($id);
});

$router->get('/attendance/take/{id}', function($id) { // <-- FIXED: Renamed route and added ID
    AuthMiddleware::handle();
    TeacherMiddleware::handle();
    return (new AttendanceController())->take($id);
});
$router->post('/attendance/store/{id}', function($id) { // <-- FIXED: Added dynamic ID
    AuthMiddleware::handle();
    TeacherMiddleware::handle();
    CsrfMiddleware::handle();
    return (new AttendanceController())->store($id);
});


// =================================================================
// --- Admin Routes (Auth + Admin Role) ---
// =================================================================

// Student Management (Admin)
$router->get('/students', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new StudentController())->index(); });
$router->get('/students/create', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new StudentController())->create(); });
$router->post('/students', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new StudentController())->store(); });
$router->get('/students/{id}', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new StudentController())->show($id); });
$router->get('/students/{id}/edit', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new StudentController())->edit($id); });
$router->post('/students/{id}/update', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new StudentController())->update($id); });
$router->post('/students/{id}/delete', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new StudentController())->destroy($id); });

// Teacher Management (Admin)
$router->get('/teachers', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new TeacherController())->index(); });
$router->get('/teachers/create', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new TeacherController())->create(); });
$router->post('/teachers', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new TeacherController())->store(); });
$router->get('/teachers/{id}', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new TeacherController())->show($id); });
$router->get('/teachers/{id}/edit', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new TeacherController())->edit($id); });
$router->post('/teachers/{id}/update', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new TeacherController())->update($id); });
$router->post('/teachers/{id}/delete', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new TeacherController())->destroy($id); });

// Course Management (Admin)
$router->get('/courses', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new CourseController())->index(); });
$router->get('/courses/create', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new CourseController())->create(); });
$router->post('/courses', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new CourseController())->store(); });
$router->get('/courses/{id}', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new CourseController())->show($id); });
$router->get('/courses/{id}/edit', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new CourseController())->edit($id); });
$router->post('/courses/{id}/update', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new CourseController())->update($id); });
$router->post('/courses/{id}/delete', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new CourseController())->destroy($id); });

// Class Management (Admin)
$router->get('/classes', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new ClassController())->index(); });
$router->get('/classes/create', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new ClassController())->create(); });
$router->post('/classes', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new ClassController())->store(); });
$router->get('/classes/{id}', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new ClassController())->show($id); });
$router->get('/classes/{id}/edit', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new ClassController())->edit($id); });
$router->post('/classes/{id}/update', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new ClassController())->update($id); });
$router->post('/classes/{id}/delete', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new ClassController())->destroy($id); });

// Department Management (Admin)
$router->get('/departments', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new DepartmentController())->index(); });
$router->get('/departments/create', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new DepartmentController())->create(); });
$router->post('/departments', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new DepartmentController())->store(); });
$router->get('/departments/{id}', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new DepartmentController())->show($id); }); // <-- Added show route
$router->get('/departments/{id}/edit', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new DepartmentController())->edit($id); });
$router->post('/departments/{id}/update', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new DepartmentController())->update($id); });
$router->post('/departments/{id}/delete', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new DepartmentController())->destroy($id); });

// Enrollment Management (Admin)
$router->get('/enrollments', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new EnrollmentController())->index(); });
$router->get('/enrollments/create', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new EnrollmentController())->create(); }); // <-- Added create route
$router->post('/enrollments', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new EnrollmentController())->store(); });
$router->get('/enrollments/view/{id}', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new EnrollmentController())->view($id); }); // <-- Added view route
$router->post('/enrollments/delete/{id}', function($id) { AuthMiddleware::handle(); AdminMiddleware::handle(); CsrfMiddleware::handle(); return (new EnrollmentController())->delete($id); }); // <-- Added delete route

// Reports (Admin)
$router->get('/reports/attendance', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new ReportController())->attendance(); });
$router->get('/reports/grades', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new ReportController())->grades(); });
$router->get('/reports/students', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new ReportController())->students(); });
$router->get('/reports/financial', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new ReportController())->financial(); });

// System Settings (Admin)
$router->get('/settings', function() { AuthMiddleware::handle(); AdminMiddleware::handle(); return (new SettingsController())->index(); });
$router->post('/settings/update', function() { // <-- FIXED route
    AuthMiddleware::handle();
    AdminMiddleware::handle();
    CsrfMiddleware::handle();
    return (new SettingsController())->updateSystemSettings(); // <-- FIXED method name
});


// =================================================================
// --- API Routes (Stateless, Token-Based) ---
// =================================================================
// (Apply CORS and ApiAuth to all API routes)

$router->get('/api/students', function() { CorsMiddleware::handle(); ApiAuthMiddleware::handle(); return (new StudentApiController())->index(); });
$router->get('/api/students/{id}', function($id) { CorsMiddleware::handle(); ApiAuthMiddleware::handle(); return (new StudentApiController())->show($id); });
$router->post('/api/students', function() { CorsMiddleware::handle(); ApiAuthMiddleware::handle(); return (new StudentApiController())->store(); });
$router->put('/api/students/{id}', function($id) { CorsMiddleware::handle(); ApiAuthMiddleware::handle(); return (new StudentApiController())->update($id); });
$router->delete('/api/students/{id}', function($id) { CorsMiddleware::handle(); ApiAuthMiddleware::handle(); return (new StudentApiController())->destroy($id); });

$router->get('/api/courses', function() { CorsMiddleware::handle(); ApiAuthMiddleware::handle(); return (new CourseApiController())->index(); });
$router->get('/api/courses/{id}', function($id) { CorsMiddleware::handle(); ApiAuthMiddleware::handle(); return (new CourseApiController())->show($id); });
// (Add POST, PUT, DELETE for API Courses as needed)

