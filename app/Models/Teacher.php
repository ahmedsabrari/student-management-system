<?php

namespace App\Models;

use App\Core\Model;
use App\Models\User;
use App\Models\Department;
use App\Models\ClassModel;
use App\Traits\SoftDeletes;
use App\Models\Course; // Added for courses() relationship
// use App\Traits\SoftDeletes; // Uncomment when ready
use \PDO; // Import PDO

/**
 * Teacher Model
 *
 * Represents the 'teachers' table in the database.
 * This model defines the teacher entity and its relationships to
 * users, departments, and the classes they teach.
 */
class Teacher extends Model
{
    /**
     * Use soft deletes (deleted_at).
     * @TODO: This trait needs to be created and implemented.
     */
    // use SoftDeletes; // We will uncomment this when the trait is built

    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected string $table = 'teachers';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected string $primaryKey = 'id';

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public int $user_id;
    public ?int $department_id = null; // Nullable
    public ?string $hire_date = null; // Nullable
    public ?string $phone = null; // Nullable
    public ?string $address = null; // Nullable
    public ?string $qualifications = null; // Nullable
    public ?string $specialization = null; // Nullable
    public ?string $deleted_at = null; // Nullable

    /**
     * @var User|null Cached User object for this teacher.
     */
    private ?User $_user = null; // Use nullable type

    /**
     * @var Department|null Cached Department object for this teacher.
     */
    private ?Department $_department = null; // Use nullable type

    /**
     * Get the associated User account for this teacher.
     * (BelongsTo relationship)
     */
    public function user(): ?object
    {
        if ($this->_user === null) {
             $foundUser = (new User())->find($this->user_id);
             if ($foundUser instanceof User) {
                 $this->_user = $foundUser;
             } else {
                 return null;
             }
        }
        return $this->_user;
    }

    /**
     * Get the Department this teacher belongs to.
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
     * Get all Classes taught by this teacher.
     * (HasMany relationship)
     */
    public function classes(): array
    {
        $classModel = new ClassModel();
        $stmt = $classModel->query("SELECT * FROM {$classModel->getTable()} WHERE teacher_id = ?", [$this->id]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, ClassModel::class);
    }
    
    /**
     * Get all Courses assigned to this teacher.
     * (HasMany relationship)
     */
    public function courses(): array
    {
        $courseModel = new Course();
        $stmt = $courseModel->query("SELECT * FROM {$courseModel->getTable()} WHERE teacher_id = ?", [$this->id]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Course::class);
    }

    /**
     * Find a teacher record by their associated user_id.
     *
     * @param int|string $userId The user ID.
     * @return object|false Teacher object or false if not found.
     */
    public function findByUserId($userId): object|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? LIMIT 1";
        $stmt = $this->query($sql, [$userId]);
        return $stmt->fetchObject(static::class);
    }
}