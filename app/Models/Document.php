<?php

namespace App\Models;

use App\Core\Model;
// use App\Traits\SoftDeletes; // Uncomment when ready
use \PDO; // <-- Import PDO for FETCH_CLASS constant

/**
 * Document Model
 *
 * Represents the 'documents' table in the database.
 * This model uses a polymorphic-like relationship ('owner_type', 'owner_id')
 * to store files (e.g., transcripts, certificates) for different
 * entities like students, teachers, or admins.
 */
class Document extends Model
{
    /**
     * Use soft deletes (deleted_at).
     * @TODO: This trait needs to be created and implemented.
     */
    // use SoftDeletes; // We will uncomment this when the trait is built

    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'documents'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public string $owner_type; // ENUM('student','teacher','admin')
    public int $owner_id;
    public ?string $title = null; // Nullable
    public string $file_path;
    public ?int $file_size = null; // Nullable
    public ?string $mime_type = null; // Nullable
    public string $uploaded_at;
    public ?string $deleted_at = null; // Nullable

    /**
     * Get the owner of this document (polymorphic relationship).
     *
     * @return Model|object|null The owner model (e.g., Student, Teacher, User).
     */
    public function owner(): ?object // <-- Use nullable type
    {
        // Property $owner_type is now declared and available
        if ($this->owner_type === 'student') {
            $owner = (new Student())->find($this->owner_id);
            return $owner instanceof Student ? $owner : null;
        }
        if ($this->owner_type === 'teacher') {
            $owner = (new Teacher())->find($this->owner_id);
            return $owner instanceof Teacher ? $owner : null;
        }
        if ($this->owner_type === 'admin') {
            $owner = (new User())->find($this->owner_id);
            return $owner instanceof User ? $owner : null;
        }
        
        return null;
    }

    /**
     * Helper function: Get all documents for a specific owner.
     *
     * @param string $type The owner type (e.g., 'student', 'teacher').
     * @param int|string $ownerId The ID of the owner.
     * @return array An array of Document objects.
     */
    public function getByOwner(string $type, $ownerId): array
    {
        // @TODO: Add check for soft deletes if trait is used
        $sql = "SELECT * FROM {$this->table} WHERE owner_type = ? AND owner_id = ? AND deleted_at IS NULL ORDER BY uploaded_at DESC";
        $stmt = $this->query($sql, [$type, $ownerId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // <-- FIXED (Fetch method)
    }

    /**
     * Helper function: Add a new document record.
     * This is a convenience wrapper for the inherited create() method.
     *
     * @param array $data Associative array of data for the new document.
     * @return string|false The new record's ID or false on failure.
     */
    public function upload(array $data)
    {
        if (empty($data['uploaded_at'])) {
            $data['uploaded_at'] = date('Y-m-d H:i:s');
        }
        return $this->create($data);
    }

    /**
     * Helper function: Soft delete a document.
     * Sets the 'deleted_at' timestamp.
     *
     * @param int|string $id The ID of the document to soft delete.
     * @return bool True on success, false otherwise.
     */
    public function softDelete($id): bool
    {
        $data = ['deleted_at' => date('Y-m-d H:i:s')];
        return $this->update($id, $data);
    }
    
    /**
     * Helper function: Permanently delete a document from the database.
     *
     * @param int|string $id The ID of the document to delete.
     * @return bool True on success, false otherwise.
     */
    public function permanentDelete($id): bool
    {
        return $this->delete($id);
    }
}