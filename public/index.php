<?php

/**
 * Student Management System - Front Controller
 *
 * This file is the single entry point for all incoming web requests.
 * It bootstraps the application environment, loads dependencies,
 * initializes the core App class, and runs the request lifecycle.
 */

// Define BASE_PATH constant for easy access to project root
// تعريف ثابت BASE_PATH للوصول السهل إلى جذر المشروع
define('BASE_PATH', dirname(__DIR__));

// 1. Register Composer Autoloader
// ١. تسجيل محمل Composer التلقائي
// Assumes Composer is used and installed in the project root.
// يفترض استخدام Composer وتثبيته في جذر المشروع.
$autoloader = BASE_PATH . '/vendor/autoload.php';
if (!file_exists($autoloader)) {
    // @codeCoverageIgnoreStart
    // Display a user-friendly error if Composer dependencies are missing.
    // عرض خطأ سهل للمستخدم إذا كانت تبعيات Composer مفقودة.
    echo "<h1>Composer Autoloader Not Found</h1>";
    echo "<p>Please run <code>composer install</code> in your project root directory.</p>";
    echo "<p>Expected path: <code>" . htmlspecialchars($autoloader) . "</code></p>";
    exit(1); // Exit with an error code
    // @codeCoverageIgnoreEnd
}
require_once $autoloader;

// 2. Load Core Application Helper Functions (if any are procedural)
// ٢. تحميل دوال المساعدة الأساسية للتطبيق (إذا كانت إجرائية)
// Ensure necessary helper function files are loaded early
// تأكد من تحميل ملفات دوال المساعدة الضرورية مبكرًا
// Example: require_once BASE_PATH . '/app/Helpers/debug.php'; 
// (Load other helpers like auth.php, flash.php etc. if needed globally before App runs)
// Note: If helpers are only used within classes, autoloading handles them.
// ملاحظة: إذا تم استخدام المساعدين داخل الكلاسات فقط، فإن التحميل التلقائي يتعامل معها.


// 3. Bootstrap the Application
// ٣. تهيئة التطبيق
// This initializes environment variables (.env), sets error handling, timezone, etc.
// يقوم بتهيئة متغيرات البيئة (.env)، وتعيين معالجة الأخطاء، المنطقة الزمنية، إلخ.
try {
    App\Core\Bootstrap::init();
} catch (\Exception $e) {
    // Catch critical bootstrap errors (e.g., .env not found, directory not writable)
    // التقاط أخطاء التهيئة الحرجة (مثل عدم العثور على .env، مجلد غير قابل للكتابة)
     // @codeCoverageIgnoreStart
     // In development, show details. In production, show a generic message.
     // في وضع التطوير، اعرض التفاصيل. في الإنتاج، اعرض رسالة عامة.
     // Basic check:
     $isDev = (getenv('APP_ENV') ?: 'production') === 'development'; // Check env before Env class might be fully ready
     
     echo "<h1>Application Boot Error</h1>";
     echo "<p>An error occurred during application initialization.</p>";
     if ($isDev) {
         echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
         echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
     } else {
          echo "<p>Please contact the site administrator.</p>";
     }
     exit(1);
     // @codeCoverageIgnoreEnd
}


// 4. Instantiate and Run the Application
// ٤. إنشاء وتشغيل التطبيق
// The App class handles routing, middleware, controller dispatching, and response generation.
// يتعامل كلاس App مع التوجيه، Middleware، إرسال الـ Controller، وتوليد الاستجابة.
$app = new App\Core\App();
$app->run();

// No code should execute after $app->run() as it handles the response and exit().
// لا يجب تنفيذ أي كود بعد $app->run() لأنه يتعامل مع الاستجابة و exit().
