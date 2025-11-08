<?php

namespace App\Models;

use App\Core\Model;
use App\Models\ClassModel;
use \PDO; // <-- Import PDO for FETCH_CLASS constant

/**
 * Timetable Model
 *
 * Represents the 'timetables' table in the database.
 * This model stores the schedule (days, times, rooms)
 * for each class.
 */
class Timetable extends Model
{
    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'timetables'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public int $class_id;
    public ?string $day_of_week = null; // Nullable (ENUM)
    public ?string $start_time = null; // Nullable
    public ?string $end_time = null; // Nullable
    public ?string $room = null; // Nullable
    public string $created_at;

    /**
     * @var ClassModel|null Cached ClassModel object.
     */
    private ?ClassModel $_class = null; // <-- Use nullable type

    /**
     * Get the Class this schedule belongs to.
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
     * Helper function: Get all schedule entries for a specific class.
     *
     * @param int|string $classId The ID of the class.
     * @return array An array of Timetable objects.
     */
    public function getByClass($classId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE class_id = ? ORDER BY day_of_week, start_time";
        $stmt = $this->query($sql, [$classId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }

    /**
     * Helper function: Add a new schedule entry.
     * This is a convenience wrapper for the inherited create() method.
     *
     * @param array $data Associative array of data for the new schedule.
     * @return string|false The new record's ID or false on failure.
     */
    public function addSchedule(array $data)
    {
        // No change needed
        return $this->create($data);
    }

    /**
     * Helper function: Update an existing schedule entry.
     * This is a convenience wrapper for the inherited update() method.
     *
     * @param int|string $id The ID of the schedule to update.
     * @param array $data Associative array of data to update.
     * @return bool True on success, false otherwise.
     */
    public function updateSchedule($id, array $data): bool
    {
        // No change needed
        return $this->update($id, $data);
    }

    /**
     * Helper function: Delete a schedule entry.
     *
     * @param int|string $id The ID of the schedule to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteSchedule($id): bool
    {
        // No change needed
        return $this->delete($id);
    }
}