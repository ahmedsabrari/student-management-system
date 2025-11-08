<?php

namespace App\Core;

/**
 * Class View
 *
 * Handles rendering views and layouts, passing data to them.
 * It uses output buffering to inject view content into a layout.
 */
class View
{
    /**
     * @var string The base directory for all view files.
     */
    protected $basePath;

    /**
     * View constructor.
     * Sets the base path for the views directory.
     */
    public function __construct()
    {
        // Sets the base path to .../student-management-system/app/Views/
        $this->basePath = dirname(__DIR__, 2) . '/app/Views/';
    }

    /**
     * Renders a view file, optionally within a layout.
     *
     * @param string $view The path to the view file (e.g., 'students/index').
     * @param array $data Data to extract into variables for the view (e.g., ['students' => $allStudents]).
     * @param string|null $layout The name of the layout file to use (e.g., 'admin'). If null, renders view only.
     * @return string The rendered HTML content.
     * @throws \Exception If the view or layout file is not found.
     */
    public function render(string $view, array $data = [], string $layout = 'main'): string
    {
        // 1. Resolve and check if the main view file exists
        $viewFile = $this->resolvePath($view);
        
        // 2. Resolve and check if the layout file exists
        $layoutFile = $this->resolvePath('layouts/' . $layout);

        // 3. Render the main view content
        // We pass $data to this method so it's available in the view
        $content = $this->renderViewFile($viewFile, $data);

        // 4. Render the layout, passing the $content and $data
        // Data is passed to the layout as well (e.g., for $title)
        return $this->renderViewFile($layoutFile, array_merge($data, ['content' => $content]));
    }

    /**
     * Renders a single view file and returns its content as a string.
     *
     * @param string $filePath The full, absolute path to the view file.
     * @param array $data Data to extract into variables.
     * @return string The rendered content.
     * @throws \Exception If the view file is not found.
     */
    protected function renderViewFile(string $filePath, array $data = []): string
    {
        if (!file_exists($filePath)) {
            throw new \Exception("View file not found: {$filePath}");
        }

        // Extract variables from the data array
        // e.g., $data = ['students' => [...]] becomes $students = [...]
        extract($data);

        // Start output buffering
        ob_start();

        // Include the view file.
        // All echoed content (HTML, PHP output) will be captured by the buffer.
        try {
            require $filePath;
        } catch (\Throwable $e) {
            // Clean buffer and re-throw if an error occurs *inside* the view file
            ob_end_clean();
            throw new \Exception("Error rendering view file {$filePath}: " . $e->getMessage(), 0, $e);
        }

        // Get the captured content from the buffer and clean the buffer
        return ob_get_clean();
    }

    /**
     * Resolves a view name (e.g., 'students/index') into a full file path.
     *
     * @param string $view The short name of the view.
     * @return string The absolute file path.
     */
    public function resolvePath(string $view): string
    {
        // Converts 'students/index' to 'students/index.php'
        // Converts 'layouts/main' to 'layouts/main.php'
        return $this->basePath . str_replace('/', DIRECTORY_SEPARATOR, $view) . '.php';
    }

    /**
     * Renders a 404 error page.
     *
     * @param array $data Optional data to pass to the error view.
     * @return string The rendered 404 page.
     */
    public function renderError(int $statusCode = 404, array $data = []): string
    {
        http_response_code($statusCode);
        
        try {
            // Try to render the specific error view (e.g., 'errors/404')
            return $this->render("errors/{$statusCode}", $data, 'main');
        } catch (\Exception $e) {
            // Fallback if the error view itself is missing
            return "<h1>{$statusCode} Error</h1><p>Page not found.</p>";
        }
    }
}