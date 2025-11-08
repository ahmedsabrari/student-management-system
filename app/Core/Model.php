<?php

namespace App\Core;

use App\Core\Database;
use \PDO;
use \PDOStatement;
use \PDOException; // Import PDOException

/**
 * Abstract Base Model
 *
 * Provides a common set of database interaction methods (CRUD) for all models
 * in the application. It handles the database connection and uses prepared
 * statements for all operations to prevent SQL injection.
 * Child models must define the protected `string $table` property.
 */
abstract class Model
{
    /**
     * @var Database The Database singleton instance.
     * تعريف خاصية $db
     */
    protected Database $db; // Declared with type

    /**
     * @var PDO The PDO connection object.
     * تعريف خاصية $pdo
     */
    protected PDO $pdo; // Declared with type

    /**
     * @var string The database table associated with this model.
     * This MUST be defined in the child class.
     * Example: protected string $table = 'students';
     */
    protected string $table; // Declared with type

    /**
     * @var string The primary key of the table.
     */
    protected string $primaryKey = 'id'; // Declared with type

    /**
     * Model constructor.
     *
     * Gets the database singleton instance and its PDO connection.
     * @throws \Exception if the child model does not define a table.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getConnection();

        // Use get_class() for better compatibility if class name changes
        if (empty($this->table)) {
             // @codeCoverageIgnoreStart
            throw new \Exception('The table property must be defined in the model: ' . get_class($this));
             // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Executes a raw SQL query with parameters and returns the PDOStatement object.
     * This is the core method used by all other database methods.
     *
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @return PDOStatement The prepared statement object after execution.
     * @throws PDOException If the query fails.
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            // Check if prepare() failed
            if ($stmt === false) {
                 // @codeCoverageIgnoreStart
                throw new PDOException("Failed to prepare statement. SQL: " . $sql);
                 // @codeCoverageIgnoreEnd
            }
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            // Log or handle the error appropriately
             // @codeCoverageIgnoreStart
             error_log("Database Query Error: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
             // Re-throw the exception to be caught by global handlers (Bootstrap/App)
             throw $e;
             // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Fetches all records from the model's table (excluding soft-deleted if trait used).
     *
     * @return array An array of objects of the current model's class.
     */
    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        // Add soft delete condition if trait exists and is used
        if (property_exists($this, 'usesSoftDeletes') && $this->usesSoftDeletes === true && defined(get_class($this).'::DELETED_AT')) {
             $deletedAtColumn = static::DELETED_AT; // Use static:: for trait const
             $sql .= " WHERE {$deletedAtColumn} IS NULL";
        }

        $stmt = $this->query($sql);
        // Fetch all results as objects of the current model's class
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class); // Use FETCH_CLASS
    }

    /**
     * Finds a single record by its primary key (excluding soft-deleted if trait used).
     *
     * @param int|string $id The value of the primary key.
     * @return object|false An object of the current model's class, or false if not found.
     */
    public function find($id): object|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        // Add soft delete condition if trait exists and is used
         if (property_exists($this, 'usesSoftDeletes') && $this->usesSoftDeletes === true && defined(get_class($this).'::DELETED_AT')) {
             $deletedAtColumn = static::DELETED_AT;
             $sql .= " AND {$deletedAtColumn} IS NULL";
        }

        $stmt = $this->query($sql, [$id]);
        // Fetch directly into an object of the current model's class
        return $stmt->fetchObject(static::class); // Use fetchObject
    }


    /**
     * Creates a new record in the database.
     *
     * @param array $data An associative array of column => value data.
     * @return string|false The ID of the newly inserted record, or false on failure.
     */
    public function create(array $data)
    {
        // Add Timestamps if trait is used
        if (method_exists($this, 'setTimestampsOnSave')) {
            $this->setTimestampsOnSave($data, true);
        }

        // Filter out keys not present as columns (optional, for safety)
        // $allowedColumns = $this->getTableColumns(); // Need a method to get columns
        // $data = array_intersect_key($data, array_flip($allowedColumns));

        if (empty($data)) {
            error_log("Create failed: Data array is empty after filtering/processing.");
            return false;
        }

        $columns = implode(', ', array_map(fn($col) => "`{$col}`", array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        try {
             $this->query($sql, array_values($data));
             // lastInsertId might return "0" or false on failure, check explicitly
             $lastId = $this->pdo->lastInsertId();
             return ($lastId && $lastId !== "0") ? $lastId : false;
              // @codeCoverageIgnoreStart
        } catch (PDOException $e) {
             error_log("Create failed: " . $e->getMessage() . " | SQL: " . $sql);
             return false;
        }
         // @codeCoverageIgnoreEnd
    }

    /**
     * Updates an existing record by its primary key.
     *
     * @param int|string $id The ID of the record to update.
     * @param array $data An associative array of column => value data.
     * @return bool True on successful update (at least one row affected), false otherwise.
     */
    public function update($id, array $data): bool
    {
        if (empty($data)) {
            // error_log("Update called with empty data for ID {$id}.");
            return false; // Nothing to update
        }

        // Add Timestamps if trait is used
        if (method_exists($this, 'setTimestampsOnSave')) {
            $this->setTimestampsOnSave($data, false);
        }

        // Prevent updating primary key
        // منع تحديث المفتاح الأساسي
        unset($data[$this->primaryKey]);
        if (empty($data)) { // Check again after removing PK
             // error_log("Update called with only primary key for ID {$id}. No changes made.");
            return false; // Or true? Depends on desired behavior for no-op. Let's say false for actual update.
        }

        $setParts = [];
        foreach (array_keys($data) as $key) {
             // Basic check to avoid invalid column names if needed
             // if (preg_match('/^[a-zA-Z0-9_]+$/', $key)) {
                $setParts[] = "`{$key}` = ?";
             // }
        }
        if (empty($setParts)) {
            error_log("Update failed: No valid columns found in data for ID {$id}.");
            return false;
        }
        $setString = implode(', ', $setParts);

        $sql = "UPDATE {$this->table} SET {$setString} WHERE {$this->primaryKey} = ?";

        $values = array_values($data);
        $values[] = $id; // Add the ID for the WHERE clause

        try {
            $stmt = $this->query($sql, $values);
            // rowCount() > 0 indicates that at least one row was actually changed.
            // rowCount() > 0 يشير إلى أنه تم تغيير صف واحد على الأقل بالفعل.
            return $stmt->rowCount() > 0;
             // @codeCoverageIgnoreStart
        } catch (PDOException $e) {
            error_log("Update failed for ID {$id}: " . $e->getMessage() . " | SQL: " . $sql);
            return false;
        }
         // @codeCoverageIgnoreEnd
    }

    /**
     * Deletes a record by its primary key (Permanent delete).
     * Note: SoftDeletes trait overrides this method.
     *
     * @param int|string $id The ID of the record to delete.
     * @return bool True on successful deletion (at least one row affected), false otherwise.
     */
    public function delete($id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        try {
            $stmt = $this->query($sql, [$id]);
            return $stmt->rowCount() > 0;
             // @codeCoverageIgnoreStart
        } catch (PDOException $e) {
             error_log("Delete failed for ID {$id}: " . $e->getMessage());
            return false;
        }
         // @codeCoverageIgnoreEnd
    }

     /**
     * Get the table associated with the model.
     * @return string
     */
     public function getTable(): string
     {
         return $this->table;
     }

     // @TODO: Consider adding a method to get table columns dynamically if needed for validation/filtering in create/update.
     // النظر في إضافة دالة لجلب أعمدة الجدول ديناميكيًا إذا لزم الأمر للتحقق/الفلترة في create/update.
     // protected function getTableColumns(): array { ... }
}

