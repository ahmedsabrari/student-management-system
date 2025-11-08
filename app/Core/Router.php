<?php

namespace App\Core;

/**
 * Class Router
 *
 * A simple yet powerful regex-based router that dispatches requests
 * to controller methods or closures.
 */
class Router
{
    /**
     * @var array Stores all registered routes, indexed by HTTP method.
     */
    protected $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];

    /**
     * @var string Base path of the application (e.g., if in a subdirectory).
     */
    protected $basePath = '';

    /**
     * Router constructor.
     *
     * @param string $basePath The base path for the application (optional).
     */
    public function __construct(string $basePath = '')
    {
        // Normalize the base path
        $this->basePath = '/' . trim($basePath, '/');
    }

    /**
     * Add a route to the routing table.
     *
     * @param string $method The HTTP request method (GET, POST, etc.).
     * @param string $path The URI path to match.
     * @param mixed $callback The controller/method array or Closure to execute.
     */
    protected function addRoute(string $method, string $path, $callback): void
    {
        $method = strtoupper($method);
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }
        
        // Normalize path
        $path = $this->basePath === '/' ? $path : $this->basePath . $path;
        $path = '/' . trim($path, '/');
        
        $this->routes[$method][$path] = $callback;
    }

    /**
     * Register a GET route.
     *
     * @param string $path The URI path.
     * @param mixed $callback The callback to execute.
     */
    public function get(string $path, $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    /**
     * Register a POST route.
     *
     * @param string $path The URI path.
     * @param mixed $callback The callback to execute.
     */
    public function post(string $path, $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * Register a PUT route.
     *
     * @param string $path The URI path.
     * @param mixed $callback The callback to execute.
     */
    public function put(string $path, $callback): void
    {
        $this->addRoute('PUT', $path, $callback);
    }

    /**
     * Register a DELETE route.
     *
     * @param string $path The URI path.
     * @param mixed $callback The callback to execute.
     */
    public function delete(string $path, $callback): void
    {
        $this->addRoute('DELETE', $path, $callback);
    }

    /**
     * Get the current request URI path.
     *
     * @return string
     */
    protected function getRequestPath(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = rawurldecode($path);

        // Remove base path from request path if it exists
        if (strlen($this->basePath) > 1 && strpos($path, $this->basePath) === 0) {
            $path = substr($path, strlen($this->basePath));
        }

        // Normalize: ensure leading slash, remove trailing slash
        $path = '/' . trim($path, '/');
        
        return $path;
    }

    /**
     * Get the current request method.
     *
     * @return string
     */
    protected function getRequestMethod(): string
    {
        // Check for method override (for forms that only support GET/POST)
        if (isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
            if (in_array($method, ['PUT', 'DELETE'])) {
                return $method;
            }
        }

        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Dispatch the request to the appropriate route.
     *
     * This is the main method that finds a matching route and executes its callback.
     *
     * @return mixed The result of the callback execution.
     */
    public function dispatch()
    {
        $requestPath = $this->getRequestPath();
        $requestMethod = $this->getRequestMethod();

        $routes = $this->routes[$requestMethod] ?? [];

        foreach ($routes as $routePath => $callback) {
            // Convert route path to a regex
            // e.g., /students/{id} -> #^/students/([^/]+)$#
            $regexPath = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $routePath);
            $regex = '#^' . $regexPath . '$#';

            $matches = [];
            if (preg_match($regex, $requestPath, $matches)) {
                // Remove the full match (index 0)
                array_shift($matches);
                $params = $matches;

                return $this->executeCallback($callback, $params);
            }
        }

        // No route matched
        return $this->handleNotFound();
    }

    /**
     * Execute the callback for a matched route.
     *
     * @param mixed $callback The callback (array, Closure, or function name).
     * @param array $params The parameters extracted from the URI.
     * @return mixed
     * @throws \Exception If the controller or method is not valid.
     */
    protected function executeCallback($callback, array $params = [])
    {
        // Case 1: [Controller::class, 'method']
        if (is_array($callback) && count($callback) === 2 && is_string($callback[0])) {
            $className = $callback[0];
            $method = $callback[1];

            if (!class_exists($className)) {
                throw new \Exception("Controller class '{$className}' not found.");
            }

            $controller = new $className(); // Create instance

            if (!method_exists($controller, $method)) {
                throw new \Exception("Method '{$method}' not found in controller '{$className}'.");
            }

            // Call the method, passing parameters
            return call_user_func_array([$controller, $method], $params);
        }

        // Case 2: Closure (anonymous function)
        if ($callback instanceof \Closure) {
            return call_user_func_array($callback, $params);
        }

        // Case 3: Simple function name (less common in MVC)
        if (is_string($callback) && function_exists($callback)) {
            return call_user_func_array($callback, $params);
        }

        throw new \Exception('Invalid callback provided for route.');
    }

    /**
     * Handle the 404 Not Found error.
     *
     * In a real application, this would render a 404 view.
     */
    protected function handleNotFound()
    {
        http_response_code(404);
        // We will improve this later to render a view from: app/Views/errors/404.php
        echo "<h1>404 Not Found</h1>";
        echo "<p>The page you are looking for does not exist.</p>";
    }
}