<?php

use App\Core\Env;

/**
 * General Application Configuration
 *
 * This file contains general application settings. These values are read
 * by the application and can be overridden by environment variables (.env).
 */
return [

    /**
     * Application Name
     * This name is used in page titles and other UI elements.
     * Env: APP_NAME
     */
    'site_name' => Env::get('APP_NAME', 'Student Management System'),

    /**
     * Application Environment
     * Determines the level of error reporting and debugging tools.
     * Accepted Values: 'development', 'production', 'testing'
     * Env: APP_ENV
     */
    'env' => Env::get('APP_ENV', 'production'),

    /**
     * Application Debug Mode
     * When enabled, detailed error messages will be shown.
     * Should *never* be true in a production environment.
     * Env: APP_DEBUG
     */
    'debug' => (bool) Env::get('APP_DEBUG', false),

    /**
     * Application URL
     * The base URL of your application.
     * Env: APP_URL
     */
    'url' => Env::get('APP_URL', 'http://localhost'),
    
    /**
     * Application Timezone
     * Sets the default timezone for date and time functions.
     * Env: APP_TIMEZONE
     */
    'timezone' => Env::get('APP_TIMEZONE', 'Africa/Casablanca'),

    /**
     * Default Application Language
     * Sets the default language for the application (e.g., for translations).
     * Env: APP_LANG
     */
    'default_language' => Env::get('APP_LANG', 'en'),
    
];