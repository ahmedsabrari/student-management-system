<?php

// Note: Standalone function files cannot use declare(strict_types=1);

namespace App\Helpers;

/**
 * Dump the given variable(s) and end the script.
 * عرض محتوى المتغير (المتغيرات) وإنهاء السكربت.
 * Should only be used during development.
 * يجب استخدامه فقط أثناء التطوير.
 *
 * @param mixed ...$vars One or more variables to dump.
 * @return void Never returns.
 */
function dd(mixed ...$vars): void
{
    // Use <pre> for better formatting in HTML output
    // استخدام <pre> لتنسيق أفضل في مخرجات HTML
    echo '<pre style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; margin: 10px; font-family: monospace; font-size: 0.9em; line-height: 1.6; color: #212529;">';

    // Dump each variable passed to the function
    // عرض كل متغير تم تمريره للدالة
    foreach ($vars as $var) {
        var_dump($var);
        echo "<hr style='border-top: 1px dashed #adb5bd; margin: 10px 0;'>"; // Separator between variables
    }

    echo '</pre>';

    // End script execution
    // إنهاء تنفيذ السكربت
    die();
}

/**
 * Dump the given variable(s) but allow the script to continue.
 * عرض محتوى المتغير (المتغيرات) والسماح للسكربت بالاستمرار.
 * Should only be used during development.
 * يجب استخدامه فقط أثناء التطوير.
 *
 * @param mixed ...$vars One or more variables to dump.
 * @return void
 */
function dump(mixed ...$vars): void
{
    // Use <pre> for better formatting in HTML output
    // استخدام <pre> لتنسيق أفضل في مخرجات HTML
    echo '<pre style="background-color: #e9ecef; border: 1px solid #ced4da; padding: 10px; border-radius: 4px; margin: 10px; font-family: monospace; font-size: 0.85em; line-height: 1.5; color: #495057;">';

    // Dump each variable passed to the function
    // عرض كل متغير تم تمريره للدالة
    foreach ($vars as $var) {
        var_dump($var);
         echo "<hr style='border-top: 1px dashed #adb5bd; margin: 8px 0;'>"; // Separator
    }

    echo '</pre>';

    // Do not end script execution
    // عدم إنهاء تنفيذ السكربت
}