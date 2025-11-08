<?php

namespace App\Models;

use App\Core\Model;

/**
 * Setting Model
 *
 * Represents the 'settings' table in the database.
 * This table is a simple key-value store for application-wide settings
 * that can be modified dynamically by an administrator.
 */
class Setting extends Model
{
    /**
     * The database table associated with the model.
     * @var string
     */
    protected string $table = 'settings'; // <-- FIXED

    /**
     * The primary key associated with the table.
     * @var string
     */
    protected string $primaryKey = 'id'; // <-- FIXED

    // --- Declare properties corresponding to database columns ---
    // --- التصريح عن الخصائص المطابقة لأعمدة قاعدة البيانات ---
    public int $id;
    public string $key_name;
    public string $value;
    public string $updated_at;

    /**
     * Get a setting's value by its key.
     *
     * @param string $key The 'key_name' of the setting to retrieve.
     * @param mixed $default The default value to return if the key is not found.
     * @return mixed The setting's value or the default.
     */
    public function get(string $key, $default = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE key_name = ? LIMIT 1";
        $stmt = $this->query($sql, [$key]);
        $result = $stmt->fetchObject(static::class); // <-- FIXED (Fetch method)

        // Check if result is a valid object and has the 'value' property
        // التحقق مما إذا كانت النتيجة كائنًا صالحًا وتحتوي على خاصية 'value'
        return ($result && property_exists($result, 'value')) ? $result->value : $default;
    }

    /**
     * Set a setting's value.
     * This performs an "upsert": it updates the key if it exists,
     * or creates it if it does not.
     *
     * @param string $key The 'key_name' of the setting.
     * @param mixed $value The 'value' to store for the setting.
     * @return bool True on success, false on failure.
     */
    public function set(string $key, $value): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE key_name = ? LIMIT 1";
        $stmt = $this->query($sql, [$key]);
        $existing = $stmt->fetchObject(static::class); // <-- FIXED (Fetch method)

        if ($existing) {
            // Key exists, UPDATE it
            return $this->update($existing->id, ['value' => $value]);
        } else {
            // Key does not exist, CREATE it
            $newId = $this->create(['key_name' => $key, 'value' => $value]);
            return (bool) $newId;
        }
    }
}