<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Department;
use App\Models\Teacher;
use App\Models\ClassModel;
// use App\Traits\SoftDeletes; // Uncomment when ready
use \PDO; // Import PDO

/**
 * Course Model
 *
 * Represents the 'courses' table in the database.
 * This model defines the academic courses offered by the institution.
 */
class Course extends Model
{
    // use SoftDeletes;

    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'courses'; // <-- ADD string type

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- ADD string type

    // --- Declare properties corresponding to database columns ---
    public int $id;
    public string $code;
    public string $name;
    public ?string $description = null; // Nullable
    public int $credit_hours;
    public ?int $department_id = null; // Nullable
    public ?int $teacher_id = null; // Nullable
    public string $created_at;
    public ?string $deleted_at = null; // Nullable
    // Note: updated_at is not in the schema for courses table

    /**
     * @var Department|null Cached Department object.
     */
    private ?Department $_department = null; // Use nullable type

    /**
     * @var Teacher|null Cached Teacher object.
     */
    private ?Teacher $_teacher = null; // Use nullable type

    /**
     * Get the Department this course belongs to.
     * (BelongsTo relationship)
     */
    public function department(): ?object
    {
        if ($this->department_id === null) {
            return null;
        }
        if ($this->_department === null) {
            $foundDept = (new Department())->find($this->department_id);
            if ($foundDept instanceof Department) {
                $this->_department = $foundDept;
            } else {
                return null;
            }
        }
        return $this->_department;
    }

    /**
     * Get the Teacher assigned to this course (if any).
     * (BelongsTo relationship)
     */
    public function teacher(): ?object
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
     * Get all Classes associated with this course.
     * (HasMany relationship)
     */
    public function classes(): array
    {
        $classModel = new ClassModel();
        $stmt = $classModel->query(
            "SELECT * FROM {$classModel->getTable()} WHERE course_id = ?",
            [$this->id]
        );
        return $stmt->fetchAll(PDO::FETCH_CLASS, ClassModel::class);
    }
    
    /**
     * Find a course by its unique course code.
     * @param string $code
     * @return object|false
     */
    public function findByCode(string $code): object|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE code = ? LIMIT 1";
        $stmt = $this->query($sql, [$code]);
        return $stmt->fetchObject(static::class);
    }
}