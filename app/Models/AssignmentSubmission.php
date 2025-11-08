<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Assignment;
use App\Models\Student;
use \PDO; // <-- Import PDO for FETCH_CLASS constant

/**
 * AssignmentSubmission Model
 *
 * Represents the 'assignment_submissions' table in the database.
 * This table stores the work submitted by a student for a specific assignment,
 * along with its grade and feedback.
 */
class AssignmentSubmission extends Model
{
    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'assignment_submissions'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public int $assignment_id;
    public int $student_id;
    public string $submitted_at;
    public ?string $grade = null; // Nullable
    public ?float $points_earned = null; // Nullable (decimal in SQL)
    public ?string $file_path = null; // Nullable
    public ?string $submission_text = null; // Nullable
    public ?string $feedback = null; // Nullable
    public string $status;
    
    /**
     * @var Assignment|null Cached Assignment object.
     */
    private ?Assignment $_assignment = null; // <-- Use nullable type

    /**
     * @var Student|null Cached Student object.
     */
    private ?Student $_student = null; // <-- Use nullable type

    /**
     * Get the Assignment this submission belongs to.
     * (BelongsTo relationship)
     *
     * @return Assignment|object|null
     */
    public function assignment(): ?object // <-- Use nullable type
    {
        if ($this->_assignment === null) {
            $foundAssignment = (new Assignment())->find($this->assignment_id);
            if ($foundAssignment instanceof Assignment) {
                $this->_assignment = $foundAssignment;
            } else {
                return null;
            }
        }
        return $this->_assignment;
    }

    /**
     * Get the Student who made this submission.
     * (BelongsTo relationship)
     *
     * @return Student|object|null
     */
    public function student(): ?object // <-- Use nullable type
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
     * Helper function: Get all submissions for a specific assignment.
     *
     * @param int|string $assignmentId The ID of the assignment.
     * @return array An array of AssignmentSubmission objects.
     */
    public function getByAssignment($assignmentId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE assignment_id = ? ORDER BY submitted_at DESC";
        $stmt = $this->query($sql, [$assignmentId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }

    /**
     * Helper function: Get all submissions from a specific student.
     *
     * @param int|string $studentId The ID of the student.
     * @return array An array of AssignmentSubmission objects.
     */
    public function getByStudent($studentId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE student_id = ? ORDER BY submitted_at DESC";
        $stmt = $this->query($sql, [$studentId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }

    /**
     * Helper function: Add a new submission.
     * This wrapper automatically sets the submission timestamp.
     *
     * @param array $data Associative array of data for the new submission.
     * @return string|false The new record's ID or false on failure.
     */
    public function addSubmission(array $data)
    {
        // Automatically set the submission time if not provided
        if (empty($data['submitted_at'])) {
            $data['submitted_at'] = date('Y-m-d H:i:s');
        }
        return $this->create($data);
    }

    /**
     * Helper function: Update an existing submission (e.g., to add a grade or feedback).
     * This is a convenience wrapper for the inherited update() method.
     *
     * @param int|string $id The ID of the submission to update.
     * @param array $data Associative array of data to update.
     * @return bool True on success, false otherwise.
     */
    public function updateSubmission($id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Helper function: Delete a submission.
     *
     * @param int|string $id The ID of the submission to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteSubmission($id): bool
    {
        return $this->delete($id);
    }
}