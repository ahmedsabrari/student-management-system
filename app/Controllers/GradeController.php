<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Grade;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Course;
use App\Models\ClassModel;
use App\Helpers\Session;

/**
 * GradeController
 *
 * Manages the entry, update, and reporting of student grades.
 */
class GradeController extends Controller
{
    /** @var Grade The grade model instance. */
    protected $gradeModel;

    /** @var Enrollment The enrollment model instance. */
    protected $enrollmentModel;

    /**
     * GradeController constructor.
     * Initializes the necessary models.
     */
    public function __construct()
    {
        parent::__construct();
        $this->gradeModel = new Grade();
        $this->enrollmentModel = new Enrollment();
    }

    /**
     * Display a listing of all recorded grades.
     * Handles GET /grades
     *
     * @return string The rendered view of the grades list.
     */
    public function index(): string
    {
        // @TODO: This is a complex query. Create a custom method in GradeModel
        // `getAllGradeDetails()` that JOINS grades, enrollments, students, classes,
        // and courses to display a comprehensive list.
        $grades = $this->gradeModel->all();
        
        return $this->render('grades/index', ['grades' => $grades], 'admin');
    }

    /**
     * Show the form for managing (adding/editing) a grade for a specific enrollment.
     * Handles GET /grades/manage/{id} (where {id} is the Enrollment ID)
     *
     * @param int|string $enrollment_id The enrollment ID to manage grades for.
     * @return string The rendered view of the grade management form.
     */
    public function manage($enrollment_id): string
    {
        $enrollment = $this->enrollmentModel->find($enrollment_id);
        if (!$enrollment) {
            return $this->view->renderError(404);
        }

        // Find the existing grade for this enrollment, if any
        // @TODO: This logic should be in the model: `Grade::findByEnrollmentId($id)`
        $grade = $this->gradeModel->query(
            "SELECT * FROM grades WHERE enrollment_id = ?",
            [$enrollment_id]
        )->fetch();

        // @TODO: This N+1 query problem should be optimized with a JOIN in a custom model method
        $student = (new Student())->find($enrollment->student_id);
        $class = (new ClassModel())->find($enrollment->class_id);
        $course = (new Course())->find($class->course_id);

        $data = [
            'enrollment' => $enrollment,
            'grade' => $grade, // This will be null/false if no grade is entered yet
            'student' => $student,
            'class' => $class,
            'course' => $course
        ];

        return $this->render('grades/manage', $data, 'admin');
    }

    /**
     * Store (create or update) a grade for a specific enrollment.
     * Handles POST /grades/store/{id} (where {id} is the Enrollment ID)
     *
     * @param int|string $enrollment_id The enrollment ID to store the grade for.
     */
    public function store($enrollment_id): void
    {
        $enrollment = $this->enrollmentModel->find($enrollment_id);
        if (!$enrollment) {
            Session::flash('error', 'Enrollment record not found.');
            $this->redirect('/grades');
            return;
        }

        $request = new Request();
        // @TODO: Implement CSRF token validation.
        // @TODO: Implement a robust Validator for 'grade' (e.g., A, B+, C) and 'grade_points' (e.g., 0.00-4.00).

        $gradeData = [
            'enrollment_id' => $enrollment_id,
            'grade' => $request->input('grade'),
            'grade_points' => $request->input('grade_points'),
            'remarks' => $request->input('remarks'),
            'recorded_at' => date('Y-m-d H:i:s') // Set or update the recorded timestamp
        ];

        // Find existing grade for this enrollment
        $existingGrade = $this->gradeModel->query(
            "SELECT id FROM grades WHERE enrollment_id = ?",
            [$enrollment_id]
        )->fetch();

        $success = false;
        if ($existingGrade) {
            // Grade exists: UPDATE it
            unset($gradeData['enrollment_id']); // No need to update the enrollment_id
            unset($gradeData['recorded_at']); // `recorded_at` is TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            $success = $this->gradeModel->update($existingGrade->id, $gradeData);
        } else {
            // Grade does not exist: CREATE it
            $success = $this->gradeModel->create($gradeData);
        }

        if ($success) {
            Session::flash('success', 'Grade saved successfully.');
        } else {
            Session::flash('error', 'Failed to save grade or no changes were made.');
        }
        
        $this->redirect('/grades/manage/' . $enrollment_id);
    }

    /**
     * Display a comprehensive grade report.
     * Handles GET /grades/report
     *
     * @return string The rendered view of the grade report.
     */
    public function report(): string
    {
        // @TODO: This is a complex business logic task.
        // This logic should be moved to a dedicated ReportService or GradeService.
        // The service would fetch all students, their enrollments, grades, and calculate GPAs.
        // $reportService = new ReportService();
        // $reportData = $reportService->getGradeReport();

        // For now, we simulate the data.
        $reportData = [
            ['student_name' => 'John Doe', 'gpa' => 3.75, 'completed_credits' => 12],
            ['student_name' => 'Jane Smith', 'gpa' => 3.90, 'completed_credits' => 15],
            ['student_name' => 'Mike Johnson', 'gpa' => 3.20, 'completed_credits' => 12],
        ];

        return $this->render('grades/report', ['reportData' => $reportData], 'admin');
    }
}