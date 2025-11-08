<?php

declare(strict_types=1); // Enforce strict types

namespace App\Traits;

use DateTime;
use Exception;
// Assuming date helper functions exist in App\Helpers namespace
use function App\Helpers\formatDateTime; 

/**
 * Timestamps Trait
 *
 * Automatically manages `created_at` and `updated_at` timestamp columns
 * for models that use this trait. Assumes the columns exist in the database table.
 * * Usage:
 * class YourModel extends \App\Core\Model {
 * use Timestamps;
 * // ... other model properties ...
 * }
 * * The base Model class should be modified to call `setTimestampsOnSave` 
 * before create/update operations if the model uses this trait.
 */
trait Timestamps
{
    /**
     * Indicates if the model should be timestamped.
     * يشير إلى ما إذا كان يجب إضافة طوابع زمنية للنموذج.
     * Models using this trait should generally set this to true.
     * النماذج التي تستخدم هذا الـ Trait يجب أن تعينه إلى true عادةً.
     * * @var bool
     */
    public bool $timestamps = true;

    /**
     * The name of the "created at" column.
     * اسم عمود "تاريخ الإنشاء".
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     * اسم عمود "تاريخ آخر تحديث".
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Sets the appropriate timestamps before a save operation (create or update).
     * This method is intended to be called by the base Model's create/update methods.
     * تعيين الطوابع الزمنية المناسبة قبل عملية الحفظ (إنشاء أو تحديث).
     * هذه الدالة مخصصة ليتم استدعاؤها بواسطة دوال create/update في الـ Model الأساسي.
     *
     * @param array &$data The data array being saved, passed by reference.
     * @param bool $isCreating True if creating a new record, false if updating.
     * @return void
     */
    protected function setTimestampsOnSave(array &$data, bool $isCreating = false): void
    {
        $now = $this->freshTimestampString();

        // If creating, set created_at if it's not already set
        // إذا كان يتم الإنشاء، قم بتعيين created_at إذا لم يكن معينًا بالفعل
        if ($isCreating && !isset($data[self::CREATED_AT])) {
            $data[self::CREATED_AT] = $now;
        }

        // Always set updated_at if it's not already set (or if creating)
        // قم دائمًا بتعيين updated_at إذا لم يكن معينًا بالفعل (أو عند الإنشاء)
        if (!isset($data[self::UPDATED_AT])) {
             $data[self::UPDATED_AT] = $now;
        }
    }
    
    /**
     * Get a fresh timestamp for the model.
     * الحصول على طابع زمني جديد للنموذج.
     *
     * @return string Formatted date-time string.
     */
    protected function freshTimestampString(): string
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Get the creation date formatted.
     * الحصول على تاريخ الإنشاء منسقًا.
     *
     * @param string $format The desired date format.
     * @return string The formatted date string or empty string.
     */
    public function getCreatedAt(string $format = 'd-m-Y H:i'): string
    {
        $createdAtColumn = self::CREATED_AT;
        // Access the property dynamically using object syntax
        // الوصول إلى الخاصية ديناميكيًا باستخدام صيغة الكائن
        return formatDateTime($this->$createdAtColumn ?? null, $format);
    }

    /**
     * Get the last updated date formatted.
     * الحصول على تاريخ آخر تحديث منسقًا.
     *
     * @param string $format The desired date format.
     * @return string The formatted date string or empty string.
     */
    public function getUpdatedAt(string $format = 'd-m-Y H:i'): string
    {
        $updatedAtColumn = self::UPDATED_AT;
        return formatDateTime($this->$updatedAtColumn ?? null, $format);
    }
}