<?php

namespace App\Models;

use App\Core\Model;
use App\Models\User;
use \PDO; // <-- Import PDO for FETCH_CLASS constant

/**
 * Message Model
 *
 * Represents the 'messages' table in the database.
 * This model handles the internal messaging system between users
 * (e.g., student to teacher, admin to staff).
 */
class Message extends Model
{
    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'messages'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public int $sender_id;
    public int $receiver_id;
    public ?string $subject = null; // Nullable
    public string $body;
    public int $is_read; // boolean stored as int
    public string $sent_at;
    public ?int $parent_message_id = null; // Nullable

    /**
     * @var User|null Cached sender User object.
     */
    private ?User $_sender = null; // <-- Use nullable type

    /**
     * @var User|null Cached receiver User object.
     */
    private ?User $_receiver = null; // <-- Use nullable type

    /**
     * @var Message|null Cached parent Message object.
     */
    private ?Message $_parent = null; // <-- Use nullable type


    /**
     * Get the User (sender) who sent this message.
     * (BelongsTo relationship)
     *
     * @return User|object|null
     */
    public function sender(): ?object // <-- Use nullable type
    {
        if ($this->_sender === null) {
            $foundUser = (new User())->find($this->sender_id);
            if ($foundUser instanceof User) {
                $this->_sender = $foundUser;
            } else {
                return null;
            }
        }
        return $this->_sender;
    }

    /**
     * Get the User (receiver) this message was sent to.
     * (BelongsTo relationship)
     *
     * @return User|object|null
     */
    public function receiver(): ?object // <-- Use nullable type
    {
        if ($this->_receiver === null) {
            $foundUser = (new User())->find($this->receiver_id);
            if ($foundUser instanceof User) {
                $this->_receiver = $foundUser;
            } else {
                return null;
            }
        }
        return $this->_receiver;
    }

    /**
     * Get the parent message this message is a reply to (if any).
     * (BelongsTo relationship)
     *
     * @return Message|object|null
     */
    public function parentMessage(): ?object // <-- Use nullable type
    {
        if ($this->parent_message_id === null) {
            return null;
        }
        if ($this->_parent === null) {
            $foundParent = (new self())->find($this->parent_message_id);
            if ($foundParent instanceof self) { // Check if it's an instance of Message
                $this->_parent = $foundParent;
            } else {
                return null;
            }
        }
        return $this->_parent;
    }

    /**
     * Helper function: Static method to send a new message.
     *
     * @param int|string $senderId The ID of the sending user.
     * @param int|string $receiverId The ID of the receiving user.
     * @param string $subject The message subject.
     * @param string $body The message body.
     * @param int|string|null $parentId The ID of the parent message (if this is a reply).
     * @return bool True on successful creation, false otherwise.
     */
    public static function sendMessage($senderId, $receiverId, string $subject, string $body, $parentId = null): bool
    {
        $message = new self();
        
        $data = [
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'subject' => $subject,
            'body' => $body,
            'parent_message_id' => $parentId,
            'is_read' => 0, // Default to unread
            // 'sent_at' is handled by the database
        ];

        return (bool) $message->create($data);
    }

    /**
     * Helper function: Mark a specific message as read.
     *
     * @param int|string $id The ID of the message to mark as read.
     * @return bool True on success, false otherwise.
     */
    public function markAsRead($id): bool
    {
        return $this->update($id, ['is_read' => 1]);
    }

    /**
     * Helper function: Get all messages received by a specific user (Inbox).
     *
     * @param int|string $userId The ID of the user.
     * @return array An array of Message objects.
     */
    public function getInbox($userId): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE receiver_id = ? 
                ORDER BY sent_at DESC";
        
        $stmt = $this->query($sql, [$userId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }

    /**
     * Helper function: Get all messages sent by a specific user (Sent Items).
     *
     * @param int|string $userId The ID of the user.
     * @return array An array of Message objects.
     */
    public function getSent($userId): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE sender_id = ? 
                ORDER BY sent_at DESC";
        
        $stmt = $this->query($sql, [$userId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }
}