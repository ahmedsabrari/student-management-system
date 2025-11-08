<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Student;
use App\Models\Guardian;
use App\Helpers\Session;

use App\Models\Enrollment; // <-- ADDED
use App\Models\Grade; // <-- ADDED
use App\Models\Attendance; // <-- ADDED
use App\Models\AssignmentSubmission; // <-- ADDED


/**
 * StudentController
 *
 * Manages all actions related to students, including listing, creating,
 * editing, viewing, and deleting student records.
 */
class StudentController extends Controller
{
    /** @var Student The student model instance. */
    protected Student $studentModel; // <-- FIXED Type Hint

    /** @var Guardian The guardian model instance. */
    protected Guardian $guardianModel; // <-- FIXED Type Hint

    /** @var Enrollment The enrollment model instance. */
    protected Enrollment $enrollmentModel;

    /**
     * StudentController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        // FIXED TYPO: Changed $this. to $this->
        // إصلاح الخطأ المطبعي
        $this->studentModel = new Student();
        $this->guardianModel = new Guardian();
        $this->enrollmentModel = new Enrollment(); 
    }

    // --- ADMIN-FACING METHODS ---
    // --- (All existing admin methods like index, create, store, edit, show, update, destroy go here) ---

    /**
     * Display a listing of all students.
     * Handles GET /students
     */
    public function index(): string
    {
        $students = $this->studentModel->all();
        return $this->render('students/index', ['students' => $students], 'admin');
    }

    /**
     * Show the form for creating a new student.
     * Handles GET /students/create
     */
    public function create(): string
    {
        $departments = (new \App\Models\Department())->all();
        return $this->render('students/create', ['departments' => $departments], 'admin');
    }

    /**
     * Store a newly created student in the database.
     * Handles POST /students
     */
    public function store(): void
    {
        $request = new Request();
        // @TODO: Implement a robust Validator class for sanitation and validation.
        $studentData = [
            'student_number' => $request->input('student_number'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'gender' => $request->input('gender'),
            'date_of_birth' => $request->input('date_of_birth'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'department_id' => $request->input('department_id'), // <-- Added department_id from create form
            'status' => 'active', // <-- Added default status
        ];

        if ($this->studentModel->create($studentData)) {
            Session::flash('success', 'Student record created successfully.');
            $this->redirect('/students');
        } else {
            Session::flash('error', 'Failed to create student record. Please try again.');
            // @TODO: Pass back old input to the form
            $this->redirect('/students/create');
        }
    }

    /**
     * Display the specified student's profile.
     * Handles GET /students/{id}
     */
    public function show($id): string
    {
        $student = $this->studentModel->find($id);

        if (!$student) {
            // FIXED TYPO: Changed $this. to $this->
            return $this->view->renderError(404);
        }

        $guardians = $this->guardianModel->getByStudent($id); // Fetch guardians

        return $this->render('students/view', [
            'student' => $student, 
            'guardians' => $guardians
        ], 'admin');
    }

    /**
     * Show the form for editing the specified student.
     * Handles GET /students/{id}/edit
     */
    public function edit($id): string
    {
        $student = $this->studentModel->find($id);

        if (!$student) {
             // FIXED TYPO: Changed $this. to $this->
            return $this->view->renderError(404);
        }
        
        $departments = (new \App\Models\Department())->all(); // Fetch departments for dropdown

        return $this->render('students/edit', [
            'student' => $student,
            'departments' => $departments // Pass departments to the view
        ], 'admin');
    }

    /**
     * Update the specified student in the database.
     * Handles POST /students/{id}/update
     */
    public function update($id): void
    {
        $request = new Request();
        // @TODO: Implement validation
        $studentData = [
            'student_number' => $request->input('student_number'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'gender' => $request->input('gender'),
            'date_of_birth' => $request->input('date_of_birth'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'status' => $request->input('status'),
            'department_id' => $request->input('department_id'), // <-- Added department_id from edit form
        ];
        
        if ($this->studentModel->update($id, $studentData)) {
            Session::flash('success', 'Student record updated successfully.');
            $this->redirect('/students/' . $id);
        } else {
            Session::flash('error', 'Failed to update student record or no changes were made.');
            $this->redirect('/students/' . $id . '/edit');
        }
    }

    /**
     * Remove the specified student from the database.
     * Handles POST /students/{id}/delete
     */
    public function destroy($id): void
    {
        if ($this->studentModel->delete($id)) {
            Session::flash('success', 'Student record deleted successfully.');
        } else {
            Session::flash('error', 'Failed to delete student record.');
        }
        $this->redirect('/students');
    }

    // --- STUDENT-FACING METHODS (FIXED TYPOS) ---
    // --- (دوال خاصة بالطالب - تم إصلاح الأخطاء المطبعية) ---
    
    /**
     * Show the logged-in student's courses.
     * Handles GET /my-courses
     */
    public function myCourses(): string
    {
        $studentId = Session::get('student_id');
        
        // @TODO: Create findEnrollmentsByStudent($studentId) in EnrollmentService/Model
        // $enrollments = $this->enrollmentModel->findEnrollmentsByStudent($studentId);
        $enrollments = []; // Placeholder for now
        
        return $this->render('students/my_courses', [ // <-- FIXED: Changed . to ->
            'title' => 'My Courses',
            'enrollments' => $enrollments
        ], 'main'); // Use the 'main' (student) layout
    }

    /**
     * Show the logged-in student's grades.
     * Handles GET /my-grades
     */
    public function myGrades(): string
    {
        $studentId = Session::get('student_id');

        
        // @TODO: Create getStudentGrades($studentId) in GradeService/Model
        // $grades = (new Grade())->getStudentGrades($studentId);
         $grades = []; // Placeholder for now
         
        return $this->render('students/my_grades', [ // <-- FIXED: Changed . to ->
            'title' => 'My Grades',
            'grades' => $grades
        ], 'main');
    }

    /**
     * Show the logged-in student's attendance.
     * Handles GET /my-attendance
     */
    public function myAttendance(): string
    {
         $studentId = Session::get('student_id');

        
        // @TODO: Create getStudentAttendance($studentId) in AttendanceService/Model
         $attendance = []; // Placeholder for now
         
        return $this->render('students/my_attendance', [ // <-- FIXED: Changed . to ->
            'title' => 'My Attendance',
            'attendance' => $attendance
        ], 'main');
    }

    /**
     * Show the logged-in student's assignments.
     * Handles GET /my-assignments
     */
    public function myAssignments(): string
    {
         $studentId = Session::get('student_id');
        
        // @TODO: Create getStudentSubmissions($studentId) in AssignmentSubmissionModel
         $submissions = []; // Placeholder for now
         
        return $this->render('students/my_assignments', [ // <-- FIXED: Changed . to ->
            'title' => 'My Assignments',
            'submissions' => $submissions
        ], 'main');
    }
}

