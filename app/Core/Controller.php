<?php

namespace App\Core;

// Import the View class to be used within the controller.
use App\Core\View;

/**
 * Base Controller Class
 *
 * This is the main controller that all other controllers in the application
 * will extend. It provides common functionalities like rendering views,
 * handling redirects, and sending JSON responses, which prevents code
 * duplication in child controllers.
 */
abstract class Controller
{
    /**
     * @var View The view instance used for rendering.
     */
    protected $view;

    /**
     * Controller constructor.
     *
     * Automatically creates a new View instance when a controller is instantiated,
     * making the view object available to all child controllers via `$this->view`.
     */
    public function __construct()
    {
        $this->view = new View();
    }

    /**
     * Renders a view file.
     * A convenient wrapper around the View class's render method.
     *
     * @param string $view The path to the view file (e.g., 'students/index').
     * @param array $data Data to be extracted into variables for the view.
     * @param string $layout The layout file to be used.
     * @return string The rendered HTML content.
     */
    protected function render(string $view, array $data = [], string $layout = 'main'): string
    {
        return $this->view->render($view, $data, $layout);
    }

    /**
     * Redirects the user to a specified URL.
     *
     * @param string $url The URL to redirect to. It can be a relative or absolute path.
     */
    protected function redirect(string $url): void
    {
        // Use the application's base URL if the path is relative
        if (strpos($url, 'http') !== 0) {
            $baseUrl = Env::get('APP_URL', 'http://localhost');
            $url = rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
        }
        
        header('Location: ' . $url);
        exit(); // Important: stop script execution after a redirect
    }

    /**
     * Sends a JSON response.
     * Useful for API endpoints. Sets the appropriate headers and outputs the data as JSON.
     *
     * @param mixed $data The data to be encoded as JSON.
     * @param int $statusCode The HTTP status code to send (e.g., 200 for OK, 404 for Not Found).
     */
    protected function json($data, int $statusCode = 200): void
    {
        // Set the HTTP response code
        http_response_code($statusCode);
        
        // Set the content type header to application/json
        header('Content-Type: application/json; charset=utf-8');
        
        // Output the JSON encoded data
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        // Stop script execution
        exit();
    }
}