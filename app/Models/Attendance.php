<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Enrollment;

/**
 * Attendance Model
 *
 * Represents the 'attendance' table in the database.
 * Each record in this table corresponds to a student's attendance status
 * (e.g., present, absent) for a specific enrollment on a specific date.
 */
class Attendance extends Model
{
    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'attendance'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public int $enrollment_id;
    public string $date;
    public string $status; // ENUM('present','absent','late','excused')
    public ?string $note = null; // Nullable
    public string $recorded_at;

    /**
     * @var Enrollment|null Cached Enrollment object.
     */
    private ?Enrollment $_enrollment = null; // <-- Use nullable type

    /**
     * Get the Enrollment associated with this attendance record.
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