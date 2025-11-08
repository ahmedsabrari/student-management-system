<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Helpers\Session;

/**
 * ClassController
 *
 * Manages the CRUD operations for classes, which represent instances
 * of a course taught in a specific academic year by a teacher.
 */
class ClassController extends Controller
{
    /** @var ClassModel The class model instance. */
    protected $classModel;

    /**
     * ClassController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->classModel = new ClassModel();
    }

    /**
     * Display a listing of all classes.
     * Handles GET /classes
     *
     * @return string The rendered view of the class list.
     */
    public function index(): string
    {
        // @TODO: Create a custom method in ClassModel to join with courses and teachers for a richer list view.
        $classes = $this->classModel->all();
        return $this->render('classes/index', ['classes' => $classes], 'admin');
    }

    /**
     * Show the form for creating a new class.
     * Handles GET /classes/create
     *
     * @return string The rendered view of the create form.
     */
    public function create(): string
    {
        $data = $this->getFormData();
        return $this->render('classes/create', $data, 'admin');
    }

    /**
     * Store a newly created class in the database.
     * Handles POST /classes
     */
    public function store(): void
    {
        $request = new Request();

        // @TODO: Implement CSRF token validation.
        // @TODO: Implement a robust Validator class.
        
        $classData = [
            'name' => $request->input('name'),
            'course_id' => $request->input('course_id'),
            'teacher_id' => $request->input('teacher_id'),
            'academic_year_id' => $request->input('academic_year_id'),
            'schedule' => $request->input('schedule'),
            'semester' => $request->input('semester'),
            'max_students' => $request->input('max_students', 30),
        ];

        if ($this->classModel->create($classData)) {
            Session::flash('success', 'Class created successfully.');
            $this->redirect('/classes');
        } else {
            Session::flash('error', 'Failed to create class. Please try again.');
            // @TODO: Pass back old input to the form
            $this->redirect('/classes/create');
        }
    }

    /**
     * Display the specified class.
     * Corresponds to the 'view.php' file.
     * Handles GET /classes/{id}
     *
     * @param int|string $id The class ID.
     * @return string The rendered view of the class details.
     */
    public function show($id): string
    {
        // @TODO: Create a custom method in ClassModel to get full details including course name,
        // teacher name, and a list of enrolled students.
        $class = $this->classModel->find($id);

        if (!$class) {
            return $this->view->renderError(404);
        }

        return $this->render('classes/view', ['class' => $class], 'admin');
    }

    /**
     * Show the form for editing the specified class.
     * Handles GET /classes/{id}/edit
     *
     * @param int|string $id The class ID.
     * @return string The rendered view of the edit form.
     */
    public function edit($id): string
    {
        $class = $this->classModel->find($id);

        if (!$class) {
            return $this->view->renderError(404);
        }
        
        $data = $this->getFormData();
        $data['class'] = $class;

        return $this->render('classes/edit', $data, 'admin');
    }

    /**
     * Update the specified class in the database.
     * Handles POST /classes/{id}/update
     *
     * @param int|string $id The class ID.
     */
    public function update($id): void
    {
        $request = new Request();
        
        // @TODO: Implement CSRF token validation and a Validator class.

        $classData = [
            'name' => $request->input('name'),
            'course_id' => $request->input('course_id'),
            'teacher_id' => $request->input('teacher_id'),
            'academic_year_id' => $request->input('academic_year_id'),
            'schedule' => $request->input('schedule'),
            'semester' => $request->input('semester'),
            'max_students' => $request->input('max_students'),
        ];
        
        if ($this->classModel->update($id, $classData)) {
            Session::flash('success', 'Class updated successfully.');
            $this->redirect('/classes/' . $id);
        } else {
            Session::flash('error', 'Failed to update class or no changes were made.');
            $this->redirect('/classes/' . $id . '/edit');
        }
    }

    /**
     * Remove the specified class from the database.
     * Handles POST /classes/{id}/delete
     *
     * @param int|string $id The class ID.
     */
    public function destroy($id): void
    {
        // @TODO: Implement CSRF token validation.

        // Add logic to check if there are enrollments before deleting.
        
        if ($this->classModel->delete($id)) {
            Session::flash('success', 'Class deleted successfully.');
        } else {
            Session::flash('error', 'Failed to delete class.');
        }
        $this->redirect('/classes');
    }

    /**
     * Fetches common data needed for create and edit forms.
     * This avoids code duplication.
     *
     * @return array
     */
    private function getFormData(): array
    {
        return [
            'courses' => (new Course())->all(),
            'teachers' => (new Teacher())->all(),
            'academic_years' => (new AcademicYear())->all(),
        ];
    }
}