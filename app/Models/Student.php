<?php

namespace App\Models;

use App\Core\Model;
use App\Models\User;
use App\Models\Department;
use App\Models\Guardian;
use App\Models\Enrollment;
use App\Models\Grade;
// use App\Traits\Timestamps; // Uncomment when ready
// use App\Traits\SoftDeletes; // Uncomment when ready
use \PDO; // Import PDO for FETCH_CLASS constant

/**
 * Student Model
 * ... ( باقي التعليقات ) ...
 */
class Student extends Model
{
    // use Timestamps, SoftDeletes; // We will uncomment this when traits are built

    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'students'; // <-- ADD string type

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- ADD string type

    /**
     * @var User|null Cached User object for this student.
     */
    private ?User $_user = null; // <-- Use nullable type

    /**
     * @var Department|null Cached Department object for this student.
     */
    private ?Department $_department = null; // <-- Use nullable type

    /**
     * Get the associated User account for this student (if any).
     * (BelongsTo relationship)
     *
     * @return User|object|null Returns User object or null.
     */
    public function user(): ?object // Changed return type
    {
        if ($this->user_id === null) {
            return null;
        }
        if ($this->_user === null) {
            $foundUser = (new User())->find($this->user_id); // find returns object|false
             if ($foundUser instanceof User) { // Check if it's a User object
                 $this->_user = $foundUser;
             } else {
                 return null; // Return null if user not found for the ID
             }
        }
        return $this->_user;
    }

    /**
     * Get the Department this student belongs to.
     * (BelongsTo relationship)
     *
     * @return Department|object|null Returns Department object or null.
     */
    public function department(): ?object // Changed return type
    {
        if ($this->department_id === null) {
            return null;
        }
        if ($this->_department === null) {
            $foundDept = (new Department())->find($this->department_id);
            if ($foundDept instanceof Department) { // Check type
                 $this->_department = $foundDept;
             } else {
                 return null; // Return null if dept not found
             }
        }
        return $this->_department;
    }

    /**
     * Get all Guardians associated with this student.
     * (HasMany relationship)
     *
     * @return array An array of Guardian objects.
     */
    public function guardians(): array
    {
        $guardianModel = new Guardian();
        $stmt = $guardianModel->query(
            "SELECT * FROM {$guardianModel->getTable()} WHERE student_id = ?",
            [$this->id]
        );
        // Fetch as Guardian objects
        return $stmt->fetchAll(PDO::FETCH_CLASS, Guardian::class); 
    }

    /**
     * Get all Enrollments for this student.
     * (HasMany relationship)
     *
     * @return array An array of Enrollment objects.
     */
    public function enrollments(): array
    {
        $enrollmentModel = new Enrollment();
        $stmt = $enrollmentModel->query(
            "SELECT * FROM {$enrollmentModel->getTable()} WHERE student_id = ?",
            [$this->id]
        );
         // Fetch as Enrollment objects
        return $stmt->fetchAll(PDO::FETCH_CLASS, Enrollment::class);
    }

    /**
     * Get all Grades for this student through their enrollments.
     * (HasManyThrough relationship)
     *
     * @return array An array of Grade objects.
     */
    public function grades(): array
    {
        $gradeModel = new Grade();
        $sql = "SELECT g.*
                FROM {$gradeModel->getTable()} g
                JOIN enrollments e ON g.enrollment_id = e.id
                WHERE e.student_id = ?";
        
        $stmt = $gradeModel->query($sql, [$this->id]);
         // Fetch as Grade objects
        return $stmt->fetchAll(PDO::FETCH_CLASS, Grade::class);
    }
    
    /**
     * Find a student by their unique student number.
     *
     * @param string $studentNumber
     * @return object|false Student object or false if not found.
     */
    public function findByStudentNumber(string $studentNumber): object|false // Updated return type
    {
        $sql = "SELECT * FROM {$this->table} WHERE student_number = ?";
        $stmt = $this->query($sql, [$studentNumber]);
        // Fetch as Student object
        return $stmt->fetchObject(static::class); 
    }
}
