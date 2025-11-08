<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Enrollment;

/**
 * Grade Model
 *
 * Represents the 'grades' table in the database.
 * Each record in this table corresponds to a specific grade
 * given for a single enrollment.
 */
class Grade extends Model
{
    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'grades'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public int $enrollment_id;
    public ?string $grade = null; // Nullable
    public ?float $grade_points = null; // Nullable (decimal)
    public ?string $remarks = null; // Nullable
    public string $recorded_at;

    /**
     * @var Enrollment|null Cached Enrollment object.
     */
    private ?Enrollment $_enrollment = null; // <-- Use nullable type

    /**
     * Get the Enrollment associated with this grade.
     * (BelongsTo relationship)
     *
     * @return Enrollment|object|null
     */
    public function enrollment(): ?object // <-- Use nullable type
    {
        if ($this->_enrollment === null) {
            $foundEnrollment = (new Enrollment())->find($this->enrollment_id);
            if ($foundEnrollment instanceof Enrollment) {
                $this->_enrollment = $foundEnrollment;
            } else {
                return null;
            }
        }
        return $this->_enrollment;
    }
}