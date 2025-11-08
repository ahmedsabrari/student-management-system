<?php

declare(strict_types=1); // Enforce strict types

namespace App\Services;

use App\Core\Env; // Needed if config relies on Env directly
use Exception;    // For throwing exceptions

/**
 * File Service
 *
 * Handles file uploads, validation (size, type), secure storage with unique names,
 * and file deletion within the application's configured upload directories.
 */
class FileService
{
    /**
     * @var array Upload configuration loaded from config/uploads.php
     */
    protected array $config = [];

    /**
     * @var string|null Stores the last error message.
     */
    protected ?string $lastError = null;

    /**
     * FileService constructor.
     * Loads the upload configuration file.
     *
     * @throws Exception If the configuration file is not found or readable.
     */
    public function __construct()
    {
        $configPath = BASE_PATH . '/app/Config/uploads.php'; // Use BASE_PATH constant
        if (!file_exists($configPath) || !is_readable($configPath)) {
             // @codeCoverageIgnoreStart
            throw new Exception("Upload configuration file not found or not readable: {$configPath}");
             // @codeCoverageIgnoreEnd
        }
        $this->config = require $configPath;

        // Ensure base upload path ends with a directory separator
        // التأكد من أن مسار الرفع الأساسي ينتهي بفاصل مجلدات
        $this->config['uploads_path'] = rtrim($this->config['uploads_path'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * Handles the upload of a single file.
     * معالجة رفع ملف واحد.
     *
     * Validates the file based on configuration, generates a unique name,
     * and moves it to the specified target directory.
     * يتحقق من صحة الملف بناءً على الإعدادات، ينشئ اسمًا فريدًا، وينقله إلى المجلد الهدف.
     *
     * @param array $file The file array entry from $_FILES (e.g., $_FILES['avatar']).
     * @param string $directoryKey The key corresponding to the target directory in config (e.g., 'avatars', 'documents').
     * @param string $allowedTypesKey The key corresponding to the allowed types array in config (e.g., 'allowed_image_types').
     * @return string|false The relative path (from web root '/uploads/...') of the saved file on success, false on failure. Check getError() for details.
     */
    public function uploadFile(array $file, string $directoryKey, string $allowedTypesKey): string|false
    {
        $this->lastError = null; // Reset error

        // 1. Check Upload Errors
        // ١. التحقق من أخطاء الرفع
        if (!$this->validateUploadError($file)) {
            return false;
        }

        // 2. Check File Size
        // ٢. التحقق من حجم الملف
        if (!$this->validateSize($file)) {
            return false;
        }

        // 3. Check File Type (Extension)
        // ٣. التحقق من نوع الملف (الامتداد)
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!$this->validateType($fileExtension, $allowedTypesKey)) {
            return false;
        }

        // 4. Determine Target Path
        // ٤. تحديد المسار الهدف
        $targetDirectoryPath = $this->config[$directoryKey] ?? null;
        if ($targetDirectoryPath === null) {
            $this->lastError = "Invalid target directory key specified: {$directoryKey}.";
             // @codeCoverageIgnoreStart
             error_log($this->lastError); // Log for developer
             // @codeCoverageIgnoreEnd
            return false;
        }
        // Ensure path ends with separator
        // التأكد من أن المسار ينتهي بفاصل
        $targetDirectoryPath = rtrim($targetDirectoryPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;


        // 5. Generate Unique Filename
        // ٥. إنشاء اسم ملف فريد
        $uniqueName = $this->generateUniqueFilename($fileExtension);
        $fullDestinationPath = $targetDirectoryPath . $uniqueName;

        // 6. Ensure Directory Exists
        // ٦. التأكد من وجود المجلد
        if (!is_dir($targetDirectoryPath)) {
            if (!mkdir($targetDirectoryPath, 0775, true)) {
                 // @codeCoverageIgnoreStart
                $this->lastError = "Failed to create upload directory: {$targetDirectoryPath}. Check permissions.";
                error_log($this->lastError);
                return false;
                 // @codeCoverageIgnoreEnd
            }
        }

        // 7. Move Uploaded File
        // ٧. نقل الملف المرفوع
        if (move_uploaded_file($file['tmp_name'], $fullDestinationPath)) {
            // Success: Return the relative web path
            // نجاح: إرجاع المسار النسبي للويب
            // Example: /uploads/avatars/timestamp_random.jpg
            $relativePath = str_replace(BASE_PATH . '/public', '', $fullDestinationPath);
            return str_replace(DIRECTORY_SEPARATOR, '/', $relativePath); // Ensure forward slashes for web
        } else {
             // @codeCoverageIgnoreStart
            $this->lastError = "Failed to move uploaded file. Check destination path permissions: {$targetDirectoryPath}.";
             error_log($this->lastError . " | Tmp Name: " . $file['tmp_name'] . " | Destination: " . $fullDestinationPath);
             return false;
             // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Deletes a file specified by its relative web path.
     * حذف ملف محدد بواسطة مساره النسبي على الويب.
     *
     * @param string|null $relativePath The path relative to the public directory (e.g., '/uploads/avatars/file.jpg').
     * @return bool True on successful deletion or if file doesn't exist, false on failure.
     */
    public function deleteFile(?string $relativePath): bool
    {
        $this->lastError = null;
        if (empty($relativePath)) {
            return true; // Nothing to delete
        }

        // Construct full server path from relative web path
        // بناء المسار الكامل على الخادم من المسار النسبي للويب
        $fullPath = BASE_PATH . '/public' . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        if (file_exists($fullPath) && is_file($fullPath)) {
            if (unlink($fullPath)) {
                return true; // Successfully deleted
            } else {
                 // @codeCoverageIgnoreStart
                $this->lastError = "Failed to delete file: {$fullPath}. Check permissions.";
                error_log($this->lastError);
                return false; // Deletion failed
                 // @codeCoverageIgnoreEnd
            }
        }

        // File does not exist, consider it success in terms of the desired state
        // الملف غير موجود، اعتبرها ناجحة من حيث الحالة المطلوبة
        return true; 
    }

    /**
     * Get the last error message that occurred during upload or delete.
     * الحصول على آخر رسالة خطأ حدثت أثناء الرفع أو الحذف.
     *
     * @return string|null The last error message, or null if no error occurred.
     */
    public function getError(): ?string
    {
        return $this->lastError;
    }

    // --- Private Validation and Helper Methods ---

    /**
     * Check for PHP upload errors.
     * التحقق من أخطاء الرفع الخاصة بـ PHP.
     */
    private function validateUploadError(array $file): bool
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            $this->lastError = 'Invalid file upload parameters.';
             // @codeCoverageIgnoreStart
             error_log($this->lastError . " - File array: " . print_r($file, true));
             // @codeCoverageIgnoreEnd
            return false;
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                return true; // No error
            case UPLOAD_ERR_NO_FILE:
                $this->lastError = 'No file sent.';
                return false;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->lastError = 'Exceeded filesize limit.';
                return false;
             // @codeCoverageIgnoreStart
            default:
                $this->lastError = 'Unknown errors.';
                 error_log("Unknown file upload error: Code " . $file['error']);
                 return false;
             // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Validate file size against configuration.
     * التحقق من حجم الملف مقابل الإعدادات.
     */
    private function validateSize(array $file): bool
    {
        $maxSize = $this->config['max_file_size'] ?? 0;
        if ($maxSize > 0 && $file['size'] > $maxSize) {
            $this->lastError = 'Exceeded maximum filesize limit (' . round($maxSize / 1024 / 1024, 1) . ' MB).';
            return false;
        }
        if ($file['size'] <= 0) {
             $this->lastError = 'File is empty.';
             return false;
        }
        return true;
    }

    /**
     * Validate file extension against allowed types in configuration.
     * التحقق من امتداد الملف مقابل الأنواع المسموح بها في الإعدادات.
     */
    private function validateType(string $extension, string $allowedTypesKey): bool
    {
        $allowedTypes = $this->config[$allowedTypesKey] ?? null;
        if ($allowedTypes === null) {
            $this->lastError = "Invalid allowed types key specified: {$allowedTypesKey}.";
             // @codeCoverageIgnoreStart
             error_log($this->lastError);
             // @codeCoverageIgnoreEnd
            return false;
        }
        if (!is_array($allowedTypes)) {
             // @codeCoverageIgnoreStart
             $this->lastError = "Configuration error: {$allowedTypesKey} must be an array.";
             error_log($this->lastError);
             return false;
             // @codeCoverageIgnoreEnd
        }

        if (empty($extension) || !in_array($extension, $allowedTypes, true)) {
            $this->lastError = 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes) . '.';
            return false;
        }
        return true;
    }

    /**
     * Generate a unique filename to prevent collisions and potential security issues.
     * إنشاء اسم ملف فريد لمنع التصادمات والمشاكل الأمنية المحتملة.
     */
    private function generateUniqueFilename(string $extension): string
    {
        // Example: timestamp_randomString.extension
        // مثال: طابع_زمني_سلسلة_عشوائية.امتداد
        return time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    }
}
