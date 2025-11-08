<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\ClassModel;
use App\Models\Student;
use App\Helpers\Session;

/**
 * AttendanceController
 *
 * Manages taking and recording student attendance for classes.
 */
class AttendanceController extends Controller
{
    /** @var Attendance The attendance model instance. */
    protected $attendanceModel;

    /** @var ClassModel The class model instance. */
    protected $classModel;

    /** @var Enrollment The enrollment model instance. */
    protected $enrollmentModel;

    /**
     * AttendanceController constructor.
     * Initializes the necessary models.
     */
    public function __construct()
    {
        parent::__construct();
        $this->attendanceModel = new Attendance();
        $this->classModel = new ClassModel();
        $this->enrollmentModel = new Enrollment();
    }

    /**
     * Display a list of classes to manage attendance for.
     * Handles GET /attendance
     *
     * @return string The rendered view.
     */
    public function index(): string
    {
        // @TODO: Fetch classes taught by the logged-in teacher if the user is a teacher.
        $classes = $this->classModel->all();
        return $this->render('attendance/index', ['classes' => $classes], 'admin');
    }

    /**
     * Show the form for taking attendance for a specific class.
     * Handles GET /attendance/take/{class_id}
     *
     * @param int|string $class_id The class ID.
     * @return string The rendered view of the attendance form.
     */
    public function take($class_id): string
    {
        $class = $this->classModel->find($class_id);
        if (!$class) {
            return $this->view->renderError(404);
        }

        // @TODO: This is a key query. Create a custom method in EnrollmentModel:
        // `findStudentsByClassId($class_id)` that JOINS with the `students` table.
        // For now, we fetch enrollments and then students (N+1 problem).
        $enrollments = $this->enrollmentModel->query(
            "SELECT e.*, s.first_name, s.last_name 
             FROM enrollments e
             JOIN students s ON e.student_id = s.id
             WHERE e.class_id = ?",
            [$class_id]
        )->fetchAll();
        
        $data = [
            'class' => $class,
            'enrollments' => $enrollments,
        ];

        return $this->render('attendance/take', $data, 'admin');
    }

    /**
     * Store the attendance records for a specific class on a specific date.
     * This method performs an "upsert" (update or insert).
     * Handles POST /attendance/store/{class_id}
     *
     * @param int|string $class_id The class ID.
     */
    public function store($class_id): void
    {
        $class = $this->classModel->find($class_id);
        if (!$class) {
            Session::flash('error', 'Class not found.');
            $this->redirect('/attendance');
            return;
        }

        $request = new Request();
        // @TODO: Implement CSRF token validation.
        
        $attendanceData = $request->input('attendance', []);
        $attendanceDate = $request->input('attendance_date');

        // @TODO: Use Validator class
        if (empty($attendanceData) || empty($attendanceDate)) {
            Session::flash('error', 'No attendance data submitted or date was missing.');
            $this->redirect('/attendance/take/' . $class_id);
            return;
        }

        // @TODO: This loop should be wrapped in a database transaction.
        $successCount = 0;
        foreach ($attendanceData as $enrollment_id => $status) {
            // Check if a record already exists for this enrollment_id and date
            $existing = $this->attendanceModel->query(
                "SELECT id FROM attendance WHERE enrollment_id = ? AND date = ?",
                [$enrollment_id, $attendanceDate]
            )->fetch();

            $data = [
                'enrollment_id' => $enrollment_id,
                'date' => $attendanceDate,
                'status' => $status,
                'note' => $request->input("notes[{$enrollment_id}]", ''), // Optional notes
            ];

            if ($existing) {
                // UPDATE
                unset($data['enrollment_id'], $data['date']); // No need to update keys
                if ($this->attendanceModel->update($existing->id, $data)) {
                    $successCount++;
                }
            } else {
                // CREATE
                if ($this->attendanceModel->create($data)) {
                    $successCount++;
                }
            }
        }

        if ($successCount == count($attendanceData)) {
            Session::flash('success', 'Attendance recorded successfully for ' . $successCount . ' students.');
        } else {
            Session::flash('warning', 'Attendance partially recorded. ' . $successCount . ' records saved.');
        }
        
        $this->redirect('/attendance/take/' . $class_id);
    }

    /**
     * Display a comprehensive attendance report.
     * Handles GET /attendance/report
     *
     * @return string The rendered view of the attendance report.
     */
    public function report(): string
    {
        // @TODO: This is complex business logic.
        // Move to a dedicated ReportService or AttendanceService.
        // The service would fetch all students, their attendance records,
        // and calculate percentages (e.g., % present, % absent).
        // $reportService = new ReportService();
        // $reportData = $reportService->getAttendanceReport();

        // For now, we simulate the data.
        $reportData = [
            ['student_name' => 'John Doe', 'class_name' => 'Web Development', 'present' => 18, 'absent' => 2, 'percentage' => '90%'],
            ['student_name' => 'Jane Smith', 'class_name' => 'Database Design', 'present' => 20, 'absent' => 0, 'percentage' => '100%'],
        ];

        return $this->render('attendance/report', ['reportData' => $reportData], 'admin');
    }
}