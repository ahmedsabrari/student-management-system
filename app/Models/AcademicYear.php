<?php

namespace App\Models;

use App\Core\Model;
use \PDO; // <-- Import PDO for FETCH_CLASS constant

/**
 * AcademicYear Model
 *
 * Represents the 'academic_years' table in the database.
 * This model manages academic sessions (e.g., "2024-2025") and
 * tracks which one is currently active.
 */
class AcademicYear extends Model
{
    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'academic_years'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public string $name;
    public string $start_date;
    public string $end_date;
    public int $is_current; // Stored as 0 or 1
    public string $created_at;

    /**
     * Helper function: Get the currently active academic year.
     *
     * @return object|false The active academic year object (as AcademicYear), or false if not set.
     */
    public function getCurrentYear(): object|false // <-- FIXED (Return type)
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_current = ? LIMIT 1";
        $stmt = $this->query($sql, [1]);
        return $stmt->fetchObject(static::class); // <-- FIXED (Fetch method)
    }

    /**
     * Helper function: Get all academic years, ordered by start date.
     *
     * @return array An array of AcademicYear objects.
     */
    public function getAll(): array
    {
        // This method is a more descriptive alias for the inherited `all()`
        // with a specific ordering.
        $sql = "SELECT * FROM {$this->table} ORDER BY start_date DESC";
        $stmt = $this->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }

    /**
     * Helper function: Add a new academic year.
     * This is a convenience wrapper for the inherited create() method.
     *
     * @param array $data Associative array of data for the new academic year.
     * @return string|false The new record's ID or false on failure.
     */
    public function add(array $data)
    {
        // No change needed, create() is inherited
        return $this->create($data);
    }

    /**
     * Helper function: Set a specific academic year as the current one.
     * This method uses a transaction to ensure that only one year
     * is marked as 'current' at any given time.
     *
     * @param int|string $id The ID of the academic year to set as current.
     * @return bool True on success, false on failure.
     */
    public function setCurrent($id): bool
    {
        // $this->pdo is the PDO connection object inherited from App\Core\Model
        try {
            $this->pdo->beginTransaction();

            // Step 1: Unset any other 'current' year
            $sql_unset = "UPDATE {$this->table} SET is_current = 0 WHERE is_current = 1";
            $this->query($sql_unset);

            // Step 2: Set the new 'current' year
            $sql_set = "UPDATE {$this->table} SET is_current = 1 WHERE id = ?";
            $this->query($sql_set, [$id]);

            // If both queries succeed, commit the transaction
            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            // If anything fails, roll back the changes
            $this->pdo->rollBack();
            // @TODO: Log the error message: $e->getMessage()
            error_log("Failed to set current academic year: " . $e->getMessage()); // Added logging
            return false;
        }
    }
}