<?php

// Note: Standalone function files cannot use declare(strict_types=1);

namespace App\Helpers;

use App\Helpers\CSRF; // Needed for automatic CSRF field

/**
 * Form Helper Functions
 *
 * Provides convenient functions for generating common HTML form elements,
 * including automatic CSRF protection and Bootstrap 5 styling.
 */


/**
 * Converts an associative array of attributes into an HTML string.
 * تحويل مصفوفة سمات إلى نص HTML.
 * Handles boolean attributes correctly.
 * يتعامل مع السمات المنطقية بشكل صحيح.
 *
 * @param array $attributes Associative array of attributes (e.g., ['class' => 'form-control', 'required' => true]).
 * @return string The formatted HTML attributes string.
 */
function attributesToString(array $attributes): string
{
    $html = '';
    foreach ($attributes as $key => $value) {
        if ($value === true) {
            // Boolean attribute (e.g., required, disabled)
            // سمة منطقية
            $html .= ' ' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
        } elseif ($value !== false && $value !== null) {
            // Regular attribute with value
            // سمة عادية بقيمة
            $html .= ' ' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '"';
        }
    }
    return $html;
}

/**
 * Opens an HTML form tag, automatically including CSRF protection and method spoofing.
 * فتح وسم <form>، مع تضمين حماية CSRF وتزييف نوع الطلب تلقائيًا.
 *
 * @param string $action The form's action URL.
 * @param string $method The form's HTTP method (GET, POST, PUT, PATCH, DELETE).
 * @param array $attributes Additional HTML attributes for the form tag (e.g., ['enctype' => 'multipart/form-data']).
 * @return string The opening <form> tag HTML.
 */
function formOpen(string $action, string $method = 'POST', array $attributes = []): string
{
    $htmlMethod = strtoupper($method);
    $spoofedMethod = null;

    // Use POST method in HTML for PUT/PATCH/DELETE and add hidden field
    // استخدام POST في HTML لـ PUT/PATCH/DELETE وإضافة حقل مخفي
    if (in_array($htmlMethod, ['PUT', 'PATCH', 'DELETE'])) {
        $spoofedMethod = $htmlMethod;
        $htmlMethod = 'POST';
    }

    $defaultAttributes = [
        'action' => htmlspecialchars($action, ENT_QUOTES, 'UTF-8'),
        'method' => $htmlMethod,
        'accept-charset' => 'UTF-8', // Good practice
    ];

    $allAttributes = array_merge($defaultAttributes, $attributes);

    $formTag = '<form' . attributesToString($allAttributes) . '>';

    // Add CSRF field automatically
    // إضافة حقل CSRF تلقائيًا
    $formTag .= "\n    " . CSRF::inputField();

    // Add method spoofing field if needed
    // إضافة حقل تزييف نوع الطلب إذا لزم الأمر
    if ($spoofedMethod) {
        $formTag .= "\n    " . '<input type="hidden" name="_method" value="' . $spoofedMethod . '">';
    }

    return $formTag;
}

/**
 * Closes an HTML form tag.
 * إغلاق وسم </form>.
 *
 * @return string The closing </form> tag.
 */
function formClose(): string
{
    return '</form>';
}

/**
 * Generates an HTML input field.
 * إنشاء حقل <input> HTML.
 * Automatically adds 'form-control' class for relevant types.
 * إضافة كلاس 'form-control' تلقائيًا للأنواع المناسبة.
 *
 * @param string $name The input's name attribute.
 * @param string $value The input's default value.
 * @param string $type The input's type attribute (text, email, password, hidden, etc.).
 * @param array $attributes Additional HTML attributes.
 * @return string The HTML input field.
 */
function formInput(string $name, string $value = '', string $type = 'text', array $attributes = []): string
{
    $defaultAttributes = [
        'type' => $type,
        'name' => $name,
        'id' => $attributes['id'] ?? $name, // Default ID to name if not provided
        'value' => $value,
    ];

    // Add Bootstrap class automatically for common input types
    // إضافة كلاس Bootstrap تلقائيًا لأنواع الإدخال الشائعة
    $controlTypes = ['text', 'email', 'password', 'number', 'date', 'time', 'url', 'tel', 'search'];
    if (in_array($type, $controlTypes) && !isset($attributes['class'])) {
        $attributes['class'] = 'form-control';
    } elseif ($type === 'checkbox' || $type === 'radio') {
         if (!isset($attributes['class'])) {
            $attributes['class'] = 'form-check-input';
         }
    } elseif ($type === 'file') {
         if (!isset($attributes['class'])) {
            $attributes['class'] = 'form-control'; // Bootstrap 5 uses form-control for files too
         }
    }


    $allAttributes = array_merge($defaultAttributes, $attributes);

    return '<input' . attributesToString($allAttributes) . '>';
}

/**
 * Generates an HTML select (dropdown) field.
 * إنشاء حقل <select> HTML (قائمة منسدلة).
 * Automatically adds 'form-select' class.
 * إضافة كلاس 'form-select' تلقائيًا.
 *
 * @param string $name The select's name attribute.
 * @param array $options An associative array of options (value => text).
 * @param string|int|null $selected The value of the pre-selected option.
 * @param array $attributes Additional HTML attributes for the select tag.
 * @return string The HTML select field.
 */
function formSelect(string $name, array $options, $selected = null, array $attributes = []): string
{
    $defaultAttributes = [
        'name' => $name,
        'id' => $attributes['id'] ?? $name,
        'class' => 'form-select', // Default Bootstrap 5 class
    ];

    $allAttributes = array_merge($attributes, $defaultAttributes); // Merge attributes, user overrides default class if provided

    $html = '<select' . attributesToString($allAttributes) . '>';

    foreach ($options as $value => $text) {
        $optionAttributes = ['value' => $value];
        // Check for selected state (handles string/int comparison loosely)
        // التحقق من الحالة المحددة (يتعامل مع مقارنة النصوص/الأرقام بشكل مرن)
        if ($selected !== null && (string)$value === (string)$selected) {
            $optionAttributes['selected'] = true;
        }
        $html .= "\n    " . '<option' . attributesToString($optionAttributes) . '>' . htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8') . '</option>';
    }

    $html .= "\n</select>";
    return $html;
}

/**
 * Generates an HTML textarea field.
 * إنشاء حقل <textarea> HTML.
 * Automatically adds 'form-control' class.
 * إضافة كلاس 'form-control' تلقائيًا.
 *
 * @param string $name The textarea's name attribute.
 * @param string $value The default content inside the textarea.
 * @param array $attributes Additional HTML attributes.
 * @return string The HTML textarea field.
 */
function formTextarea(string $name, string $value = '', array $attributes = []): string
{
    $defaultAttributes = [
        'name' => $name,
        'id' => $attributes['id'] ?? $name,
        'class' => 'form-control', // Default Bootstrap 5 class
        'rows' => '3', // Default rows
    ];

    $allAttributes = array_merge($attributes, $defaultAttributes);

    // Value goes between the tags, sanitized
    // القيمة توضع بين الوسوم، مع التنظيف
    $content = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

    return '<textarea' . attributesToString($allAttributes) . '>' . $content . '</textarea>';
}

/**
 * Generates an HTML button element.
 * إنشاء عنصر <button> HTML.
 * Automatically adds 'btn btn-primary' class and 'type="submit"' if not specified.
 * إضافة كلاس 'btn btn-primary' و 'type="submit"' تلقائيًا إذا لم يتم تحديدها.
 *
 * @param string $text The text content of the button.
 * @param array $attributes Additional HTML attributes.
 * @return string The HTML button element.
 */
function formButton(string $text, array $attributes = []): string
{
    $defaultAttributes = [
        'type' => 'submit',
        'class' => 'btn btn-primary', // Default Bootstrap 5 button
    ];

    // Merge attributes, allowing user to override defaults
    // دمج السمات، مع السماح للمستخدم بتجاوز القيم الافتراضية
    $allAttributes = array_merge($defaultAttributes, $attributes); 

    // Sanitize button text
    // تنظيف نص الزر
    $content = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

    return '<button' . attributesToString($allAttributes) . '>' . $content . '</button>';
}