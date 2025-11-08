<?php
/**
 * 404 Not Found Error View
 *
 * Displays a user-friendly page when a requested resource is not found.
 * This view is typically rendered within the 'main.php' or a simpler error layout.
 */

// Set the title for the layout
// (This $title variable will be used by the layout file)
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "404 - Page Not Found";

// Optional: You could pass a message from the controller/router if needed
// $message = $message ?? 'The page you are looking for could not be found.';

?>

<div class="container text-center py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <h1 class="display-1 fw-bold text-primary">404</h1>
            
            <h2 class="mb-3">Oops! Page Not Found</h2>
            
            <p class="lead text-muted mb-4">
                We're sorry, but the page you requested could not be found. 
                It might have been removed, had its name changed, or is temporarily unavailable.
            </p>
            
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="/" class="btn btn-primary btn-lg px-4 gap-3">
                    <i class="fas fa-home me-1"></i> Go to Homepage
                </a>
                <button onclick="window.history.back();" class="btn btn-outline-secondary btn-lg px-4">
                    <i class="fas fa-arrow-left me-1"></i> Go Back
                </button>
            </div>

        </div>
    </div>
</div>