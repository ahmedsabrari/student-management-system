<?php

namespace App\Core;

use App\Core\Router;
use App\Core\View;
use App\Core\Env;

/**
 * Main Application Class (Kernel)
 *
 * This class acts as the central point of the application. It initializes
 * the router, loads all defined routes, and dispatches the incoming request
 * to the appropriate controller action. It also handles top-level exceptions.
 */
class App
{
    /**
     * @var Router The router instance.
     */
    protected $router;

    /**
     * @var View The view instance for error handling.
     */
    protected $view;

    /**
     * App constructor.
     *
     * Initializes the environment, router, view components, and loads the routes.
     */
    public function __construct()
    {
        // 1. Load environment variables from .env file
        Env::load();

        // 2. Initialize core components
        $this->router = new Router();
        $this->view = new View();

        // 3. Load the application routes
        $this->loadRoutes();
    }

    /**
     * Loads the routes from the configuration file.
     * The routes file is expected to have access to the $router instance.
     *
     * @throws \Exception if the routes file is not found.
     */
    protected function loadRoutes(): void
    {
        // Make the router instance available to the included routes file
        $router = $this->router;
        
        $routesPath = dirname(__DIR__) . '/Config/routes.php';

        if (!file_exists($routesPath)) {
            throw new \Exception("Routes file not found at: {$routesPath}");
        }

        // The routes.php file will now be able to call methods on the $router object
        require_once $routesPath;
    }

    /**
     * Runs the application.
     *
     * This method is the main entry point. It dispatches the router and
     * handles any uncaught exceptions, displaying a user-friendly error page.
     */
    public function run(): void
    {
        try {
            // Dispatch the router and echo the response from the controller
            // The router's dispatch method will return the HTML content or a 404 page.
            $response = $this->router->dispatch();
            echo $response;

        } catch (\Throwable $e) {
            // This is the global exception handler for any error not caught elsewhere.
            // (e.g., Database connection error, Controller class not found, etc.)
            
            // For developers: Log the detailed error
            $this->logError($e);

            // For users: Display a generic 500 Internal Server Error page.
            // In development, we might want to show more details.
            if (Env::isDevelopment()) {
                // For a better developer experience, you could use a dedicated error page
                // that displays the exception details. For now, we keep it simple.
                // Example: echo $this->view->renderError(500, ['exception' => $e]);
                error_log("FATAL ERROR: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile());
                echo $this->view->renderError(500);

            } else {
                // In production, always show a generic error page.
                echo $this->view->renderError(500);
            }
        }
    }

    /**
     * Logs exceptions to the application log file.
     *
     * @param \Throwable $exception The exception to log.
     */
    private function logError(\Throwable $exception): void
    {
        $logPath = dirname(__DIR__, 2) . '/storage/logs/app.log';
        $logDir = dirname($logPath);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }

        $errorMessage = sprintf(
            "[%s] Uncaught Exception: \"%s\" in %s:%d\nStack trace:\n%s\n---\n",
            date('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        error_log($errorMessage, 3, $logPath);
    }
}