<?php

namespace App\Models;

use App\Core\Model;
use App\Models\User;
use \PDO; // <-- Import PDO for FETCH_CLASS constant

/**
 * Event Model
 *
 * Represents the 'events' table in the database.
 * This model stores information about calendar events such as holidays,
 * academic deadlines, or social activities.
 */
class Event extends Model
{
    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'events'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public string $title;
    public ?string $description = null; // Nullable
    public string $event_date;
    public ?string $start_time = null; // Nullable
    public ?string $end_time = null; // Nullable
    public ?string $location = null; // Nullable
    public int $organizer_id;
    public string $event_type; // ENUM
    public string $created_at;

    /**
     * @var User|null Cached User (organizer) object.
     */
    private ?User $_organizer = null; // <-- Use nullable type

    /**
     * Get the User (organizer) who created this event.
     * (BelongsTo relationship)
     *
     * @return User|object|null
     */
    public function organizer(): ?object // <-- Use nullable type
    {
        if ($this->_organizer === null) {
            $foundUser = (new User())->find($this->organizer_id);
            if ($foundUser instanceof User) {
                $this->_organizer = $foundUser;
            } else {
                return null;
            }
        }
        return $this->_organizer;
    }

    /**
     * Helper function: Get upcoming events.
     * Fetches events that are happening today or in the future.
     *
     * @param int $limit The maximum number of events to return.
     * @return array An array of Event objects.
     */
    public function getUpcomingEvents(int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE event_date >= CURDATE() 
                ORDER BY event_date ASC, start_time ASC 
                LIMIT ?";
        
        $stmt = $this->query($sql, [$limit]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }

    /**
     * Helper function: Get all events of a specific type.
     *
     * @param string $type The event type (e.g., 'academic', 'social', 'sports', 'holiday').
     * @return array An array of Event objects.
     */
    public function getEventsByType(string $type): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE event_type = ? ORDER BY event_date DESC";
        $stmt = $this->query($sql, [$type]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }
    
    /**
     * Helper function: Delete an event.
     * (This is a wrapper for the inherited delete method for API consistency).
     *
     * @param int|string $id The ID of the event to delete.
     * @return bool True on success, false otherwise.
     */
    public function delete($id): bool // Overriding the base model's delete
    {
        return parent::delete($id); // Call the parent's delete method
    }
}