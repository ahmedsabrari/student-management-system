<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Department;
use App\Helpers\Session;

/**
 * CourseController
 *
 * Manages all CRUD operations for academic courses.
 */
class CourseController extends Controller
{
    /** @var Course The course model instance. */
    protected $courseModel;

    /**
     * CourseController constructor.
     * Initializes necessary models.
     */
    public function __construct()
    {
        parent::__construct();
        $this->courseModel = new Course();
    }

    /**
     * Display a listing of all courses.
     * Handles GET /courses
     *
     * @return string The rendered view of the course list.
     */
    public function index(): string
    {
        $courses = $this->courseModel->all();
        return $this->render('courses/index', ['courses' => $courses], 'admin');
    }

    /**
     * Show the form for creating a new course.
     * Handles GET /courses/create
     *
     * @return string The rendered view of the create form.
     */
    public function create(): string
    {
        // Fetch related data needed for the form, like teachers and departments
        $teacherModel = new Teacher();
        $departmentModel = new Department();

        $data = [
            'teachers' => $teacherModel->all(),
            'departments' => $departmentModel->all(),
        ];

        return $this->render('courses/create', $data, 'admin');
    }

    /**
     * Store a newly created course in the database.
     * Handles POST /courses
     */
    public function store(): void
    {
        $request = new Request();

        // @TODO: Implement CSRF token validation.
        // @TODO: Implement a robust Validator class.
        
        $courseData = [
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'credit_hours' => $request->input('credit_hours', 3),
            'department_id' => $request->input('department_id'),
            'teacher_id' => $request->input('teacher_id'),
        ];

        if ($this->courseModel->create($courseData)) {
            Session::flash('success', 'Course created successfully.');
            $this->redirect('/courses');
        } else {
            Session::flash('error', 'Failed to create course. Please try again.');
            // @TODO: Pass back old input to the form
            $this->redirect('/courses/create');
        }
    }

    /**
     * Display the specified course.
     * Corresponds to the 'view.php' file.
     * Handles GET /courses/{id}
     *
     * @param int|string $id The course ID.
     * @return string The rendered view of the course details.
     */
    public function show($id): string
    {
        // @TODO: Create a custom method in Course model to join with teachers and departments
        $course = $this->courseModel->find($id);

        if (!$course) {
            return $this->view->renderError(404);
        }

        return $this->render('courses/view', ['course' => $course], 'admin');
    }

    /**
     * Show the form for editing the specified course.
     * Handles GET /courses/{id}/edit
     *
     * @param int|string $id The course ID.
     * @return string The rendered view of the edit form.
     */
    public function edit($id): string
    {
        $course = $this->courseModel->find($id);

        if (!$course) {
            return $this->view->renderError(404);
        }

        $teacherModel = new Teacher();
        $departmentModel = new Department();
        
        $data = [
            'course' => $course,
            'teachers' => $teacherModel->all(),
            'departments' => $departmentModel->all(),
        ];

        return $this->render('courses/edit', $data, 'admin');
    }

    /**
     * Update the specified course in the database.
     * Handles POST /courses/{id}/update
     *
     * @param int|string $id The course ID.
     */
    public function update($id): void
    {
        $request = new Request();
        
        // @TODO: Implement CSRF token validation and a Validator class.

        $courseData = [
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'credit_hours' => $request->input('credit_hours'),
            'department_id' => $request->input('department_id'),
            'teacher_id' => $request->input('teacher_id'),
        ];
        
        if ($this->courseModel->update($id, $courseData)) {
            Session::flash('success', 'Course updated successfully.');
            $this->redirect('/courses/' . $id); // Redirect to the show page
        } else {
            Session::flash('error', 'Failed to update course or no changes were made.');
            $this->redirect('/courses/' . $id . '/edit');
        }
    }

    /**
     * Remove the specified course from the database.
     * Handles POST /courses/{id}/delete
     *
     * @param int|string $id The course ID.
     */
    public function destroy($id): void
    {
        // @TODO: Implement CSRF token validation.

        if ($this->courseModel->delete($id)) {
            Session::flash('success', 'Course deleted successfully.');
        } else {
            Session::flash('error', 'Failed to delete course.');
        }
        $this->redirect('/courses');
    }
}