<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Role;
// use App\Traits\Timestamps; // Uncomment when ready
// use App\Traits\SoftDeletes; // Uncomment when ready
use \PDO;

/**
 * User Model
 * Represents the 'users' table in the database.
 */
class User extends Model
{
    // use Timestamps, SoftDeletes;

    protected string $table = 'users';
    protected string $primaryKey = 'id';

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public int $role_id;
    public string $username;
    public string $email;
    public string $password; // Keep password, though we shouldn't access it directly often
    public string $full_name;
    public ?string $avatar = null; // Nullable string
    public string $status;
    public string $created_at; // Assuming database handles default/on update
    public ?string $updated_at = null; // Nullable string
    public ?string $deleted_at = null; // Nullable string if using SoftDeletes

    /**
     * @var Role|null Cached Role object for this user.
     */
    private ?Role $_role = null;

    /**
     * Get the user's full name.
     * @return string
     */
    public function fullName(): string
    {
        // Now directly accessing the declared public property
        return $this->full_name ?? '';
    }

    /**
     * Get the Role associated with this User (BelongsTo relationship).
     * @return Role|object|null
     */
    public function role(): ?object
    {
        if ($this->_role === null) {
            $roleModel = new Role();
            // Access the declared public property $this->role_id
            $foundRole = $roleModel->find($this->role_id);
             if ($foundRole instanceof Role) {
                 $this->_role = $foundRole;
             } else {
                 // Log error: Role ID might be invalid in the database
                 error_log("Role not found for user ID {$this->id}, role_id {$this->role_id}");
                 return null;
             }
        }
        return $this->_role;
    }

    /**
     * Check if the user has a specific role.
     * @param string $roleName The name of the role (e.g., 'admin').
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        $role = $this->role();
        // Access role's public 'name' property
        return $role && property_exists($role, 'name') && strtolower($role->name) === strtolower($roleName);
    }

    // --- Role checking helpers ---
    public function isAdmin(): bool { return $this->hasRole('admin'); }
    public function isTeacher(): bool { return $this->hasRole('teacher'); }
    public function isStudent(): bool { return $this->hasRole('student'); }
    public function isStaff(): bool { return $this->hasRole('staff'); }
    public function isGuardian(): bool { return $this->hasRole('guardian'); }

    /**
     * Find a user by their email address.
     * @param string $email
     * @return object|false User object or false if not found.
     */
    public function findByEmail(string $email): object|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
        $stmt = $this->query($sql, [$email]);
        return $stmt->fetchObject(static::class);
    }

    /**
     * Find a user by their username.
     * @param string $username
     * @return object|false User object or false if not found.
     */
    public function findByUsername(string $username): object|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = ? LIMIT 1";
        $stmt = $this->query($sql, [$username]);
        return $stmt->fetchObject(static::class);
    }
}

