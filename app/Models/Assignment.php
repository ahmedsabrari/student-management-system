<?php

namespace App\Models;

use App\Core\Model;
use App\Models\ClassModel;
use App\Models\AssignmentSubmission;
// use App\Traits\Timestamps; // Uncomment when ready
use \PDO; // <-- Import PDO for FETCH_CLASS constant

/**
 * Assignment Model
 *
 * Represents the 'assignments' table in the database.
 * This model defines an assignment (e.g., homework, project)
 * associated with a specific class.
 */
class Assignment extends Model
{
    /**
     * Use timestamps (created_at, updated_at).
     * @TODO: The Timestamps trait needs to be created and implemented.
     */
    // use Timestamps; // We will uncomment this when the trait is built

    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'assignments'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public int $class_id;
    public string $title;
    public ?string $description = null; // Nullable
    public ?string $due_date = null; // Nullable
    public ?float $max_points = null; // Nullable (decimal in SQL)
    public string $created_at;
    public ?string $updated_at = null; // Nullable

    /**
     * @var ClassModel|null Cached ClassModel object.
     */
    private ?ClassModel $_class = null; // <-- Use nullable type

    /**
     * Get the Class this assignment belongs to.
     * (BelongsTo relationship)
     *
     * @return ClassModel|object|null
     */
    public function class(): ?object // <-- Use nullable type
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
     * Get all submissions for this assignment.
     * (HasMany relationship)
     *
     * @return array An array of AssignmentSubmission objects.
     */
    public function submissions(): array
    {
        $submissionModel = new AssignmentSubmission();
        $stmt = $submissionModel->query(
            "SELECT * FROM {$submissionModel->getTable()} WHERE assignment_id = ?",
            [$this->id]
        );
        return $stmt->fetchAll(PDO::FETCH_CLASS, AssignmentSubmission::class); // <-- FIXED (Fetch method)
    }

    /**
     * Helper function: Get all assignments for a specific class.
     *
     * @param int|string $classId The ID of the class.
     * @return array An array of Assignment objects.
     */
    public function getByClass($classId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE class_id = ? ORDER BY due_date ASC";
        $stmt = $this->query($sql, [$classId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }

    /**
     * Helper function: Add a new assignment.
     * This is a convenience wrapper for the inherited create() method.
     *
     * @param array $data Associative array of data for the new assignment.
     * @return string|false The new record's ID or false on failure.
     */
    public function add(array $data)
    {
        // No change needed, create() is inherited
        return $this->create($data);
    }

    /**
     * Helper function: Update an existing assignment.
     * This is a convenience wrapper for the inherited update() method.
     *
     * @param int|string $id The ID of the assignment to update.
     * @param array $data Associative array of data to update.
     * @return bool True on success, false otherwise.
     */
    public function updateAssignment($id, array $data): bool
    {
        // No change needed, update() is inherited
        return $this->update($id, $data);
    }

    /**
     * Helper function: Delete an assignment.
     * This will also delete all submissions due to ON DELETE CASCADE.
     *
     * @param int|string $id The ID of the assignment to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteAssignment($id): bool
    {
        // No change needed, delete() is inherited
        return $this->delete($id);
    }
}