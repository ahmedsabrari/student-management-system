<?php

namespace App\Core;

/**
 * HTTP Response Class
 *
 * Encapsulates the logic for sending HTTP responses, including headers,
 * status codes, JSON data, and redirects. This promotes a cleaner
 * separation of concerns within controllers.
 */
class Response
{
    /**
     * Sets the HTTP response status code.
     *
     * @param int $code The HTTP status code (e.g., 200, 404, 500).
     * @return self Returns the instance for method chaining.
     */
    public function status(int $code): self
    {
        http_response_code($code);
        return $this;
    }

    /**
     * Sets a raw HTTP header.
     *
     * @param string $header The full header string (e.g., 'Content-Type: application/json').
     * @return self Returns the instance for method chaining.
     */
    public function setHeader(string $header): self
    {
        header($header);
        return $this;
    }

    /**
     * Sends a JSON response and terminates the script.
     *
     * @param mixed $data The data (e.g., array, object) to be encoded as JSON.
     * @param int $status The HTTP status code to send with the response.
     */
    public function json($data, int $status = 200): void
    {
        // Set status and content type, then echo the JSON encoded data
        $this->status($status)
             ->setHeader('Content-Type: application/json; charset=utf-8');
        
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        // Terminate execution as the response is complete.
        exit();
    }

    /**
     * Redirects the user to a new URL and terminates the script.
     *
     * @param string $url The URL to redirect to.
     * @param int $status The HTTP redirect status code (302 for temporary, 301 for permanent).
     */
    public function redirect(string $url, int $status = 302): void
    {
        // Set the redirect header and status code
        $this->status($status)
             ->setHeader('Location: ' . $url);
        
        // Terminate execution.
        exit();
    }

    /**
     * Sends content to the browser.
     *
     * @param string $content The content to send (e.g., HTML).
     * @param int $status The HTTP status code.
     */
    public function send(string $content, int $status = 200): void
    {
        $this->status($status);
        echo $content;
    }
}