<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Student;
use \PDO; // <-- Import PDO for FETCH_CLASS constant

/**
 * Department Model
 *
 * Represents the 'departments' table in the database.
 * This model defines the academic departments and their relationships
 * to teachers, courses, and students.
 */
class Department extends Model
{
    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'departments'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public string $name;
    public ?string $description = null; // Nullable
    public string $created_at;

    /**
     * Get all Teachers associated with this department.
     * (HasMany relationship)
     *
     * @return array An array of Teacher objects.
     */
    public function teachers(): array
    {
        $teacherModel = new Teacher();
        $stmt = $teacherModel->query(
            "SELECT * FROM {$teacherModel->getTable()} WHERE department_id = ?",
            [$this->id]
        );
        return $stmt->fetchAll(PDO::FETCH_CLASS, Teacher::class); // <-- FIXED (Fetch method)
    }

    /**
     * Get all Courses associated with this department.
     * (HasMany relationship)
     *
     * @return array An array of Course objects.
     */
    public function courses(): array
    {
        $courseModel = new Course();
        $stmt = $courseModel->query(
            "SELECT * FROM {$courseModel->getTable()} WHERE department_id = ?",
            [$this->id]
        );
        return $stmt->fetchAll(PDO::FETCH_CLASS, Course::class); // <-- FIXED (Fetch method)
    }

    /**
     * Get all Students associated with this department.
     * (HasMany relationship)
     *
     * @return array An array of Student objects.
     */
    public function students(): array
    {
        $studentModel = new Student();
        $stmt = $studentModel->query(
            "SELECT * FROM {$studentModel->getTable()} WHERE department_id = ?",
            [$this->id]
        );
        return $stmt->fetchAll(PDO::FETCH_CLASS, Student::class); // <-- FIXED (Fetch method)
    }
}