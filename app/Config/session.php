<?php

use App\Core\Env;

/**
 * Session Configuration
 *
 * This file contains settings for session management. It's crucial for security
 * that these settings, especially 'secure' and 'http_only', are correctly
 * configured for your production environment.
 */
return [

    /**
     * Session Cookie Name
     * This defines the name of the cookie that will store the session ID.
     * It's good practice to use a unique name for your application.
     * Env: SESSION_NAME
     */
    'name' => Env::get('SESSION_NAME', 'student_mgmt_session'),

    /**
     * Session Lifetime
     * The number of seconds that the session should be allowed to remain idle
     * before it expires. By default, this is set to 2 hours (7200 seconds).
     * Env: SESSION_LIFETIME
     */
    'lifetime' => (int) Env::get('SESSION_LIFETIME', 7200),

    /**
     * Session Cookie Path
     * The path on the domain where the cookie will be available.
     * '/' means the cookie is available for the entire domain.
     * Env: SESSION_PATH
     */
    'path' => Env::get('SESSION_PATH', '/'),

    /**
     * Session Cookie Domain
     * The domain that the cookie is available to. To make the cookie
     * available to all subdomains, prefix the domain with a dot.
     * Env: SESSION_DOMAIN
     */
    'domain' => Env::get('SESSION_DOMAIN', null),

    /**
     * HTTPS Only Cookies
     * If set to true, the session cookie will only be sent over secure HTTPS connections.
     * This should *always* be true in production.
     * Env: SESSION_SECURE_COOKIE
     */
    'secure' => (bool) Env::get('SESSION_SECURE_COOKIE', false),

    /**
     * HTTP Only
     * If set to true, the cookie will be made accessible only through the HTTP protocol.
     * This means that the cookie won't be accessible by scripting languages,
     * such as JavaScript, which helps mitigate XSS attacks.
     * Env: SESSION_HTTP_ONLY
     */
    'http_only' => (bool) Env::get('SESSION_HTTP_ONLY', true),
    
    /**
     * SameSite Cookie Attribute
     * This attribute helps to mitigate CSRF attacks. Recommended values are 'Lax' or 'Strict'.
     * 'Lax' is a good default for most applications.
     * Env: SESSION_SAMESITE
     */
    'samesite' => Env::get('SESSION_SAMESITE', 'Lax'),

];