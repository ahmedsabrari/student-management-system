<?php

use App\Core\Env;

/**
 * Database Configuration
 *
 * This file defines the connection parameters for the database.
 * It reads values from the .env file, providing sensible defaults
 * for a local development environment.
 */
return [

    /**
     * Default Database Connection Driver
     * E.g., 'mysql', 'pgsql', 'sqlite'
     * Env: DB_CONNECTION
     */
    'driver' => Env::get('DB_CONNECTION', 'mysql'),

    /**
     * Database Host
     * The IP address or hostname of your database server.
     * Env: DB_HOST
     */
    'host' => Env::get('DB_HOST', '127.0.0.1'),

    /**
     * Database Port
     * The port on which your database server is listening.
     * Env: DB_PORT
     */
    'port' => Env::get('DB_PORT', 3306),

    /**
     * Database Name
     * The name of the database you want to connect to.
     * Env: DB_DATABASE
     */
    'dbname' => Env::get('DB_DATABASE', 'student_management_system'),

    /**
     * Database Username
     * The username for the database connection.
     * Env: DB_USERNAME
     */
    'username' => Env::get('DB_USERNAME', 'root'),

    /**
     * Database Password
     * The password for the database connection.
     * Env: DB_PASSWORD
     */
    'password' => Env::get('DB_PASSWORD', ''),

    /**
     * Default Character Set
     * 'utf8mb4' is recommended as it supports a wide range of characters, including emojis.
     * Env: DB_CHARSET
     */
    'charset' => Env::get('DB_CHARSET', 'utf8mb4'),

    /**
     * PDO Fetch Style
     * Defines the default fetch mode for database queries.
     * Options: \PDO::FETCH_OBJ, \PDO::FETCH_ASSOC, etc.
     */
    'fetch_mode' => \PDO::FETCH_OBJ,

];