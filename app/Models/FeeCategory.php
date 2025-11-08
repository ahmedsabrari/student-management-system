<?php

namespace App\Models;

use App\Core\Model;
use App\Models\AcademicYear;
use App\Models\Department;
use \PDO; // <-- Import PDO for FETCH_CLASS constant

/**
 * FeeCategory Model
 *
 * Represents the 'fee_categories' table in the database.
 * This model defines different types of fees (e.g., tuition, lab fee)
 * that can be applied to students.
 */
class FeeCategory extends Model
{
    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'fee_categories'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public string $name;
    public ?string $description = null; // Nullable
    public float $amount; // decimal(10,2) is treated as float
    public ?int $academic_year_id = null; // Nullable
    public ?int $department_id = null; // Nullable
    public int $is_active; // boolean stored as int
    public string $created_at;

    /**
     * @var AcademicYear|null Cached AcademicYear object.
     */
    private ?AcademicYear $_academicYear = null; // <-- Use nullable type

    /**
     * @var Department|null Cached Department object.
     */
    private ?Department $_department = null; // <-- Use nullable type

    /**
     * Get the Academic Year this fee category belongs to (if any).
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
     * Get the Department this fee category belongs to (if any).
     * (BelongsTo relationship)
     *
     * @return Department|object|null
     */
    public function department(): ?object // <-- Use nullable type
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
     * Helper function: Get all active fee categories.
     *
     * @return array An array of active FeeCategory objects.
     */
    public function getAllActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name";
        $stmt = $this->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }

    /**
     * Helper function: Get all fee categories for a specific department.
     *
     * @param int|string $departmentId The ID of the department.
     * @return array An array of FeeCategory objects.
     */
    public function getByDepartment($departmentId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE department_id = ? ORDER BY name";
        $stmt = $this->query($sql, [$departmentId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }

    /**
     * Helper function: Add a new fee category.
     * This is a convenience wrapper for the inherited create() method.
     *
     * @param array $data Associative array of data for the new category.
     * @return string|false The new category's ID or false on failure.
     */
    public function add(array $data)
    {
        // No change needed
        return $this->create($data);
    }

    /**
     * Helper function: Update an existing fee category.
     * This is a convenience wrapper for the inherited update() method.
     *
     * @param int|string $id The ID of the category to update.
     * @param array $data Associative array of data to update.
     * @return bool True on success, false otherwise.
     */
    public function updateCategory($id, array $data): bool
    {
        // No change needed
        return $this->update($id, $data);
    }
}