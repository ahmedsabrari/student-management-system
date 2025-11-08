<?php

namespace App\Core;

/**
 * HTTP Request Wrapper Class
 *
 * Provides an object-oriented interface for accessing incoming
 * HTTP request data (GET, POST, SERVER, etc.) and helper methods
 * for inspecting the request.
 */
class Request
{
    /**
     * @var array All merged request data (GET + POST).
     */
    private $data = [];

    /**
     * @var string The request method (GET, POST, PUT, DELETE).
     */
    private $method;

    /**
     * @var array Server and execution environment information.
     */
    private $server;

    /**
     * @var array HTTP GET variables.
     */
    private $get;

    /**
     * @var array HTTP POST variables.
     */
    private $post;

    /**
     * Request constructor.
     *
     * Initializes the request object by capturing and parsing
     * the global $_GET, $_POST, and $_SERVER variables once.
     */
    public function __construct()
    {
        // Store raw superglobals
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;

        // Merge GET and POST data. POST data takes precedence over GET data
        // if keys are the same.
        $this->data = array_merge($this->get, $this->post);

        // Determine the request method, allowing for method spoofing
        $this->method = $this->determineMethod();
    }

    /**
     * Gets the request method.
     *
     * @return string The HTTP method (GET, POST, PUT, DELETE).
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * Determines the request method, checking for spoofing.
     * (e.g., from a form: <input type="hidden" name="_method" value="PUT">)
     *
     * @return string The HTTP method (GET, POST, PUT, DELETE).
     */
    private function determineMethod(): string
    {
        // Check for method override
        if (isset($this->post['_method'])) {
            $method = strtoupper($this->post['_method']);
            if (in_array($method, ['PUT', 'DELETE', 'PATCH'])) {
                return $method;
            }
        }

        // Fallback to the server's reported method
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Get all request input data (merged GET and POST).
     *
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Get a specific input item from the request.
     *
     * @param string $key The key of the input item.
     * @param mixed $default The default value to return if the key is not found.
     * @return mixed The input value or the default.
     */
    public function input(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Check if an input item exists in the request.
     *
     * @param string $key The key to check.
     * @return bool True if the key exists, false otherwise.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Check if the request is an AJAX (XMLHttpRequest) request.
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return !empty($this->server['HTTP_X_REQUESTED_WITH']) &&
               strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get the request URI path.
     *
     * @return string
     */
    public function getPath(): string
    {
        $path = parse_url($this->server['REQUEST_URI'], PHP_URL_PATH);
        return rawurldecode($path) ?: '/';
    }

    /**
     * Get a specific value from the $_SERVER array.
     *
     * @param string $key The key to retrieve.
     * @param mixed $default The default value.
     * @return mixed
     */
    public function server(string $key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }
}