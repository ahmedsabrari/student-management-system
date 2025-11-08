<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Grade;
use App\Models\Attendance;
use \PDO; // Import PDO for FETCH_CLASS

/**
 * Enrollment Model
 *
 * Represents the 'enrollments' table in the database.
 * This model acts as a pivot, linking a student to a class they are
 * enrolled in, and is the parent for grades and attendance records.
 */
class Enrollment extends Model
{
    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'enrollments'; // <-- ADD string type

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- ADD string type

    // --- Declare properties corresponding to database columns ---
    public int $id;
    public int $student_id;
    public int $class_id;
    public ?string $enrollment_date = null; // Nullable
    public string $status;
    public string $created_at;
    // Note: updated_at is not in the schema for enrollments table

    /**
     * @var Student|null Cached Student object.
     */
    private ?Student $_student = null; // Use nullable type

    /**
     * @var ClassModel|null Cached Class object.
     */
    private ?ClassModel $_class = null; // Use nullable type

    /**
     * Get the Student associated with this enrollment.
     * (BelongsTo relationship)
     * @return Student|object|null
     */
    public function student(): ?object // Use nullable type
    {
        if ($this->_student === null) {
            $foundStudent = (new Student())->find($this->student_id);
            if ($foundStudent instanceof Student) {
                $this->_student = $foundStudent;
            } else {
                 return null;
            }
        }
        return $this->_student;
    }

    /**
     * Get the Class associated with this enrollment.
     * (BelongsTo relationship)
     * @return ClassModel|object|null
     */
    public function class(): ?object // Use nullable type
    {
        if ($this->_class === null) {
            $foundClass = (new ClassModel())->find($this->class_id);
             if ($foundClass instanceof ClassModel) {
                 $this->_class = $foundClass;
             } else {
                 return null;
            }
        }
        return $this->_class;
    }

    /**
     * Get all Grades associated with this enrollment.
     * (HasMany relationship)
     * @return array An array of Grade objects.
     */
    public function grades(): array
    {
        $gradeModel = new Grade();
        $stmt = $gradeModel->query(
            "SELECT * FROM {$gradeModel->getTable()} WHERE enrollment_id = ?",
            [$this->id]
        );
        return $stmt->fetchAll(PDO::FETCH_CLASS, Grade::class); // Fetch as Grade objects
    }

    /**
     * Get all Attendance records for this enrollment.
     * (HasMany relationship)
     * @return array An array of Attendance objects.
     */
    public function attendance(): array
    {
        $attendanceModel = new Attendance();
        $stmt = $attendanceModel->query(
            "SELECT * FROM {$attendanceModel->getTable()} WHERE enrollment_id = ?",
            [$this->id]
        );
        return $stmt->fetchAll(PDO::FETCH_CLASS, Attendance::class); // Fetch as Attendance objects
    }
}
