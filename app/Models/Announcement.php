<?php

namespace App\Models;

use App\Core\Model;
use App\Models\User;
use App\Models\Department;
// use App\Traits\Timestamps; // Uncomment when ready
use \PDO; // <-- Import PDO for FETCH_CLASS constant

/**
 * Announcement Model
 *
 * Represents the 'announcements' table in the database.
 * This model stores announcements intended for various audiences
 * within the system.
 */
class Announcement extends Model
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
    protected string $table = 'announcements'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public string $title;
    public string $content;
    public int $author_id;
    public string $target_audience;
    public ?int $department_id = null; // Nullable
    public int $is_published; // Assuming 0 or 1
    public ?string $publish_at = null; // Nullable
    public ?string $expires_at = null; // Nullable
    public string $created_at;
    public ?string $updated_at = null; // Nullable

    /**
     * @var User|null Cached User (author) object.
     */
    private ?User $_author = null; // <-- Use nullable type

    /**
     * @var Department|null Cached Department object.
     */
    private ?Department $_department = null; // <-- Use nullable type

    /**
     * Get the User (author) who created this announcement.
     * (BelongsTo relationship)
     *
     * @return User|object|null
     */
    public function author(): ?object // <-- Use nullable type
    {
        if ($this->_author === null) {
            $foundUser = (new User())->find($this->author_id);
            if ($foundUser instanceof User) {
                $this->_author = $foundUser;
            } else {
                return null;
            }
        }
        return $this->_author;
    }

    /**
     * Get the Department this announcement is targeted at (if any).
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
     * Helper function: Get all currently active and published announcements.
     * Filters by publication status, date range, and optionally department.
     *
     * @param int|string|null $departmentId Optional department ID to filter by.
     * @return array An array of Announcement objects.
     */
    public function getAllPublished($departmentId = null): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_published = 1
                  AND (publish_at IS NULL OR publish_at <= NOW())
                  AND (expires_at IS NULL OR expires_at >= NOW())";
        
        $params = [];

        if ($departmentId !== null) {
            // Fetch announcements for this specific department OR global announcements (department_id IS NULL)
            $sql .= " AND (department_id = ? OR department_id IS NULL)";
            $params[] = $departmentId;
        }
        
        $sql .= " ORDER BY publish_at DESC, created_at DESC";

        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }

    /**
     * Helper function: Publish an announcement.
     *
     * @param int|string $id The ID of the announcement to publish.
     * @return bool True on success, false otherwise.
     */
    public function publish($id): bool
    {
        // Set publish_at to now if it's not already set in the future
        $data = [
            'is_published' => 1,
            'publish_at' => date('Y-m-d H:i:s')
        ];
        return $this->update($id, $data);
    }

    /**
     * Helper function: Unpublish an announcement.
     *
     * @param int|string $id The ID of the announcement to unpublish.
     * @return bool True on success, false otherwise.
     */
    public function unpublish($id): bool
    {
        $data = ['is_published' => 0];
        return $this->update($id, $data);
    }
    
    /**
     * Helper function: Delete an announcement.
     * (This is a wrapper for the inherited delete method for API consistency).
     *
     * @param int|string $id The ID of the announcement to delete.
     * @return bool True on success, false otherwise.
     */
    public function delete($id): bool // Overriding the base model's delete
    {
        return parent::delete($id); // Call the parent's delete method
    }
}