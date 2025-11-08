<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use App\Helpers\Session;

/**
 * TeacherController
 *
 * Manages all CRUD operations for teachers, including their associated
 * user accounts and department assignments.
 */
class TeacherController extends Controller
{
    /** @var Teacher The teacher model instance. */
    protected $teacherModel;

    /** @var User The user model instance. */
    protected $userModel;

    /**
     * TeacherController constructor.
     * Initializes necessary models.
     */
    public function __construct()
    {
        parent::__construct();
        $this->teacherModel = new Teacher();
        $this->userModel = new User();
    }

    /**
     * Display a listing of all teachers.
     * Handles GET /teachers
     *
     * @return string The rendered view of the teacher list.
     */
    public function index(): string
    {
        // @TODO: Create a custom method in TeacherModel to join with `users` and `departments`
        // for a richer list view (e.g., $this->teacherModel->getAllDetails())
        $teachers = $this->teacherModel->all();
        return $this->render('teachers/index', ['teachers' => $teachers], 'admin');
    }

    /**
     * Show the form for creating a new teacher.
     * Handles GET /teachers/create
     *
     * @return string The rendered view of the create form.
     */
    public function create(): string
    {
        $departmentModel = new Department();
        $data = [
            'departments' => $departmentModel->all(),
        ];
        return $this->render('teachers/create', $data, 'admin');
    }

    /**
     * Store a newly created teacher in the database.
     * This involves creating a User record first, then a Teacher record.
     * Handles POST /teachers
     */
    public function store(): void
    {
        $request = new Request();

        // @TODO: Implement CSRF token validation.
        // @TODO: Implement a robust Validator class for all fields.

        // --- 1. Create the User record ---
        // @TODO: Get role_id for 'teacher' from the database dynamically
        $teacherRole = (new Role())->findByName('teacher');
        
        $userData = [
            'full_name' => $request->input('full_name'),
            'email' => $request->input('email'),
            'username' => $request->input('username', $request->input('email')), // Default username to email
            'password' => password_hash($request->input('password'), PASSWORD_BCRYPT),
            'role_id' => $teacherRole ? $teacherRole->id : null, // Assign 'teacher' role
        ];

        // @TODO: This should be wrapped in a database transaction
        $newUserId = $this->userModel->create($userData);

        if ($newUserId) {
            // --- 2. Create the Teacher record ---
            $teacherData = [
                'user_id' => $newUserId,
                'department_id' => $request->input('department_id'),
                'hire_date' => $request->input('hire_date'),
                'phone' => $request->input('phone'),
                'specialization' => $request->input('specialization'),
            ];

            if ($this->teacherModel->create($teacherData)) {
                Session::flash('success', 'Teacher created successfully.');
                $this->redirect('/teachers');
            } else {
                // Rollback: Delete the user that was just created, as teacher creation failed
                $this->userModel->delete($newUserId);
                Session::flash('error', 'Failed to create teacher record.');
                $this->redirect('/teachers/create');
            }
        } else {
            Session::flash('error', 'Failed to create user account for the teacher.');
            $this->redirect('/teachers/create');
        }
    }

    /**
     * Display the specified teacher's profile.
     * Corresponds to the 'view.php' file.
     * Handles GET /teachers/{id}
     *
     * @param int|string $id The Teacher record ID (not the User ID).
     * @return string The rendered view of the teacher profile.
     */
    public function show($id): string
    {
        // @TODO: Create a custom method in TeacherModel to get full details
        // (joined with users, departments, courses, and classes).
        // e.g., $teacher = $this->teacherModel->getFullProfile($id);
        $teacher = $this->teacherModel->find($id);

        if (!$teacher) {
            return $this->view->renderError(404);
        }

        // Fetch associated user data
        $teacher->user = $this->userModel->find($teacher->user_id);
        
        // @TODO: Fetch courses and classes
        // $teacher->courses = (new \App\Models\Course())->findByTeacherId($id);
        // $teacher->classes = (new \App\Models\ClassModel())->findByTeacherId($id);

        return $this->render('teachers/view', ['teacher' => $teacher], 'admin');
    }

    /**
     * Show the form for editing the specified teacher.
     * Handles GET /teachers/{id}/edit
     *
     * @param int|string $id The Teacher record ID.
     * @return string The rendered view of the edit form.
     */
    public function edit($id): string
    {
        $teacher = $this->teacherModel->find($id);

        if (!$teacher) {
            return $this->view->renderError(404);
        }

        $departmentModel = new Department();
        
        $data = [
            'teacher' => $teacher,
            'user' => $this->userModel->find($teacher->user_id),
            'departments' => $departmentModel->all(),
        ];

        return $this->render('teachers/edit', $data, 'admin');
    }

    /**
     * Update the specified teacher in the database.
     * Handles POST /teachers/{id}/update
     *
     * @param int|string $id The Teacher record ID.
     */
    public function update($id): void
    {
        $request = new Request();
        $teacher = $this->teacherModel->find($id);

        if (!$teacher) {
            Session::flash('error', 'Teacher not found.');
            $this->redirect('/teachers');
            return;
        }

        // @TODO: Implement CSRF token validation and a Validator class.
        // @TODO: This should be wrapped in a database transaction.

        // --- 1. Update User data ---
        $userData = [
            'full_name' => $request->input('full_name'),
            'email' => $request->input('email'),
            'username' => $request->input('username', $request->input('email')),
        ];
        $this->userModel->update($teacher->user_id, $userData);

        // --- 2. Update Teacher data ---
        $teacherData = [
            'department_id' => $request->input('department_id'),
            'hire_date' => $request->input('hire_date'),
            'phone' => $request->input('phone'),
            'specialization' => $request->input('specialization'),
        ];
        
        if ($this->teacherModel->update($id, $teacherData)) {
            Session::flash('success', 'Teacher updated successfully.');
            $this->redirect('/teachers/' . $id); // Redirect to show page
        } else {
            Session::flash('error', 'Failed to update teacher or no changes were made.');
            $this->redirect('/teachers/' . $id . '/edit');
        }
    }

    /**
     * Remove the specified teacher from the database.
     * This will also delete the associated User record due to ON DELETE CASCADE.
     * Handles POST /teachers/{id}/delete
     *
     * @param int|string $id The Teacher record ID.
     */
    public function destroy($id): void
    {
        // @TODO: Implement CSRF token validation.
        
        $teacher = $this->teacherModel->find($id);

        if (!$teacher) {
            Session::flash('error', 'Teacher not found.');
            $this->redirect('/teachers');
            return;
        }

        // By deleting the User, the corresponding Teacher record will be
        // deleted automatically because of the `ON DELETE CASCADE` constraint
        // defined in the `teachers` table.
        if ($this->userModel->delete($teacher->user_id)) {
            Session::flash('success', 'Teacher and associated user account deleted successfully.');
        } else {
            Session::flash('error', 'Failed to delete teacher.');
        }
        $this->redirect('/teachers');
    }
}