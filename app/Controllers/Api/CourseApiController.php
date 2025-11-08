<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Course;

/**
 * CourseApiController
 *
 * Handles API requests for the Course resource. All responses are in JSON format.
 * This controller provides a stateless RESTful interface for managing courses.
 */
class CourseApiController extends Controller
{
    /** @var Course The course model instance. */
    protected $courseModel;

    /**
     * CourseApiController constructor.
     *
     * @TODO: Apply ApiAuthMiddleware to all methods in this controller.
     * e.g., $this->middleware(ApiAuthMiddleware::class);
     */
    public function __construct()
    {
        parent::__construct();
        $this->courseModel = new Course();
    }

    /**
     * Get all courses.
     * Handles GET /api/courses
     *
     * @return void
     */
    public function index(): void
    {
        try {
            // @TODO: Add logic for pagination (e.g., ?page=1&limit=15)
            $courses = $this->courseModel->all();
            $this->json([
                'status' => 'success',
                'message' => 'Courses retrieved successfully',
                'data' => $courses
            ], 200);
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a single course by ID.
     * Handles GET /api/courses/{id}
     *
     * @param int|string $id The course ID.
     * @return void
     */
    public function show($id): void
    {
        // @TODO: This could be optimized to JOIN with teacher and department tables
        $course = $this->courseModel->find($id);

        if (!$course) {
            $this->json([
                'status' => 'error',
                'message' => 'Course not found'
            ], 404);
            return;
        }

        $this->json([
            'status' => 'success',
            'message' => 'Course retrieved successfully',
            'data' => $course
        ], 200);
    }

    /**
     * Create a new course.
     * Reads JSON payload from the request.
     * Handles POST /api/courses
     *
     * @return void
     */
    public function store(): void
    {
        $data = $this->getJsonData();

        // @TODO: Implement a robust Validator class
        // $errors = Validator::validate($data, [
        //    'code' => 'required|unique:courses',
        //    'name' => 'required',
        //    'department_id' => 'required|exists:departments,id',
        //    'teacher_id' => 'required|exists:teachers,id'
        // ]);
        // if ($errors) {
        //    $this->json(['status' => 'fail', 'message' => 'Validation error', 'data' => $errors], 422);
        //    return;
        // }

        try {
            $newCourseId = $this->courseModel->create($data);
            if ($newCourseId) {
                $newCourse = $this->courseModel->find($newCourseId);
                $this->json([
                    'status' => 'success',
                    'message' => 'Course created successfully',
                    'data' => $newCourse
                ], 201); // 201 Created
            } else {
                throw new \Exception('Failed to create course.');
            }
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 400); // 400 Bad Request
        }
    }

    /**
     * Update an existing course.
     * Reads JSON payload from the request.
     * Handles PUT /api/courses/{id}
     *
     * @param int|string $id The course ID.
     * @return void
     */
    public function update($id): void
    {
        $course = $this->courseModel->find($id);
        if (!$course) {
            $this->json(['status' => 'error', 'message' => 'Course not found'], 404);
            return;
        }

        $data = $this->getJsonData();

        // @TODO: Implement Validator
        
        try {
            if ($this->courseModel->update($id, $data)) {
                $updatedCourse = $this->courseModel->find($id);
                $this->json([
                    'status' => 'success',
                    'message' => 'Course updated successfully',
                    'data' => $updatedCourse
                ], 200);
            } else {
                $this->json([
                    'status' => 'success',
                    'message' => 'No changes were made',
                    'data' => $course
                ], 200);
            }
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Delete a course.
     * Handles DELETE /api/courses/{id}
     *
     * @param int|string $id The course ID.
     * @return void
     */
    public function destroy($id): void
    {
        $course = $this->courseModel->find($id);
        if (!$course) {
            $this->json(['status' => 'error', 'message' => 'Course not found'], 404);
            return;
        }

        try {
            if ($this->courseModel->delete($id)) {
                $this->json([
                    'status' => 'success',
                    'message' => 'Course deleted successfully',
                    'data' => null
                ], 200);
            } else {
                throw new \Exception('Failed to delete course.');
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
        $data = json_decode(file_get_contents('php://input'), true);
        return $data ?? [];
    }
}