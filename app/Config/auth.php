<?php

use App\Core\Env;

/**
 * Authentication Configuration
 *
 * This file defines settings related to user authentication processes,
 * including login throttling (rate limiting) and password hashing mechanisms.
 * These settings are crucial for securing user accounts.
 */
return [

    /**
     * Login Identification Field
     * Specifies which database column should be used to identify a user
     * during the login process. Common options are 'email' or 'username'.
     * Env: AUTH_LOGIN_FIELD
     */
    'login_field' => Env::get('AUTH_LOGIN_FIELD', 'email'),

    /**
     * Login Throttling: Maximum Attempts
     * The maximum number of failed login attempts to allow from a single
     * user or IP address before locking them out. This helps prevent brute-force attacks.
     * Env: AUTH_MAX_ATTEMPTS
     */
    'max_attempts' => (int) Env::get('AUTH_MAX_ATTEMPTS', 5),

    /**
     * Login Throttling: Lockout Time
     * The duration in seconds for which a user or IP will be locked out after
     * exceeding the maximum number of failed login attempts.
     * Default: 300 seconds (5 minutes).
     * Env: AUTH_LOCKOUT_TIME
     */
    'lockout_time' => (int) Env::get('AUTH_LOCKOUT_TIME', 300),

    /**
     * Password Hashing Algorithm
     * Defines the algorithm used for hashing and verifying passwords.
     * PASSWORD_BCRYPT is a strong and widely supported default.
     * Other options include PASSWORD_ARGON2ID.
     * This is a PHP constant and should not be set via .env.
     */
    'password_hash_algo' => PASSWORD_BCRYPT,
    
    /**
     * "Remember Me" Cookie Lifetime
     * Defines the lifetime in seconds for the "remember me" cookie, allowing
     * users to stay logged in for an extended period.
     * Default: 2592000 seconds (30 days).
     * Env: AUTH_REMEMBER_LIFETIME
     */
    'remember_lifetime' => (int) Env::get('AUTH_REMEMBER_LIFETIME', 2592000),

];