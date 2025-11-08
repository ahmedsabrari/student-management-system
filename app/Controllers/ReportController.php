<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Student;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\StudentFee;
use App\Models\Department;

/**
 * ReportController
 *
 * Generates various data-driven reports for administrative review.
 */
class ReportController extends Controller
{
    /**
     * ReportController constructor.
     *
     * @TODO: Apply middleware here to ensure only admin/staff can access reports.
     * e.g., $this->middleware(AdminMiddleware::class);
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generates a report of all students, filterable by department and status.
     * Handles GET /reports/students
     *
     * @return string The rendered student report view.
     */
    public function students(): string
    {
        $request = new Request();
        $filters = [
            'department_id' => $request->input('department_id'),
            'status' => $request->input('status'),
        ];

        // @TODO: This logic should be moved to a ReportService or StudentModel.
        $studentModel = new Student();
        $sql = "SELECT s.*, d.name as department_name 
                FROM students s 
                LEFT JOIN departments d ON s.department_id = d.id 
                WHERE 1=1";
        
        $params = [];
        if (!empty($filters['department_id'])) {
            $sql .= " AND s.department_id = ?";
            $params[] = $filters['department_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND s.status = ?";
            $params[] = $filters['status'];
        }
        $sql .= " ORDER BY s.last_name, s.first_name";

        $reportData = $studentModel->query($sql, $params)->fetchAll();
        $departments = (new Department())->all();

        $data = [
            'reportData' => $reportData,
            'filters' => $filters,
            'departments' => $departments
        ];
        
        return $this->render('reports/students', $data, 'admin');
    }

    /**
     * Generates a report of all courses and their total enrollment count.
     * Handles GET /reports/courses
     *
     * @return string The rendered course report view.
     */
    public function courses(): string
    {
        // @TODO: Move this complex query to a ReportService or CourseModel.
        $courseModel = new Course();
        $sql = "SELECT c.id, c.name, c.code, COUNT(e.id) as enrollment_count
                FROM courses c
                LEFT JOIN classes cl ON c.id = cl.course_id
                LEFT JOIN enrollments e ON cl.id = e.class_id
                GROUP BY c.id, c.name, c.code
                ORDER BY enrollment_count DESC, c.name";
        
        $reportData = $courseModel->query($sql)->fetchAll();

        return $this->render('reports/courses', ['reportData' => $reportData], 'admin');
    }

    /**
     * Generates a detailed attendance report, filterable by class, student, or date.
     * This replaces the basic report() method from AttendanceController.
     * Handles GET /reports/attendance
     *
     * @return string The rendered attendance report view.
     */
    public function attendance(): string
    {
        $request = new Request();
        $filters = [
            'class_id' => $request->input('class_id'),
            'student_id' => $request->input('student_id'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        // @TODO: This is extremely complex business logic.
        // This query needs to join students, classes, and attendance,
        // then calculate percentages. This MUST be moved to a ReportService.
        // $reportData = $this->reportService->getAttendanceReport($filters);

        // Simulating data for now.
        $reportData = [
            ['student_name' => 'John Doe', 'class_name' => 'Web Dev', 'present' => 18, 'absent' => 2, 'percentage' => '90%'],
            ['student_name' => 'Jane Smith', 'class_name' => 'Database', 'present' => 20, 'absent' => 0, 'percentage' => '100%']
        ];
        
        $data = [
            'reportData' => $reportData,
            'filters' => $filters,
            'classes' => (new \App\Models\ClassModel())->all(),
            'students' => (new Student())->all()
        ];

        return $this->render('reports/attendance', $data, 'admin');
    }

    /**
     * Generates a financial report showing student balances.
     * Handles GET /reports/financial
     *
     * @return string The rendered financial report view.
     */
    public function financial(): string
    {
        // @TODO: Move this complex query to a ReportService or StudentFeeModel.
        $studentFeeModel = new StudentFee();
        
        $sql = "SELECT 
                    s.id as student_id, 
                    s.student_number,
                    s.first_name, 
                    s.last_name,
                    COALESCE(SUM(sf.amount_due), 0) as total_due,
                    COALESCE(SUM(sf.amount_paid), 0) as total_paid,
                    (COALESCE(SUM(sf.amount_due), 0) - COALESCE(SUM(sf.amount_paid), 0)) as balance
                FROM students s
                LEFT JOIN student_fees sf ON s.id = sf.student_id
                GROUP BY s.id, s.student_number, s.first_name, s.last_name
                HAVING balance != 0
                ORDER BY balance DESC, s.last_name";
        
        $reportData = $studentFeeModel->query($sql)->fetchAll();
        
        return $this->render('reports/financial', ['reportData' => $reportData], 'admin');
    }
}