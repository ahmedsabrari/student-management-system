<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Course;
use App\Helpers\Session;

/**
 * EnrollmentController
 *
 * Manages the process of enrolling students into classes.
 */
class EnrollmentController extends Controller
{
    /** @var Enrollment The enrollment model instance. */
    protected $enrollmentModel;

    /**
     * EnrollmentController constructor.
     * Initializes the Enrollment model.
     */
    public function __construct()
    {
        parent::__construct();
        $this->enrollmentModel = new Enrollment();
    }

    /**
     * Display a listing of all enrollments.
     * Handles GET /enrollments
     *
     * @return string The rendered view of the enrollment list.
     */
    public function index(): string
    {
        // @TODO: Create a custom method in EnrollmentModel `getAllDetails()`
        // that JOINS with students, classes, and courses for a readable list.
        $enrollments = $this->enrollmentModel->all();
        
        return $this->render('enrollments/index', ['enrollments' => $enrollments], 'admin');
    }

    /**
     * Show the form for creating a new enrollment.
     * Fetches students and classes to populate dropdowns.
     * Handles GET /enrollments/create
     *
     * @return string The rendered view of the create form.
     */
    public function create(): string
    {
        // @TODO: Optimize this to fetch only active classes and unenrolled students if needed.
        $data = [
            'students' => (new Student())->all(),
            'classes' => (new ClassModel())->all(),
            'courses' => (new Course())->all(), // As requested, though 'classes' model might be sufficient
        ];

        return $this->render('enrollments/create', $data, 'admin');
    }

    /**
     * Store a newly created enrollment in the database.
     * Includes validation for duplicate entries.
     * Handles POST /enrollments/store
     */
    public function store(): void
    {
        $request = new Request();

        // @TODO: Implement CSRF token validation.
        
        $student_id = $request->input('student_id');
        $class_id = $request->input('class_id');

        // @TODO: Replace with a formal Validator class.
        if (empty($student_id) || empty($class_id)) {
            Session::flash('error', 'Student and Class selection are required.');
            $this->redirect('/enrollments/create');
            return;
        }

        // --- Validation: Check for duplicate enrollment ---
        // @TODO: Move this logic into a model method: `isEnrolled(student_id, class_id)`
        $existing = $this->enrollmentModel->query(
            "SELECT id FROM enrollments WHERE student_id = ? AND class_id = ?",
            [$student_id, $class_id]
        )->fetch();

        if ($existing) {
            Session::flash('error', 'This student is already enrolled in this class.');
            $this->redirect('/enrollments/create');
            return;
        }
        
        $data = [
            'student_id' => $student_id,
            'class_id' => $class_id,
            'enrollment_date' => date('Y-m-d H:i:s'), // Set enrollment date to now
            'status' => 'enrolled',
        ];

        if ($this->enrollmentModel->create($data)) {
            Session::flash('success', 'Student enrolled in class successfully.');
            $this->redirect('/enrollments');
        } else {
            Session::flash('error', 'Failed to enroll student. Please try again.');
            $this->redirect('/enrollments/create');
        }
    }

    /**
     * Display the specified enrollment details.
     * Fetches details for the student, class, and course.
     * Handles GET /enrollments/view/{id}
     *
     * @param int|string $id The enrollment ID.
     * @return string The rendered view of the enrollment details.
     */
    public function view($id): string
    {
        $enrollment = $this->enrollmentModel->find($id);

        if (!$enrollment) {
            return $this->view->renderError(404);
        }

        // @TODO: This is an N+1 query problem. This data should be fetched
        // with a single, optimized JOIN query in a custom model method.
        $student = (new Student())->find($enrollment->student_id);
        $class = (new ClassModel())->find($enrollment->class_id);
        $course = (new Course())->find($class->course_id); // Get course from class

        $data = [
            'enrollment' => $enrollment,
            'student' => $student,
            'class' => $class,
            'course' => $course,
        ];

        return $this->render('enrollments/view', $data, 'admin');
    }

    /**
     * Remove the specified enrollment from the database (un-enroll).
     * Handles POST /enrollments/delete/{id}
     *
     * @param int|string $id The enrollment ID.
     */
    public function delete($id): void
    {
        // @TODO: Implement CSRF token validation.

        $enrollment = $this->enrollmentModel->find($id);
        if (!$enrollment) {
            Session::flash('error', 'Enrollment record not found.');
            $this->redirect('/enrollments');
            return;
        }

        // @TODO: Add logic? Should grades be deleted?
        // For now, just delete the enrollment record.
        
        if ($this->enrollmentModel->delete($id)) {
            Session::flash('success', 'Student enrollment has been successfully cancelled.');
        } else {
            Session::flash('error', 'Failed to cancel the enrollment.');
        }
        $this->redirect('/enrollments');
    }
}