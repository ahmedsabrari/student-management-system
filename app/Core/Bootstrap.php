<?php

namespace App\Core;

use App\Core\Env;
use App\Core\View;
use \ErrorException;

/**
 * Application Bootstrapper
 * ... ( باقي التعليقات ) ...
 */
class Bootstrap
{
    /**
     * Initializes the entire application environment.
     * ... ( باقي التعليقات ) ...
     */
    public static function init(): void // <-- init is already static, which is correct
    {
        // ... ( الكود السابق لـ loadComposerAutoloader, Env::load ) ...

        // Set global PHP settings based on environment
        $timezone = Env::get('APP_TIMEZONE', 'UTC'); 
        if (!@date_default_timezone_set($timezone)) {
            error_log("Invalid timezone specified in .env: '{$timezone}'. Falling back to UTC.");
            date_default_timezone_set('UTC');
        }

        error_reporting(E_ALL);
        ini_set('display_errors', Env::isDevelopment() ? '1' : '0');
        ini_set('log_errors', '1');
        if (defined('BASE_PATH')) {
            ini_set('error_log', BASE_PATH . '/storage/logs/app.log');
        } else {
            ini_set('error_log', dirname(__DIR__, 2) . '/storage/logs/app.log');
        }

        // Set custom handlers for errors and exceptions
        // --- MAKE SURE THESE METHODS ARE STATIC ---
        // --- تأكد من أن هذه الدوال static ---
        set_error_handler([self::class, 'handleError']);       // <--- Calls static handleError
        set_exception_handler([self::class, 'handleException']); // <--- Calls static handleException
        register_shutdown_function([self::class, 'handleShutdown']); // <--- Calls static handleShutdown
    }

    /**
     * Loads the Composer autoloader file if it exists.
     */
    private static function loadComposerAutoloader(): void // <-- Already static, good
    {
         $autoloader = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2)) . '/vendor/autoload.php';
        if (file_exists($autoloader)) {
            require_once $autoloader;
        }
    }

    /**
     * Error Handler: Converts all PHP errors into ErrorException.
     * --- ADD static ---
     * --- أضف static ---
     * @throws ErrorException
     */
    public static function handleError(int $level, string $message, string $file, int $line): void // <--- ADD static
    {
        if (!(error_reporting() & $level)) {
            return;
        }
        throw new ErrorException($message, 0, $level, $file, $line);
    }

    /**
     * Exception Handler: Catches all uncaught exceptions.
     * --- ADD static ---
     * --- أضف static ---
     */
    public static function handleException(\Throwable $exception): void // <--- ADD static
    {
        // Ensure we send a 500 Internal Server Error status code
        if (!headers_sent()) { // Check if headers already sent
            http_response_code(500);
        }

        // Log the detailed exception using the static log method
        // تسجيل الاستثناء المفصل باستخدام دالة السجل الـ static
        self::logException($exception); // <--- Use self::

        // Display a user-friendly error page
        if (Env::isDevelopment()) {
            // In development, show detailed error information
            echo '<pre style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; font-family: monospace;">';
            echo '<strong>Fatal Error:</strong> ' . htmlspecialchars($exception->getMessage()) . '<br>';
            echo '<strong>File:</strong> ' . $exception->getFile() . ' on line ' . $exception->getLine() . '<br><br>';
            echo '<strong>Stack Trace:</strong><br>' . htmlspecialchars($exception->getTraceAsString());
            echo '</pre>';
        } else {
             // @codeCoverageIgnoreStart
             // In production, show a generic error view (if View class can be instantiated)
             // في الإنتاج، اعرض عرض خطأ عام (إذا كان يمكن إنشاء كائن View)
             try {
                 // Check if the View class exists before trying to use it during fatal errors
                 // التحقق من وجود كلاس View قبل محاولة استخدامه أثناء الأخطاء الفادحة
                 if (class_exists(View::class)) {
                    echo (new View())->renderError(500); // Use the dedicated method in View
                 } else {
                     echo "<h1>500 Internal Server Error</h1><p>Something went wrong. Please try again later.</p>";
                 }
             } catch (\Throwable $viewError) {
                 // Fallback if rendering the error view itself fails
                 // بديل إذا فشل عرض صفحة الخطأ نفسها
                 error_log("Error rendering 500 page: " . $viewError->getMessage());
                 echo "<h1>500 Internal Server Error</h1><p>An unexpected error occurred.</p>";
             }
              // @codeCoverageIgnoreEnd
        }

        exit(); // Stop script execution
    }
    
    /**
     * Shutdown Handler: Catches fatal errors that are not caught by other handlers.
     * --- ADD static ---
     * --- أضف static ---
     */
    public static function handleShutdown(): void // <--- ADD static
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            // Create an exception from the fatal error and pass it to our main handler
            // إنشاء استثناء من الخطأ الفادح وتمريره إلى المعالج الرئيسي
            $exception = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
            self::handleException($exception); // <--- Use self::
        }
    }

    /**
     * Logs the details of an exception to the log file.
     * --- ADD static ---
     * --- أضف static ---
     */
    private static function logException(\Throwable $exception): void // <--- ADD static
    {
        $logFile = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2)) . '/storage/logs/app.log';
        // Ensure log directory exists (simple check)
        // التأكد من وجود مجلد السجلات (تحقق بسيط)
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
             // @codeCoverageIgnoreStart
            @mkdir($logDir, 0775, true); // Attempt to create it
             // @codeCoverageIgnoreEnd
        }
        
        $errorMessage = sprintf(
            "[%s] %s: %s in %s:%d\nStack trace:\n%s\n---\n",
            date('Y-m-d H:i:s'),
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
        // Use error_log with type 3 to append to file
        // استخدام error_log مع النوع 3 للإضافة إلى الملف
        @error_log($errorMessage, 3, $logFile);
    }
}

