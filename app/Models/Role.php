<?php

namespace App\Models;

use App\Core\Model;
use App\Models\User;
use \PDO; // Import PDO

/**
 * Role Model
 * ... ( باقي التعليقات ) ...
 */
class Role extends Model
{
    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'roles'; // <-- ADD string type

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- ADD string type

    // --- Declare properties corresponding to database columns ---
    public int $id;
    public string $name;
    public ?string $description = null; // Nullable
    public string $created_at;

    /**
     * Get all users associated with this role.
     * @return array An array of User objects.
     */
    public function users(): array
    {
        $userModel = new User();
        $stmt = $userModel->query("SELECT * FROM users WHERE role_id = ?", [$this->id]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, User::class); // Fetch as User objects
    }

    /**
     * Check if this role has a specific permission.
     * @param string $permissionName The machine-name of the permission.
     * @return bool True if the role has the permission, false otherwise.
     */
    public function hasPermission(string $permissionName): bool
    {
        // This query joins the role_permissions pivot table with the permissions table
        $sql = "SELECT rp.role_id
                FROM role_permissions rp
                JOIN permissions p ON rp.permission_id = p.id
                WHERE rp.role_id = ? AND p.name = ? LIMIT 1"; // Added LIMIT 1

        $result = $this->query($sql, [$this->id, $permissionName])->fetch();

        return (bool) $result;
    }

    /**
     * Find a role by its unique name.
     * @param string $name The name of the role (e.g., 'admin').
     * @return object|false Role object or false if not found.
     */
    public function findByName(string $name): object|false // Updated return type
    {
        $sql = "SELECT * FROM {$this->table} WHERE name = ? LIMIT 1";
        $stmt = $this->query($sql, [$name]);
        return $stmt->fetchObject(static::class); // Fetch as Role object
    }
}
