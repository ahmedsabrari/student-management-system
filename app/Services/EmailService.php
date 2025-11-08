<?php

declare(strict_types=1); // Enforce strict types

namespace App\Services;

// --- PHPMailer Dependency ---
// --- الاعتماد على PHPMailer ---
// Ensure PHPMailer is installed (e.g., via Composer: composer require phpmailer/phpmailer)
// and its autoloader is included in your bootstrap process.
// تأكد من تثبيت PHPMailer وتضمين أداة التحميل التلقائي الخاصة بها.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\SMTP;
// --- End PHPMailer Dependency ---

use App\Core\Env; // Needed if config relies on Env directly
use Exception;    // For catching general exceptions

/**
 * Email Service
 *
 * Handles sending emails using SMTP configuration.
 * Relies on the PHPMailer library. Loads configuration from config/mail.php.
 */
class EmailService
{
    /**
     * @var array Mail configuration loaded from config/mail.php
     */
    protected array $config = [];

    /**
     * @var PHPMailer PHPMailer instance.
     */
    protected PHPMailer $mailer;

    /**
     * @var string|null Stores the last error message from PHPMailer.
     */
    protected ?string $lastError = null;

    /**
     * EmailService constructor.
     * Loads mail configuration and initializes PHPMailer.
     *
     * @throws Exception If the configuration file is not found or PHPMailer class is missing.
     */
    public function __construct()
    {
        // Check if PHPMailer class exists (basic dependency check)
        // التحقق من وجود كلاس PHPMailer (تحقق أساسي من الاعتمادية)
        if (!class_exists(PHPMailer::class)) {
             // @codeCoverageIgnoreStart
            throw new Exception('PHPMailer library is not loaded. Please install it (e.g., composer require phpmailer/phpmailer) and ensure autoloading.');
             // @codeCoverageIgnoreEnd
        }

        // Load mail configuration
        // تحميل إعدادات البريد
        $configPath = BASE_PATH . '/app/Config/mail.php'; // Use BASE_PATH constant
        if (!file_exists($configPath) || !is_readable($configPath)) {
             // @codeCoverageIgnoreStart
            throw new Exception("Mail configuration file not found or not readable: {$configPath}");
             // @codeCoverageIgnoreEnd
        }
        $this->config = require $configPath;

        // Initialize PHPMailer
        // تهيئة PHPMailer
        $this->mailer = new PHPMailer(true); // Enable exceptions
    }

    /**
     * Sends an email.
     * إرسال بريد إلكتروني.
     *
     * Configures PHPMailer with settings from config/mail.php and sends the email.
     * يقوم بتكوين PHPMailer بالإعدادات من config/mail.php وإرسال البريد.
     *
     * @param string|array $to Email address(es) of the recipient(s). Can be a single string or an array of strings.
     * @param string $subject The subject of the email.
     * @param string $htmlBody The HTML content of the email.
     * @param string|null $altBody Optional plain text alternative body for non-HTML clients.
     * @param array $cc Optional array of CC recipients.
     * @param array $bcc Optional array of BCC recipients.
     * @return bool True on success, false on failure. Check getError() for details.
     */
    public function send(
        string|array $to,
        string $subject,
        string $htmlBody,
        ?string $altBody = null,
        array $cc = [],
        array $bcc = []
    ): bool {
        $this->lastError = null; // Reset error

        try {
            // --- Server Settings ---
            // --- إعدادات الخادم ---
            // Enable verbose debug output (comment out in production)
            // تفعيل مخرجات التصحيح المفصلة (ضعها في تعليق في الإنتاج)
            // $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER; 
            $this->mailer->isSMTP();                               // Send using SMTP
            $this->mailer->Host       = $this->config['host'] ?? 'localhost'; // Set the SMTP server to send through
            $this->mailer->SMTPAuth   = !empty($this->config['username']);    // Enable SMTP authentication if username is set
            $this->mailer->Username   = $this->config['username'] ?? '';      // SMTP username
            $this->mailer->Password   = $this->config['password'] ?? '';      // SMTP password
            $this->mailer->SMTPSecure = $this->config['encryption'] ?? PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $this->mailer->Port       = (int)($this->config['port'] ?? 587); // TCP port to connect to

            // --- Recipients ---
            // --- المستلمون ---
            $this->mailer->setFrom($this->config['from_address'] ?? 'noreply@example.com', $this->config['from_name'] ?? 'App');
            
            // Add To recipients
            // إضافة مستلمي "To"
            if (is_array($to)) {
                foreach ($to as $recipient) {
                    $this->mailer->addAddress($recipient);
                }
            } else {
                $this->mailer->addAddress($to);
            }

            // Add CC recipients
            // إضافة مستلمي "CC"
            foreach ($cc as $recipient) {
                $this->mailer->addCC($recipient);
            }
             // Add BCC recipients
             // إضافة مستلمي "BCC"
            foreach ($bcc as $recipient) {
                $this->mailer->addBCC($recipient);
            }

            // --- Content ---
            // --- المحتوى ---
            $this->mailer->isHTML(true);                                // Set email format to HTML
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $htmlBody;
            $this->mailer->AltBody = $altBody ?? strip_tags($htmlBody); // Generate plain text from HTML if not provided

            // Send the email
            // إرسال البريد
            $this->mailer->send();
            return true;

        } catch (PHPMailerException $e) {
            $this->lastError = "PHPMailer Error: {$this->mailer->ErrorInfo}";
             // @codeCoverageIgnoreStart
             error_log($this->lastError); // Log the detailed error
             // @codeCoverageIgnoreEnd
            return false;
        } catch (Exception $e) {
             // @codeCoverageIgnoreStart
            // Catch other potential exceptions during setup
            // التقاط استثناءات أخرى محتملة أثناء الإعداد
            $this->lastError = "Email Service Error: " . $e->getMessage();
            error_log($this->lastError);
            return false;
             // @codeCoverageIgnoreEnd
        } finally {
             // Reset PHPMailer recipients and attachments for the next send if instance is reused
             // إعادة تعيين مستلمي ومرفقات PHPMailer للإرسال التالي إذا تم إعادة استخدام النسخة
             $this->mailer->clearAddresses();
             $this->mailer->clearCCs();
             $this->mailer->clearBCCs();
             $this->mailer->clearAttachments(); // If you add attachment logic later
        }
    }

    /**
     * Get the last error message from PHPMailer.
     * الحصول على آخر رسالة خطأ من PHPMailer.
     *
     * @return string|null The last error message, or null if no error occurred.
     */
    public function getError(): ?string
    {
        return $this->lastError;
    }
}