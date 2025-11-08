<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Student;
use App\Helpers\Session; // Although session might not be used for token-based API auth

/**
 * StudentApiController
 *
 * Handles API requests for the Student resource. All responses are in JSON format.
 * This controller provides a stateless RESTful interface for managing students.
 */
class StudentApiController extends Controller
{
    /** @var Student The student model instance. */
    protected $studentModel;

    /**
     * StudentApiController constructor.
     *
     * @TODO: Apply ApiAuthMiddleware to all methods in this controller.
     * e.g., $this->middleware(ApiAuthMiddleware::class);
     */
    public function __construct()
    {
        parent::__construct();
        $this->studentModel = new Student();
    }

    /**
     * Get all students.
     * Handles GET /api/students
     *
     * @return void
     */
    public function index(): void
    {
        try {
            $students = $this->studentModel->all();
            $this->json([
                'status' => 'success',
                'message' => 'Students retrieved successfully',
                'data' => $students
            ], 200);
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a single student by ID.
     * Handles GET /api/students/{id}
     *
     * @param int|string $id The student ID.
     * @return void
     */
    public function show($id): void
    {
        $student = $this->studentModel->find($id);

        if (!$student) {
            $this->json([
                'status' => 'error',
                'message' => 'Student not found'
            ], 404);
            return;
        }

        $this->json([
            'status' => 'success',
            'message' => 'Student retrieved successfully',
            'data' => $student
        ], 200);
    }

    /**
     * Create a new student.
     * Reads JSON payload from the request.
     * Handles POST /api/students
     *
     * @return void
     */
    public function store(): void
    {
        $data = $this->getJsonData();

        // @TODO: Implement a robust Validator class
        // $errors = Validator::validate($data, [
        //    'student_number' => 'required|unique:students',
        //    'first_name' => 'required',
        //    'last_name' => 'required',
        //    'email' => 'required|email|unique:students'
        // ]);
        // if ($errors) {
        //    $this->json(['status' => 'fail', 'message' => 'Validation error', 'data' => $errors], 422);
        //    return;
        // }

        try {
            $newStudentId = $this->studentModel->create($data);
            if ($newStudentId) {
                $newStudent = $this->studentModel->find($newStudentId);
                $this->json([
                    'status' => 'success',
                    'message' => 'Student created successfully',
                    'data' => $newStudent
                ], 201); // 201 Created
            } else {
                throw new \Exception('Failed to create student.');
            }
        } catch (\Exception $e) {
            // Handle unique constraint violations, etc.
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 400); // 400 Bad Request
        }
    }

    /**
     * Update an existing student.
     * Reads JSON payload from the request.
     * Handles PUT /api/students/{id}
     *
     * @param int|string $id The student ID.
     * @return void
     */
    public function update($id): void
    {
        $student = $this->studentModel->find($id);
        if (!$student) {
            $this->json(['status' => 'error', 'message' => 'Student not found'], 404);
            return;
        }

        $data = $this->getJsonData();

        // @TODO: Implement Validator
        
        try {
            if ($this->studentModel->update($id, $data)) {
                $updatedStudent = $this->studentModel->find($id);
                $this->json([
                    'status' => 'success',
                    'message' => 'Student updated successfully',
                    'data' => $updatedStudent
                ], 200);
            } else {
                // No rows affected, but not necessarily an error
                $this->json([
                    'status' => 'success',
                    'message' => 'No changes were made',
                    'data' => $student
                ], 200);
            }
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 400); // 400 Bad Request
        }
    }

    /**
     * Delete a student.
     * Handles DELETE /api/students/{id}
     *
     * @param int|string $id The student ID.
     * @return void
     */
    public function destroy($id): void
    {
        $student = $this->studentModel->find($id);
        if (!$student) {
            $this->json(['status' => 'error', 'message' => 'Student not found'], 404);
            return;
        }

        try {
            if ($this->studentModel->delete($id)) {
                $this->json([
                    'status' => 'success',
                    'message' => 'Student deleted successfully',
                    'data' => null
                ], 200); // Or 204 No Content
            } else {
                throw new \Exception('Failed to delete student.');
            }
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper method to get JSON data from the request body.
     *
     * @return array
     */
    private function getJsonData(): array
    {
        // Get raw JSON post data
        $data = json_decode(file_get_contents('php://input'), true);
        
        // If json_decode fails, return an empty array
        return $data ?? [];
    }
}