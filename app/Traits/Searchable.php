<?php

declare(strict_types=1); // Enforce strict types

namespace App\Traits;

use App\Core\Model; // Assuming Model class is in App\Core

/**
 * Searchable Trait
 *
 * Provides a simple, unified search method for models.
 * Models using this trait should define a protected `$searchable` array
 * listing the columns that should be included in the search.
 *
 * Usage:
 * class YourModel extends \App\Core\Model {
 * use Searchable;
 * protected array $searchable = ['name', 'email', 'description'];
 * // ... other model properties ...
 * }
 *
 * // Then call statically:
 * $results = YourModel::search('search term');
 */
trait Searchable
{
    /**
     * Define the columns that are searchable in the model.
     * تحديد الأعمدة القابلة للبحث في النموذج.
     * This MUST be overridden in the model using the trait.
     * يجب تجاوز هذه الخاصية في النموذج الذي يستخدم الـ Trait.
     * Example: protected array $searchable = ['first_name', 'last_name', 'email'];
     * @var array
     */
    protected array $searchable = [];

    /**
     * Perform a search across the model's searchable columns.
     * تنفيذ بحث عبر الأعمدة القابلة للبحث في النموذج.
     *
     * Uses a simple LIKE %term% search.
     * يستخدم بحث LIKE %term% بسيط.
     *
     * @param string $term The search term.
     * @return array An array of model objects/arrays matching the search term. Returns empty array if no searchable columns defined.
     */
    public static function search(string $term): array
    {
        // Instantiate the model to access its properties (table, searchable) and methods (query)
        // إنشاء نسخة من النموذج للوصول إلى خصائصه (الجدول، الأعمدة القابلة للبحث) ودواله (query)
        $instance = new static();

        // Ensure the instance is a Model and has the 'searchable' property
        // التأكد من أن النسخة هي Model ولديها خاصية 'searchable'
        if (!$instance instanceof Model || empty($instance->searchable) || !property_exists($instance, 'searchable')) {
            // Log warning or simply return empty if no searchable fields defined
            // تسجيل تحذير أو إرجاع مصفوفة فارغة إذا لم يتم تحديد حقول قابلة للبحث
            // error_log('Search called on model ' . static::class . ' with no $searchable fields defined.');
            return [];
        }

        // Prepare the LIKE term
        // تحضير مصطلح البحث لـ LIKE
        $likeTerm = "%{$term}%";

        // Build the WHERE clause dynamically
        // بناء جملة WHERE ديناميكيًا
        $whereClauses = [];
        $bindings = [];
        foreach ($instance->searchable as $column) {
            // Basic sanitization for column name (prevent injection if somehow manipulated)
            // تنظيف أساسي لاسم العمود (لمنع الحقن إذا تم التلاعب به بطريقة ما)
            $safeColumn = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
            if (!empty($safeColumn)) {
                $whereClauses[] = "`{$safeColumn}` LIKE ?";
                $bindings[] = $likeTerm;
            }
        }

        if (empty($whereClauses)) {
            // No valid searchable columns found after sanitization
            // لم يتم العثور على أعمدة قابلة للبحث صالحة بعد التنظيف
            return [];
        }

        // Construct the full SQL query
        // بناء استعلام SQL الكامل
        $sql = "SELECT * FROM {$instance->table} WHERE (" . implode(' OR ', $whereClauses) . ")";
        
        // Add condition to exclude soft-deleted records if the trait is used
        // إضافة شرط لاستبعاد السجلات المحذوفة منطقيًا إذا تم استخدام Trait الحذف المنطقي
        if (property_exists($instance, 'usesSoftDeletes') && $instance->usesSoftDeletes === true && defined(get_class($instance).'::DELETED_AT')) {
             $deletedAtColumn = $instance::DELETED_AT;
             $sql .= " AND {$deletedAtColumn} IS NULL";
        }

        try {
            // Execute the query using the instance's query method
            // تنفيذ الاستعلام باستخدام دالة query الخاصة بالنسخة
            return $instance->query($sql, $bindings)->fetchAll();
             // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            // Log the error
            // تسجيل الخطأ
            // error_log("Search query failed for model " . static::class . ": " . $e->getMessage());
            return []; // Return empty array on database error
        }
         // @codeCoverageIgnoreEnd
    }
}