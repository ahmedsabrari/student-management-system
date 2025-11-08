<?php

use App\Core\Env;

/**
 * Mailer Configuration
 *
 * This file defines the settings for sending emails from the application.
 * It's configured to read from .env variables, allowing for different
 * setups in development and production environments.
 */
return [

    /**
     * Mail Driver
     * Defines the default mailer to use (e.g., 'smtp', 'log', 'sendmail').
     * The 'log' driver is useful for development as it writes emails to the log file
     * instead of actually sending them.
     * Env: MAIL_DRIVER
     */
    'driver' => Env::get('MAIL_DRIVER', 'smtp'),

    /**
     * SMTP Host Address
     * The hostname of your SMTP server.
     * Env: MAIL_HOST
     */
    'host' => Env::get('MAIL_HOST', 'smtp.mailtrap.io'),

    /**
     * SMTP Host Port
     * The port for the SMTP server. (e.g., 587 for TLS, 465 for SSL)
     * Env: MAIL_PORT
     */
    'port' => Env::get('MAIL_PORT', 2525),

    /**
     * SMTP Server Username
     * The username to authenticate with the SMTP server.
     * Env: MAIL_USERNAME
     */
    'username' => Env::get('MAIL_USERNAME', ''),

    /**
     * SMTP Server Password
     * The password to authenticate with the SMTP server.
     * Env: MAIL_PASSWORD
     */
    'password' => Env::get('MAIL_PASSWORD', ''),

    /**
     * Encryption Protocol
     * The encryption type to use ('tls', 'ssl', or null).
     * Env: MAIL_ENCRYPTION
     */
    'encryption' => Env::get('MAIL_ENCRYPTION', 'tls'),

    /**
     * Global "From" Address
     * The email address that all outgoing emails will be sent from by default.
     * Env: MAIL_FROM_ADDRESS
     */
    'from_address' => Env::get('MAIL_FROM_ADDRESS', 'noreply@example.com'),

    /**
     * Global "From" Name
     * The name that all outgoing emails will appear to be sent from.
     * Env: MAIL_FROM_NAME
     */
    'from_name' => Env::get('MAIL_FROM_NAME', 'Student Management System'),

];