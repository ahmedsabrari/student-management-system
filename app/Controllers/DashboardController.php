<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Enrollment;

/**
 * DashboardController
 *
 * Manages the main dashboard view, which dynamically changes based on the
 * logged-in user's role (Admin, Teacher, Student).
 */
class DashboardController extends Controller
{
    /**
     * Displays the appropriate dashboard based on the user's role.
     * This is the main entry point for the /dashboard route.
     *
     * @return string The rendered dashboard view.
     */
    public function index(): string
    {
        // Check for user role from the session
        $userRole = Session::get('user_role');

        switch ($userRole) {
            case 'admin':
                $data = $this->getAdminDashboardData();
                return $this->render('dashboard/admin', $data, 'admin');

            case 'teacher':
                $data = $this->getTeacherDashboardData();
                return $this->render('dashboard/teacher', $data, 'admin');

            case 'student':
                $data = $this->getStudentDashboardData();
                // --- FIX THIS LINE ---
                // --- أصلح هذا السطر ---
                // return $this->render('dashboard/student', $data, 'main'); // WRONG
                return $this->render('dashboard/index', $data, 'main'); // CORRECT (Use index.php for student dashboard)

            default:
                // If no role is found or role is unrecognized, log out and redirect to login
                Session::destroy();
                $this->redirect('/login');
                return ''; // Return empty string to satisfy return type
        }
    }

    /**
     * Fetches the data required for the Admin dashboard.
     *
     * @return array An array of statistical data.
     */
    private function getAdminDashboardData(): array
    {
        // Use COUNT(*) for better performance
        // استخدام COUNT(*) لأداء أفضل
        $studentModel = new Student();
        $teacherModel = new Teacher();
        $courseModel = new Course();

        // Ensure models have getTable() method
        // التأكد من أن النماذج تحتوي على دالة getTable()
        $studentCount = $studentModel->query("SELECT COUNT(*) as count FROM {$studentModel->getTable()} WHERE deleted_at IS NULL")->fetch()->count ?? 0;
        $teacherCount = $teacherModel->query("SELECT COUNT(t.id) as count FROM {$teacherModel->getTable()} t JOIN users u ON t.user_id = u.id WHERE u.deleted_at IS NULL")->fetch()->count ?? 0; // Count based on non-deleted users too
        $courseCount = $courseModel->query("SELECT COUNT(*) as count FROM {$courseModel->getTable()} WHERE deleted_at IS NULL")->fetch()->count ?? 0;

        return [
            'studentCount' => $studentCount,
            'teacherCount' => $teacherCount,
            'courseCount' => $courseCount,
        ];
    }

    /**
     * Fetches the data required for the Teacher dashboard.
     *
     * @return array An array of data relevant to the logged-in teacher.
     */
    private function getTeacherDashboardData(): array
    {
        $teacherId = Session::get('teacher_id'); // Assuming teacher_id is stored in session upon login for teachers

        // @TODO: Implement real data fetching logic
        // تنفيذ منطق جلب البيانات الحقيقي
        $courses = [
            // Example data
             ['id' => 1, 'name' => 'Introduction to PHP', 'class_count' => 2, 'student_count' => 45],
             ['id' => 2, 'name' => 'Advanced MySQL', 'class_count' => 1, 'student_count' => 28],
        ];

        return [
            'courses' => $courses,
        ];
    }

    /**
     * Fetches the data required for the Student dashboard.
     *
     * @return array An array of data relevant to the logged-in student.
     */
    private function getStudentDashboardData(): array
    {
        $studentId = Session::get('student_id'); // Assuming student_id is stored in session upon login for students

        // @TODO: Implement real data fetching logic using Enrollment model
        // تنفيذ منطق جلب البيانات الحقيقي باستخدام Enrollment model
         $enrollments = [
            // Example data
            ['course_name' => 'Web Development Fundamentals', 'grade' => 'A', 'attendance' => '95%'],
            ['course_name' => 'Database Design', 'grade' => 'B+', 'attendance' => '92%'],
            ['course_name' => 'Object-Oriented Programming', 'grade' => 'In Progress', 'attendance' => '98%'],
        ];

        return [
            'enrollments' => $enrollments,
        ];
    }


}