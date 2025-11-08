<?php

namespace App\Helpers;

use App\Core\Database; // Needed for the 'unique' rule

/**
 * Data Validation Helper Class
 *
 * Provides a flexible way to validate input data against a set of rules.
 * Gathers error messages for invalid fields.
 */
class Validator
{
    /**
     * @var array Holds the data to be validated.
     */
    private array $data = [];

    /**
     * @var array Holds the validation rules.
     */
    private array $rules = [];

    /**
     * @var array Stores validation error messages.
     * Format: ['field_name' => ['Error message 1', 'Error message 2']]
     */
    private array $errors = [];

    /**
     * @var array Default error messages for validation rules.
     */
    private array $messages = [
        'required' => 'The :field field is required.',
        'email'    => 'The :field must be a valid email address.',
        'min'      => 'The :field must be at least :min characters/value.',
        'max'      => 'The :field may not be greater than :max characters/value.',
        'numeric'  => 'The :field must be a number.',
        'string'   => 'The :field must be a string.',
        'in'       => 'The selected :field is invalid.',
        'unique'   => 'The :field has already been taken.',
    ];

    /**
     * PDO database connection instance.
     * @var \PDO|null
     */
    private ?\PDO $db = null;

    /**
     * Validates the given data against the specified rules.
     * التحقق من صحة البيانات المعطاة مقابل القواعد المحددة.
     *
     * @param array $data The data to validate (e.g., $_POST).
     * @param array $rules Associative array of rules (e.g., ['email' => 'required|email']).
     * @return bool True if validation passes, false otherwise.
     */
    public function validate(array $data, array $rules): bool
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->errors = []; // Reset errors for each validation call

        foreach ($this->rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;
            $ruleSet = explode('|', $fieldRules);

            foreach ($ruleSet as $rule) {
                $params = [];
                if (strpos($rule, ':') !== false) {
                    list($rule, $paramString) = explode(':', $rule, 2);
                    $params = explode(',', $paramString);
                }

                $methodName = 'validate' . ucfirst($rule);

                if (method_exists($this, $methodName)) {
                    // Call the specific validation method
                    // استدعاء دالة التحقق المحددة
                    call_user_func([$this, $methodName], $field, $value, $params);
                } else {
                     // @codeCoverageIgnoreStart
                     // Optionally throw an exception for unknown rules
                     // يمكن إطلاق استثناء للقواعد غير المعروفة (اختياري)
                     // throw new \InvalidArgumentException("Validation rule '{$rule}' not found.");
                     // For now, we just ignore unknown rules
                     // @codeCoverageIgnoreEnd
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Get all validation error messages.
     * الحصول على جميع رسائل أخطاء التحقق.
     *
     * @return array Associative array of errors ['field' => ['message1', ...]].
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Check if the validation failed.
     * التحقق مما إذا كان التحقق قد فشل.
     *
     * @return bool True if there are validation errors, false otherwise.
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Adds an error message for a specific field and rule.
     * إضافة رسالة خطأ لحقل وقاعدة محددين.
     *
     * @param string $field The field name.
     * @param string $rule The rule name (e.g., 'required', 'min').
     * @param array $params Parameters for the error message placeholder (e.g., ['min' => 3]).
     * @return void
     */
    private function addError(string $field, string $rule, array $params = []): void
    {
        $message = $this->messages[$rule] ?? 'Invalid data for :field.';
        
        // Replace placeholders like :field, :min, :max
        // استبدال العناصر النائبة مثل :field، :min، :max
        $message = str_replace(':field', str_replace('_', ' ', $field), $message); // Make field name readable
        foreach ($params as $key => $value) {
            $message = str_replace(":$key", $value, $message);
        }

        $this->errors[$field][] = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    }

    // --- Validation Rule Methods ---
    // --- دوال قواعد التحقق ---

    private function validateRequired(string $field, $value): void
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            $this->addError($field, 'required');
        }
    }

    private function validateEmail(string $field, $value): void
    {
        // Allow empty values unless 'required' rule is also present
        // السماح بالقيم الفارغة ما لم تكن قاعدة 'required' موجودة أيضًا
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, 'email');
        }
    }

    private function validateMin(string $field, $value, array $params): void
    {
        if (empty($params[0])) return; // Parameter required
        $min = (int) $params[0];

        if (!empty($value)) {
             if (is_string($value) && mb_strlen($value) < $min) {
                 $this->addError($field, 'min', ['min' => $min]);
             } elseif (is_numeric($value) && $value < $min) {
                 $this->addError($field, 'min', ['min' => $min]);
             }
        }
    }

     private function validateMax(string $field, $value, array $params): void
    {
        if (empty($params[0])) return; // Parameter required
        $max = (int) $params[0];

        if (!empty($value)) {
            if (is_string($value) && mb_strlen($value) > $max) {
                 $this->addError($field, 'max', ['max' => $max]);
            } elseif (is_numeric($value) && $value > $max) {
                 $this->addError($field, 'max', ['max' => $max]);
            }
        }
    }

    private function validateNumeric(string $field, $value): void
    {
        if (!empty($value) && !is_numeric($value)) {
            $this->addError($field, 'numeric');
        }
    }

    private function validateString(string $field, $value): void
    {
        if (!empty($value) && !is_string($value)) {
            $this->addError($field, 'string');
        }
    }

    private function validateIn(string $field, $value, array $params): void
    {
        if (!empty($value) && !in_array($value, $params)) {
             $this->addError($field, 'in');
        }
    }

    private function validateUnique(string $field, $value, array $params): void
    {
        if (empty($params[0]) || empty($params[1])) return; // Table and column required
        if (empty($value)) return; // Don't check empty values for uniqueness

        $table = $params[0];
        $column = $params[1];
        $ignoreId = $params[2] ?? null; // Optional ID to ignore (for updates)

        if ($this->db === null) {
            $this->db = Database::getInstance()->getConnection();
        }

        $sql = "SELECT COUNT(*) as count FROM `{$table}` WHERE `{$column}` = ?";
        $bindings = [$value];

        if ($ignoreId !== null) {
            $primaryKey = $params[3] ?? 'id'; // Optional primary key name
            $sql .= " AND `{$primaryKey}` != ?";
            $bindings[] = $ignoreId;
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($bindings);
            $result = $stmt->fetch(\PDO::FETCH_OBJ);

            if ($result && $result->count > 0) {
                $this->addError($field, 'unique');
            }
        } catch (\PDOException $e) {
            // @codeCoverageIgnoreStart
            // Log the database error or handle it appropriately
            // تسجيل خطأ قاعدة البيانات أو معالجته بشكل مناسب
             $this->addError($field, 'db_error', ['message' => 'Database check failed']); // Add a generic error
            // @codeCoverageIgnoreEnd
        }
    }
}