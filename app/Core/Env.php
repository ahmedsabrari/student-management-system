<?php

namespace App\Core;

/**
 * Environment Variable Loader
 *
 * This class is responsible for loading, parsing, and providing access 
 * to environment variables from a .env file at the project root.
 * It uses a static (singleton) pattern to ensure variables are loaded only once.
 */
class Env
{
    /**
     * @var array Holds all loaded environment variables.
     */
    private static $variables = [];

    /**
     * @var bool Flag to ensure .env is loaded only once.
     */
    private static $loaded = false;

    /**
     * @var string The path to the .env file.
     */
    private static $envPath;

    /**
     * Load the .env file.
     *
     * This method reads the .env file line by line, parses it, and
     * populates the static $variables array, $_ENV, and $_SERVER.
     *
     * @throws \Exception If the .env file is not found or not readable.
     */
    public static function load(): void
    {
        if (self::$loaded) {
            return;
        }

        self::$envPath = dirname(__DIR__, 2) . '/.env';

        if (!file_exists(self::$envPath) || !is_readable(self::$envPath)) {
            throw new \Exception('.env file not found or is not readable. Path: ' . self::$envPath);
        }

        $lines = file(self::$envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments or empty lines
            if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
                continue;
            }

            // Split into key and value
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = self::parseValue(trim($value));

            // Store the variable
            self::$variables[$key] = $value;
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value; // Also useful for web server context
            putenv("$key=$value");
        }

        self::$loaded = true;
    }

    /**
     * Parse a raw string value from .env into its correct PHP type.
     *
     * @param string $value The raw value.
     * @return mixed The parsed value (bool, int, float, null, string).
     */
    private static function parseValue(string $value)
    {
        // Handle quoted strings (remove quotes)
        if (preg_match('/^"(.*)"$/', $value, $matches) || preg_match("/^'(.*)'$/", $value, $matches)) {
            return $matches[1];
        }

        // Handle boolean and null values
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
        }

        // Handle numeric values
        if (is_numeric($value)) {
            if (strpos($value, '.') !== false) {
                return (float) $value;
            }
            return (int) $value;
        }

        // Return as plain string
        return $value;
    }

    /**
     * Get an environment variable.
     *
     * Will automatically load the .env file on the first call.
     *
     * @param string $key The variable key.
     * @param mixed $default The default value to return if the key is not found.
     * @return mixed The value of the environment variable or the default.
     */
    public static function get(string $key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        // Check our loaded variables first
        if (array_key_exists($key, self::$variables)) {
            return self::$variables[$key];
        }

        // Fallback check for system-level env variables (less common)
        $value = getenv($key);
        if ($value !== false) {
            return self::parseValue($value);
        }

        return $default;
    }

    /**
     * Check if an environment variable exists.
     *
     * @param string $key The variable key.
     * @return bool True if the key exists, false otherwise.
     */
    public static function has(string $key): bool
    {
        if (!self::$loaded) {
            self::load();
        }

        return array_key_exists($key, self::$variables) || getenv($key) !== false;
    }

    /**
     * Get the current application environment (e.g., 'development', 'production').
     *
     * @return string The application environment.
     */
    public static function getEnvironment(): string
    {
        return self::get('APP_ENV', 'production');
    }

    /**
     * Check if the application is in 'development' mode.
     *
     * @return bool
     */
    public static function isDevelopment(): bool
    {
        return self::getEnvironment() === 'development';
    }

    /**
     * Check if the application is in 'production' mode.
     *
     * @return bool
     */
    public static function isProduction(): bool
    {
        return self::getEnvironment() === 'production';
    }

    /**
* Get all loaded environment variables.
     *
     * @return array
     */
    public static function all(): array
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$variables;
    }
}