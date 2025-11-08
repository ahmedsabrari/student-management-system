<?php

namespace App\Models;

use App\Core\Model;
use App\Models\User;
use \PDO; // <-- Import PDO for FETCH_CLASS constant

/**
 * Log Model
 *
 * Represents the 'logs' table in the database.
 * This model is used to record user activities and significant
 * system events for auditing and debugging purposes.
 */
class Log extends Model
{
    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'logs'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public ?int $user_id = null; // Nullable
    public ?string $action = null; // Nullable
    public ?string $description = null; // Nullable
    public ?string $ip_address = null; // Nullable
    public ?string $user_agent = null; // Nullable
    public string $created_at;

    /**
     * @var User|null Cached User object.
     */
    private ?User $_user = null; // <-- Use nullable type

    /**
     * Get the User associated with this log entry (if any).
     * (BelongsTo relationship)
     *
     * @return User|object|null
     */
    public function user(): ?object // <-- Use nullable type
    {
        if ($this->user_id === null) {
            return null; // For system logs not tied to a user
        }
        if ($this->_user === null) {
            $foundUser = (new User())->find($this->user_id);
            if ($foundUser instanceof User) {
                $this->_user = $foundUser;
            } else {
                return null;
            }
        }
        return $this->_user;
    }

    /**
     * Helper function: Static method to quickly create a new log entry.
     *
     * @param int|null $userId The ID of the user performing the action (null for system actions).
     * @param string $action A short code for the action (e.g., 'LOGIN', 'STUDENT_CREATE').
     * @param string|null $description A detailed description of the event.
     * @param string|null $ip The IP address (auto-detected if null).
     * @param string|null $userAgent The user agent (auto-detected if null).
     * @return bool True on successful creation, false otherwise.
     */
    public static function logAction($userId, string $action, string $description = null, string $ip = null, string $userAgent = null): bool
    {
        // Create a new instance of this model to call the non-static create()
        $log = new self();
        
        $data = [
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $ip ?? $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN',
            'user_agent' => $userAgent ?? $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN',
        ];

        return (bool) $log->create($data);
    }

    /**
     * Helper function: Get the latest log entries for a specific user.
     *
     * @param int|string $userId The ID of the user.
     * @param int $limit The maximum number of logs to retrieve.
     * @return array An array of Log objects.
     */
    public function getLogsByUser($userId, int $limit = 50): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        $stmt = $this->query($sql, [$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }
    
    /**
     * Helper function: Delete a log entry.
     * (This is a wrapper for the inherited delete method for API consistency).
     *
     * @param int|string $id The ID of the log to delete.
     * @return bool True on success, false otherwise.
     */
    public function delete($id): bool // Overriding the base model's delete
    {
        return parent::delete($id); // Call the parent's delete method
    }
}