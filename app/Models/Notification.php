<?php

namespace App\Models;

use App\Core\Model;
use App\Models\User;
use \PDO; // <-- Import PDO for FETCH_CLASS constant

/**
 * Notification Model
 *
 * Represents the 'notifications' table in the database.
 * This model stores messages, alerts, and other notifications
 * directed to specific users.
 */
class Notification extends Model
{
    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'notifications'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public int $user_id;
    public string $title;
    public string $message;
    public string $type; // ENUM
    public int $is_read; // boolean stored as int
    public string $created_at;

    /**
     * @var User|null Cached User object.
     */
    private ?User $_user = null; // <-- Use nullable type

    /**
     * Get the User this notification belongs to.
     * (BelongsTo relationship)
     *
     * @return User|object|null
     */
    public function user(): ?object // <-- Use nullable type
    {
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
     * Helper function: Static method to create a new notification for a user.
     *
     * @param int|string $userId The ID of the user to notify.
     * @param string $title The title of the notification.
     * @param string $message The notification message.
     * @param string $type The type of notification (e.g., 'info', 'warning', 'success').
     * @return bool True on successful creation, false otherwise.
     */
    public static function notifyUser($userId, string $title, string $message, string $type = 'info'): bool
    {
        // Create a new instance of this model to call the non-static create()
        $notification = new self();
        
        $data = [
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => 0, // Default to unread
            // 'created_at' is handled by the database
        ];

        return (bool) $notification->create($data);
    }

    /**
     * Helper function: Mark a specific notification as read.
     *
     * @param int|string $id The ID of the notification to mark as read.
     * @return bool True on success, false otherwise.
     */
    public function markAsRead($id): bool
    {
        // We only update 'is_read', timestamps will be handled by the database
        return $this->update($id, ['is_read' => 1]);
    }

    /**
     * Helper function: Get all unread notifications for a specific user.
     *
     * @param int|string $userId The ID of the user.
     * @return array An array of Notification objects.
     */
    public function getUnread($userId): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = ? AND is_read = 0 
                ORDER BY created_at DESC";
        
        $stmt = $this->query($sql, [$userId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }
}