<?php
/**
 * 500 Internal Server Error View
 *
 * Displays a user-friendly page when an unexpected server error occurs.
 * Crucially, it does *not* display technical error details to the end-user.
 * This view is typically rendered within the 'main.php' or a simpler error layout.
 */

// Set the title for the layout
// (This $title variable will be used by the layout file)
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "500 - Server Error";

// Optional: You could pass a generic message, but avoid specifics
// $message = $message ?? 'Something went wrong on our end. Please try again later.';

?>

<div class="container text-center py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <h1 class="display-1 fw-bold text-danger">500</h1>
            
            <h2 class="mb-3">Internal Server Error</h2>
            
            <p class="lead text-muted mb-4">
                We apologize, but something went wrong on our server. 
                Our team has been notified. Please try refreshing the page or come back later.
            </p>
            
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="/" class="btn btn-primary btn-lg px-4 gap-3">
                    <i class="fas fa-home me-1"></i> Go to Homepage
                </a>
                <button onclick="location.reload();" class="btn btn-outline-secondary btn-lg px-4">
                    <i class="fas fa-sync-alt me-1"></i> Refresh Page
                </button>
            </div>

        </div>
    </div>
</div>