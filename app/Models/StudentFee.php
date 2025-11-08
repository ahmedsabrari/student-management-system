<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Student;
use App\Models\FeeCategory;
use App\Models\AcademicYear;
// use App\Traits\Timestamps; // Uncomment when ready
use \PDO; // <-- Import PDO for FETCH_CLASS constant

/**
 * StudentFee Model
 *
 * Represents the 'student_fees' table. This table tracks the financial
 * obligations of a student for a specific fee category and academic year,
 * including their payment status.
 */
class StudentFee extends Model
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
    protected string $table = 'student_fees'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public int $student_id;
    public int $fee_category_id;
    public ?int $academic_year_id = null; // Nullable
    public ?float $amount_due = null; // Nullable (decimal)
    public float $amount_paid; // Default 0
    public ?string $due_date = null; // Nullable
    public ?string $paid_date = null; // Nullable
    public string $status; // ENUM
    public ?string $payment_method = null; // Nullable
    public ?string $transaction_id = null; // Nullable
    public string $created_at;
    public ?string $updated_at = null; // Nullable

    /**
     * @var Student|null Cached Student object.
     */
    private ?Student $_student = null; // <-- Use nullable type

    /**
     * @var FeeCategory|null Cached FeeCategory object.
     */
    private ?FeeCategory $_feeCategory = null; // <-- Use nullable type

    /**
     * @var AcademicYear|null Cached AcademicYear object.
     */
    private ?AcademicYear $_academicYear = null; // <-- Use nullable type

    /**
     * Get the Student this fee record is associated with.
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
     * Get the FeeCategory this fee record belongs to.
     * (BelongsTo relationship)
     *
     * @return FeeCategory|object|null
     */
    public function feeCategory(): ?object // <-- Use nullable type
    {
        if ($this->_feeCategory === null) {
            $foundCategory = (new FeeCategory())->find($this->fee_category_id);
             if ($foundCategory instanceof FeeCategory) {
                $this->_feeCategory = $foundCategory;
            } else {
                return null;
            }
        }
        return $this->_feeCategory;
    }

    /**
     * Get the Academic Year this fee is associated with.
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
     * Helper function: Get all fee records for a specific student.
     *
     * @param int|string $studentId The ID of the student.
     * @return array An array of StudentFee objects.
     */
    public function getByStudent($studentId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE student_id = ? ORDER BY due_date DESC";
        $stmt = $this->query($sql, [$studentId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }

    /**
     * Helper function: Get all fees that are not fully paid ('pending' or 'overdue').
     *
     * @return array An array of StudentFee objects.
     */
    public function getPendingFees(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'pending' OR status = 'overdue' ORDER BY due_date ASC";
        $stmt = $this->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }

    /**
     * Helper function: Add a new student fee record.
     * This is a convenience wrapper for the inherited create() method.
     *
     * @param array $data Associative array of data for the new fee record.
     * @return string|false The new record's ID or false on failure.
     */
    public function add(array $data)
    {
        // No change needed
        return $this->create($data);
    }

    /**
     * Helper function: Update an existing student fee payment.
     *
     * @param int|string $id The ID of the student_fee record to update.
     * @param array $data Associative array of data (e.g., amount_paid, status, paid_date).
     * @return bool True on success, false otherwise.
     */
    public function updatePayment($id, array $data): bool
    {
        // No change needed
        return $this->update($id, $data);
    }
}