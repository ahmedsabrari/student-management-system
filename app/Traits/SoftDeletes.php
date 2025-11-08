<?php

declare(strict_types=1); // Enforce strict types

namespace App\Traits;

use App\Core\Model; // Assuming Model class is in App\Core
use DateTime;
use Exception;

/**
 * SoftDeletes Trait
 *
 * Provides soft delete functionality for models. Instead of deleting records
 * from the database, a `deleted_at` timestamp is set. Queries will automatically
 * exclude soft-deleted records unless explicitly requested.
 *
 * Usage:
 * class YourModel extends \App\Core\Model {
 * use SoftDeletes;
 * // ... other model properties ...
 * }
 *
 * Assumes the model's table has a nullable TIMESTAMP column named 'deleted_at'.
 * Note: This trait overrides the all() and find() methods from the base Model.
 */
trait SoftDeletes
{
    /**
     * The name of the "deleted at" column.
     * اسم عمود "تاريخ الحذف".
     * @var string
     */
    const DELETED_AT = 'deleted_at';

    /**
     * Indicates if the model uses soft deletes.
     * يشير إلى ما إذا كان النموذج يستخدم الحذف المنطقي.
     * @var bool
     */
    public bool $usesSoftDeletes = true;

    /**
     * Perform a soft delete on the model instance.
     * تنفيذ حذف منطقي على نسخة النموذج.
     * Sets the `deleted_at` column to the current timestamp.
     * يعين عمود `deleted_at` إلى الطابع الزمني الحالي.
     * This method overrides the base Model's delete method.
     * هذه الدالة تتجاوز دالة الحذف في الـ Model الأساسي.
     *
     * @return bool True on success, false otherwise.
     */
    public function delete(): bool
    {
        // Ensure the model instance has an ID
        // التأكد من أن نسخة النموذج لديها ID
        $keyName = $this->primaryKey;
        if (!property_exists($this, $keyName) || !$this->$keyName) {
            // Cannot delete a model that doesn't exist or has no ID property
            // لا يمكن حذف نموذج غير موجود أو ليس لديه خاصية ID
            return false;
        }

        // Avoid re-deleting if already deleted
        // تجنب إعادة الحذف إذا كان محذوفًا بالفعل
        if ($this->isDeleted()) {
            return true; // Or false depending on desired behavior
        }

        try {
            $now = (new DateTime())->format('Y-m-d H:i:s');
            $sql = "UPDATE {$this->table} SET " . self::DELETED_AT . " = ? WHERE {$this->primaryKey} = ?";

            // Execute the query using the base model's query method
            // تنفيذ الاستعلام باستخدام دالة query من الـ Model الأساسي
            $stmt = $this->query($sql, [$now, $this->$keyName]);

            // Update the current instance property if the update was successful
            // تحديث خاصية النسخة الحالية إذا نجح التحديث
            if ($stmt->rowCount() > 0) {
                 $deletedAtColumn = self::DELETED_AT;
                 // Ensure the property exists before assigning
                 if (property_exists($this, $deletedAtColumn)) {
                    $this->$deletedAtColumn = $now;
                 }
                 return true;
            }
            return false;
             // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            // Log error
            // error_log("Soft delete failed: " . $e->getMessage());
            return false;
        }
         // @codeCoverageIgnoreEnd
    }

    /**
     * Restore a soft-deleted model instance.
     * استعادة نسخة نموذج محذوفة منطقيًا.
     * Sets the `deleted_at` column to NULL.
     * يعين عمود `deleted_at` إلى NULL.
     *
     * @return bool True on success, false otherwise.
     */
    public function restore(): bool
    {
        $keyName = $this->primaryKey;
        if (!property_exists($this, $keyName) || !$this->$keyName) {
            return false;
        }

        // Avoid restoring if not deleted
        // تجنب الاستعادة إذا لم يكن محذوفًا
        if (!$this->isDeleted()) {
            return true;
        }

        try {
            $sql = "UPDATE {$this->table} SET " . self::DELETED_AT . " = NULL WHERE {$this->primaryKey} = ?";
            $stmt = $this->query($sql, [$this->$keyName]);

            // Update the current instance property if the update was successful
            // تحديث خاصية النسخة الحالية إذا نجح التحديث
            if ($stmt->rowCount() > 0) {
                 $deletedAtColumn = self::DELETED_AT;
                 if (property_exists($this, $deletedAtColumn)) {
                    $this->$deletedAtColumn = null;
                 }
                return true;
            }
            return false;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            // Log error
             // error_log("Restore failed: " . $e->getMessage());
            return false;
        }
         // @codeCoverageIgnoreEnd
    }

    /**
     * Force a permanent delete on the model instance.
     * فرض حذف دائم على نسخة النموذج.
     * This bypasses the soft delete mechanism.
     * يتجاوز آلية الحذف المنطقي.
     *
     * @return bool True on success, false otherwise.
     */
    public function forceDelete(): bool
    {
        $keyName = $this->primaryKey;
        if (!property_exists($this, $keyName) || !$this->$keyName) {
            return false;
        }

        try {
            // Directly call the base delete logic using a raw query
            // استدعاء منطق الحذف الأساسي مباشرة باستخدام استعلام خام
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
            $stmt = $this->query($sql, [$this->$keyName]);
            return $stmt->rowCount() > 0;
             // @codeCoverageIgnoreStart
        } catch (Exception $e) {
             // error_log("Force delete failed: " . $e->getMessage());
            return false;
        }
         // @codeCoverageIgnoreEnd
    }

    /**
     * Determine if the model instance has been soft-deleted.
     * تحديد ما إذا كانت نسخة النموذج قد تم حذفها منطقيًا.
     *
     * @return bool True if soft-deleted, false otherwise.
     */
    public function isDeleted(): bool
    {
        $deletedAtColumn = self::DELETED_AT;
        // Check if the property exists and is not null
        // التحقق مما إذا كانت الخاصية موجودة وليست null
        return property_exists($this, $deletedAtColumn) && !empty($this->$deletedAtColumn);
    }

    /**
     * Override: Get all records from the model's table, excluding soft-deleted ones.
     * تجاوز: جلب جميع السجلات من جدول النموذج، باستثناء المحذوفة منطقيًا.
     *
     * @return array An array of objects representing the non-deleted records.
     */
    public function all(): array
    {
        // Call the base query method with the soft delete condition
        // استدعاء دالة query الأساسية مع شرط الحذف المنطقي
        $sql = "SELECT * FROM {$this->table} WHERE " . self::DELETED_AT . " IS NULL";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Override: Finds a single non-deleted record by its primary key.
     * تجاوز: البحث عن سجل واحد غير محذوف بواسطة مفتاحه الأساسي.
     *
     * @param int|string $id The value of the primary key.
     * @return mixed An object representing the record, or false if not found or soft-deleted.
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? AND " . self::DELETED_AT . " IS NULL";
        return $this->query($sql, [$id])->fetch();
    }


    /**
     * Static: Get all records from the table, including soft-deleted ones.
     * ثابت: جلب جميع السجلات من الجدول، بما في ذلك المحذوفة منطقيًا.
     *
     * @return array An array of objects representing all records.
     */
    public static function withTrashed(): array
    {
        // Need to instantiate the model to access non-static properties/methods
        // نحتاج إلى إنشاء نسخة من النموذج للوصول إلى الخصائص/الدوال غير الثابتة
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table}";
        return $instance->query($sql)->fetchAll();
    }

    /**
     * Static: Get only the soft-deleted records from the table.
     * ثابت: جلب السجلات المحذوفة منطقيًا فقط من الجدول.
     *
     * @return array An array of objects representing the soft-deleted records.
     */
    public static function onlyTrashed(): array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} WHERE " . self::DELETED_AT . " IS NOT NULL";
        return $instance->query($sql)->fetchAll();
    }

     /**
     * Static: Find a record by ID, including trashed ones.
     * ثابت: البحث عن سجل بواسطة ID، بما في ذلك المحذوفة.
     *
     * @param int|string $id
     * @return mixed
     */
    public static function findWithTrashed($id)
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ?";
        return $instance->query($sql, [$id])->fetch();
    }

     /**
     * Static: Find a record by ID, only if it is trashed.
     * ثابت: البحث عن سجل بواسطة ID، فقط إذا كان محذوفًا منطقيًا.
     *
     * @param int|string $id
     * @return mixed
     */
    public static function findTrashed($id)
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ? AND " . self::DELETED_AT . " IS NOT NULL";
        return $instance->query($sql, [$id])->fetch();
    }
}
