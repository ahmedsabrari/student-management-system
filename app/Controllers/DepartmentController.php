<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Department;
use App\Models\Teacher;
use App\Models\Course;
use App\Helpers\Session;

/**
 * DepartmentController
 *
 * Manages all CRUD operations for academic departments.
 */
class DepartmentController extends Controller
{
    /** @var Department The department model instance. */
    protected $departmentModel;

    /**
     * DepartmentController constructor.
     * Initializes the Department model.
     */
    public function __construct()
    {
        parent::__construct();
        $this->departmentModel = new Department();
    }

    /**
     * Display a listing of all departments.
     * Handles GET /departments
     *
     * @return string The rendered view of the department list.
     */
    public function index(): string
    {
        $departments = $this->departmentModel->all();
        return $this->render('departments/index', ['departments' => $departments], 'admin');
    }

    /**
     * Show the form for creating a new department.
     * Handles GET /departments/create
     *
     * @return string The rendered view of the create form.
     */
    public function create(): string
    {
        return $this->render('departments/create', [], 'admin');
    }

    /**
     * Store a newly created department in the database.
     * Handles POST /departments
     */
    public function store(): void
    {
        $request = new Request();

        // @TODO: Implement CSRF token validation.
        // @TODO: Implement a robust Validator class.
        // E.g., Validator::validate($request->all(), [
        //    'name' => 'required|unique:departments,name'
        // ]);

        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ];

        if ($this->departmentModel->create($data)) {
            Session::flash('success', 'Department created successfully.');
            $this->redirect('/departments');
        } else {
            Session::flash('error', 'Failed to create department. The name may already exist.');
            // @TODO: Pass back old input
            $this->redirect('/departments/create');
        }
    }

    /**
     * Display the specified department's details.
     * (Corresponds to the 'view' page from the file structure)
     * Handles GET /departments/{id}
     *
     * @param int|string $id The department ID.
     * @return string The rendered view of the department details.
     */
    public function show($id): string
    {
        $department = $this->departmentModel->find($id);

        if (!$department) {
            return $this->view->renderError(404);
        }

        // @TODO: Create custom methods in models for efficient fetching.
        // e.g., $teacherModel->findAllByDepartment($id);
        $teachers = (new Teacher())->query("SELECT * FROM teachers WHERE department_id = ?", [$id])->fetchAll();
        $courses = (new Course())->query("SELECT * FROM courses WHERE department_id = ?", [$id])->fetchAll();

        $data = [
            'department' => $department,
            'teachers' => $teachers,
            'courses' => $courses,
        ];

        return $this->render('departments/view', $data, 'admin');
    }

    /**
     * Show the form for editing the specified department.
     * Handles GET /departments/{id}/edit
     *
     * @param int|string $id The department ID.
     * @return string The rendered view of the edit form.
     */
    public function edit($id): string
    {
        $department = $this->departmentModel->find($id);

        if (!$department) {
            return $this->view->renderError(404);
        }

        return $this->render('departments/edit', ['department' => $department], 'admin');
    }

    /**
     * Update the specified department in the database.
     * Handles POST /departments/{id}/update
     *
     * @param int|string $id The department ID.
     */
    public function update($id): void
    {
        $request = new Request();
        
        // @TODO: Implement CSRF token validation.
        // @TODO: Implement Validator::validate(..., ['name' => 'required|unique:departments,name,' . $id]);

        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ];
        
        if ($this->departmentModel->update($id, $data)) {
            Session::flash('success', 'Department updated successfully.');
            $this->redirect('/departments');
        } else {
            Session::flash('error', 'Failed to update department or no changes were made.');
            $this->redirect('/departments/' . $id . '/edit');
        }
    }

    /**
     * Remove the specified department from the database.
     * Handles POST /departments/{id}/delete
     *
     * @param int|string $id The department ID.
     */
    public function destroy($id): void
    {
        // @TODO: Implement CSRF token validation.

        // Note: The database schema (departments table) uses `ON DELETE SET NULL`
        // for teachers and courses. This means deleting a department will not
        // delete associated teachers/courses, but will set their `department_id` to NULL.
        
        if ($this->departmentModel->delete($id)) {
            Session::flash('success', 'Department deleted successfully.');
        } else {
            Session::flash('error', 'Failed to delete department.');
        }
        $this->redirect('/departments');
    }
}