<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Student;
use \PDO; // Import PDO

/**
 * Guardian Model
 *
 * Represents the 'guardians' table in the database.
 * This model stores information about the parents or guardians
 * associated with a student.
 */
class Guardian extends Model
{
    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'guardians'; // <-- ADD string type

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- ADD string type

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public int $student_id;
    public string $full_name;
    public string $relationship;
    public ?string $email = null; // Nullable
    public ?string $phone = null; // Nullable
    public ?string $address = null; // Nullable
    public int $is_primary; // Assuming 0 or 1 (boolean stored as int)
    public string $created_at;

    /**
     * @var Student|null Cached Student object.
     */
    private ?Student $_student = null; // Use nullable type

    /**
     * Get the Student this guardian is associated with.
     * (BelongsTo relationship)
     *
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
     * Helper function: Get all guardians for a specific student.
     *
     * @param int|string $studentId The ID of the student.
     * @return array An array of Guardian objects.
     */
    public function getByStudent($studentId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE student_id = ?";
        $stmt = $this->query($sql, [$studentId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // Fetch as Guardian objects
    }

    /**
     * Helper function: Add a new guardian.
     * This is a convenience wrapper for the inherited create() method.
     *
     * @param array $data Associative array of data for the new guardian.
     * @return string|false The new guardian's ID or false on failure.
     */
    public function add(array $data)
    {
        return $this->create($data);
    }

    /**
     * Helper function: Update an existing guardian.
     * This is a convenience wrapper for the inherited update() method.
     *
     * @param int|string $id The ID of the guardian to update.
     * @param array $data Associative array of data to update.
     * @return bool True on success, false otherwise.
     */
    public function updateGuardian($id, array $data): bool
    {
        return $this->update($id, $data);
    }
}
