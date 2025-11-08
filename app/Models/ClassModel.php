<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Assignment;
// use App\Traits\SoftDeletes; // Uncomment when ready
use \PDO; // <-- Import PDO for FETCH_CLASS constant

/**
 * ClassModel Model
 *
 * Represents the 'classes' table in the database.
 * This model defines a specific instance of a course being taught,
 * linking it to a teacher, academic year, and enrolled students.
 */
class ClassModel extends Model
{
    /**
     * Use soft deletes (deleted_at).
     * @TODO: This trait needs to be created and implemented.
     */
    // use SoftDeletes; // We will uncomment this when the trait is built

    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'classes'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public string $name;
    public int $course_id;
    public ?int $teacher_id = null; // Nullable
    public ?int $academic_year_id = null; // Nullable
    public ?string $schedule = null; // Nullable
    public string $semester; // ENUM
    public string $year; // YEAR type
    public int $max_students;
    public string $created_at;
    public ?string $deleted_at = null; // Nullable

    /**
     * @var Course|null Cached Course object.
     */
    private ?Course $_course = null; // <-- Use nullable type

    /**
     * @var Teacher|null Cached Teacher object.
     */
    private ?Teacher $_teacher = null; // <-- Use nullable type
    
    /**
     * @var AcademicYear|null Cached AcademicYear object.
     */
    private ?AcademicYear $_academicYear = null; // <-- Use nullable type


    /**
     * Get the Course this class belongs to.
     * (BelongsTo relationship)
     *
     * @return Course|object|null
     */
    public function course(): ?object // <-- Use nullable type
    {
        if ($this->_course === null) {
            $foundCourse = (new Course())->find($this->course_id);
            if ($foundCourse instanceof Course) {
                $this->_course = $foundCourse;
            } else {
                return null;
            }
        }
        return $this->_course;
    }

    /**
     * Get the Teacher assigned to this class.
     * (BelongsTo relationship)
     *
     * @return Teacher|object|null
     */
    public function teacher(): ?object // <-- Use nullable type
    {
        if ($this->teacher_id === null) {
            return null;
        }
        if ($this->_teacher === null) {
            $foundTeacher = (new Teacher())->find($this->teacher_id);
             if ($foundTeacher instanceof Teacher) {
                $this->_teacher = $foundTeacher;
            } else {
                return null;
            }
        }
        return $this->_teacher;
    }

    /**
     * Get the Academic Year for this class.
     * (BelongsTo relationship)
     *
     * @return AcademicYear|object|null
     */
    public function academicYear(): ?object // <-- Use nullable type
    {
        if ($this->academic_year_id === null) {
            return null;
        }
        if ($this->_academicYear === null) {
            $foundYear = (new AcademicYear())->find($this->academic_year_id);
             if ($foundYear instanceof AcademicYear) {
                $this->_academicYear = $foundYear;
            } else {
                return null;
            }
        }
        return $this->_academicYear;
    }

    /**
     * Get all Students enrolled in this class.
     * (Many-to-Many relationship through 'enrollments' table)
     *
     * @return array An array of Student objects.
     */
    public function students(): array
    {
        $studentModel = new Student(); // Instance of Student model
        $sql = "SELECT s.*
                FROM {$studentModel->getTable()} s
                JOIN enrollments e ON s.id = e.student_id
                WHERE e.class_id = ?";
        
        $stmt = $studentModel->query($sql, [$this->id]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Student::class); // <-- FIXED (Fetch method)
    }

    /**
     * Get all Assignments associated with this class.
     * (HasMany relationship)
     *
     * @return array An array of Assignment objects.
     */
    public function assignments(): array
    {
        $assignmentModel = new Assignment(); // Instance of Assignment model
        $stmt = $assignmentModel->query(
            "SELECT * FROM {$assignmentModel->getTable()} WHERE class_id = ?",
            [$this->id]
        );
        return $stmt->fetchAll(PDO::FETCH_CLASS, Assignment::class); // <-- FIXED (Fetch method)
    }
}