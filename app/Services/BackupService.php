<?php

declare(strict_types=1); // Enforce strict types

namespace App\Services;

use App\Core\Env; // Needed if config relies on Env directly
use Exception;    // For catching exceptions
use FilesystemIterator; // For listing files

/**
 * Backup Service
 *
 * Handles creation and restoration of MySQL database backups using
 * command-line utilities (mysqldump, mysql).
 * Requires appropriate server permissions and command-line tools access.
 */
class BackupService
{
    /**
     * @var array Database configuration.
     */
    protected array $dbConfig = [];

    /**
     * @var string Path to the database backup directory.
     */
    protected string $backupDir;

    /**
     * @var string|null Stores the last error message.
     */
    protected ?string $lastError = null;

    /**
     * BackupService constructor.
     * Loads database configuration and sets the backup directory path.
     *
     * @throws Exception If database configuration is incomplete or backup directory is invalid.
     */
    public function __construct()
    {
        // Load database configuration (adapt based on how config is loaded)
        // تحميل إعدادات قاعدة البيانات (عدّل بناءً على كيفية تحميل الإعدادات)
        $dbConfigPath = BASE_PATH . '/app/Config/database.php';
        if (!file_exists($dbConfigPath)) {
            // @codeCoverageIgnoreStart
            throw new Exception("Database configuration file not found: {$dbConfigPath}");
            // @codeCoverageIgnoreEnd
        }
        $config = require $dbConfigPath;
        $this->dbConfig = [
            'host' => $config['host'] ?? '127.0.0.1',
            'port' => $config['port'] ?? 3306,
            'name' => $config['dbname'] ?? null,
            'user' => $config['username'] ?? null,
            'pass' => $config['password'] ?? '',
        ];

        // Validate essential config
        // التحقق من الإعدادات الأساسية
        if (empty($this->dbConfig['name']) || empty($this->dbConfig['user'])) {
            // @codeCoverageIgnoreStart
            throw new Exception("Database name and username must be configured for backups.");
            // @codeCoverageIgnoreEnd
        }

        // Define and ensure backup directory exists
        // تحديد والتأكد من وجود مجلد النسخ الاحتياطي
        $this->backupDir = BASE_PATH . '/storage/backups/database/';
        if (!is_dir($this->backupDir)) {
            if (!mkdir($this->backupDir, 0775, true)) {
                // @codeCoverageIgnoreStart
                throw new Exception("Failed to create backup directory: {$this->backupDir}. Check permissions.");
                // @codeCoverageIgnoreEnd
            }
        }
        if (!is_writable($this->backupDir)) {
             // @codeCoverageIgnoreStart
             throw new Exception("Backup directory is not writable: {$this->backupDir}. Check permissions.");
             // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Creates a backup of the database using mysqldump.
     * إنشاء نسخة احتياطية من قاعدة البيانات باستخدام mysqldump.
     *
     * @return string|false The full path to the created backup file on success, false on failure. Check getError().
     */
    public function backupDatabase(): string|false
    {
        $this->lastError = null;
        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = $this->backupDir . "backup-{$timestamp}.sql";
        
        // Escape shell arguments for security
        // تأمين معاملات سطر الأوامر للأمان
        $host = escapeshellarg($this->dbConfig['host']);
        $port = escapeshellarg((string)$this->dbConfig['port']);
        $user = escapeshellarg($this->dbConfig['user']);
        $pass = $this->dbConfig['pass']; // Password handled separately
        $dbName = escapeshellarg($this->dbConfig['name']);
        $backupFilePath = escapeshellarg($backupFile);
        
        // Construct the mysqldump command
        // بناء أمر mysqldump
        // Note: Adding --single-transaction is good for InnoDB tables to avoid locking.
        // ملاحظة: إضافة --single-transaction جيدة لجداول InnoDB لتجنب القفل.
        $command = "mysqldump --host={$host} --port={$port} --user={$user}";
        if (!empty($pass)) {
             $command .= " --password=" . escapeshellarg($pass); // Add password directly (less secure than alternatives like .my.cnf)
        }
        $command .= " --single-transaction --databases {$dbName} > {$backupFilePath} 2>&1"; // Redirect stderr to stdout

        // Execute the command
        // تنفيذ الأمر
        exec($command, $output, $returnVar);

        // Check for errors
        // التحقق من الأخطاء
        if ($returnVar !== 0) {
            $this->lastError = "mysqldump failed (Exit Code: {$returnVar}): " . implode("\n", $output);
             // @codeCoverageIgnoreStart
             error_log($this->lastError);
             // Attempt to delete the potentially incomplete/empty file
             // محاولة حذف الملف غير المكتمل/الفارغ المحتمل
             if (file_exists($backupFile)) { @unlink($backupFile); }
             return false;
             // @codeCoverageIgnoreEnd
        }
        
        // Check if the file was actually created and is not empty
        // التحقق مما إذا كان الملف قد تم إنشاؤه بالفعل وليس فارغًا
         if (!file_exists($backupFile) || filesize($backupFile) === 0) {
              // @codeCoverageIgnoreStart
              $this->lastError = "Backup file was not created or is empty, although mysqldump reported success.";
              error_log($this->lastError);
              if (file_exists($backupFile)) { @unlink($backupFile); }
              return false;
              // @codeCoverageIgnoreEnd
         }

        return $backupFile; // Return full path on success
    }

    /**
     * Restores the database from a specified SQL backup file using mysql client.
     * استعادة قاعدة البيانات من ملف نسخ احتياطي SQL محدد باستخدام عميل mysql.
     * WARNING: This will overwrite the current database content.
     * تحذير: سيؤدي هذا إلى الكتابة فوق محتوى قاعدة البيانات الحالي.
     *
     * @param string $filePath The full path to the .sql backup file.
     * @return bool True on success, false on failure. Check getError().
     */
    public function restoreDatabase(string $filePath): bool
    {
        $this->lastError = null;

        // Validate file path
        // التحقق من مسار الملف
        if (!file_exists($filePath) || !is_readable($filePath) || strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) !== 'sql') {
            $this->lastError = "Invalid backup file path or file is not readable/not a .sql file: {$filePath}";
            return false;
        }

        // Escape shell arguments
        // تأمين معاملات سطر الأوامر
        $host = escapeshellarg($this->dbConfig['host']);
        $port = escapeshellarg((string)$this->dbConfig['port']);
        $user = escapeshellarg($this->dbConfig['user']);
        $pass = $this->dbConfig['pass'];
        $dbName = escapeshellarg($this->dbConfig['name']);
        $backupFilePath = escapeshellarg($filePath);

        // Construct the mysql command
        // بناء أمر mysql
        $command = "mysql --host={$host} --port={$port} --user={$user}";
         if (!empty($pass)) {
             $command .= " --password=" . escapeshellarg($pass);
        }
        $command .= " {$dbName} < {$backupFilePath} 2>&1"; // Pipe the file content and redirect stderr

        // Execute the command
        // تنفيذ الأمر
        exec($command, $output, $returnVar);

        // Check for errors
        // التحقق من الأخطاء
        if ($returnVar !== 0) {
            $this->lastError = "mysql restore failed (Exit Code: {$returnVar}): " . implode("\n", $output);
             // @codeCoverageIgnoreStart
             error_log($this->lastError);
             return false;
             // @codeCoverageIgnoreEnd
        }

        return true; // Success
    }
    
    /**
     * Lists available backup files in the backup directory.
     * عرض ملفات النسخ الاحتياطي المتاحة في مجلد النسخ الاحتياطي.
     *
     * @return array An array of filenames sorted by modification time (newest first).
     */
    public function listBackups(): array
    {
        $files = [];
        $iterator = new FilesystemIterator($this->backupDir);
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile() && strtolower($fileinfo->getExtension()) === 'sql') {
                 // Store filename and modification time
                 // تخزين اسم الملف ووقت التعديل
                 $files[$fileinfo->getMTime()] = $fileinfo->getFilename();
            }
        }
        // Sort by modification time, newest first
        // الترتيب حسب وقت التعديل، الأحدث أولاً
        krsort($files); 
        return array_values($files);
    }
    
     /**
     * Deletes a specific backup file.
     * حذف ملف نسخ احتياطي محدد.
     *
     * @param string $fileName The name of the file (not the full path) within the backup directory.
     * @return bool True on success, false on failure.
     */
    public function deleteBackup(string $fileName): bool
    {
         $this->lastError = null;
         // Basic sanitization to prevent path traversal
         // تنظيف أساسي لمنع اجتياز المسار
         $fileName = basename($fileName); 
         $filePath = $this->backupDir . $fileName;

         if (file_exists($filePath) && is_file($filePath)) {
              if (unlink($filePath)) {
                  return true;
              } else {
                   // @codeCoverageIgnoreStart
                   $this->lastError = "Failed to delete backup file: {$filePath}. Check permissions.";
                   error_log($this->lastError);
                   return false;
                   // @codeCoverageIgnoreEnd
              }
         } else {
             $this->lastError = "Backup file not found: {$fileName}";
             return false; // File not found
         }
    }


    /**
     * Get the last error message.
     * الحصول على آخر رسالة خطأ.
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->lastError;
    }
}
