<?php
/**
 * System Settings View
 *
 * Renders the form for editing general system settings.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $settings An associative array of current settings (key => value) passed from SettingsController::index().
 * @var array $errors (Assumed from Validator)
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "System Settings";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>System Settings</h2>
    </div>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>General Configuration</h5>
    </div>
    <div class="card-body">
        <form action="/settings/update" method="POST" id="systemSettingsForm">
            
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">

            <div class="mb-3">
                <label for="site_name" class="form-label">Application Name</label>
                <input type="text" class="form-control" id="site_name" name="site_name" 
                       value="<?= htmlspecialchars($settings['site_name'] ?? 'Student Management System') ?>" 
                       aria-describedby="siteNameHelp" required>
                <div id="siteNameHelp" class="form-text">The name displayed in titles and headers.</div>
                </div>

            <div class="mb-3">
                <label for="site_email" class="form-label">Default Site Email</label>
                <input type="email" class="form-control" id="site_email" name="site_email" 
                       value="<?= htmlspecialchars($settings['site_email'] ?? 'admin@example.com') ?>" 
                       aria-describedby="siteEmailHelp">
                <div id="siteEmailHelp" class="form-text">The default email address for system notifications.</div>
                </div>

            <div class="mb-3">
                <label for="default_language" class="form-label">Default Language</label>
                <select class="form-select" id="default_language" name="default_language">
                     <option value="en" <?= (($settings['default_language'] ?? 'en') === 'en') ? 'selected' : '' ?>>English</option>
                     <option value="fr" <?= (($settings['default_language'] ?? 'en') === 'fr') ? 'selected' : '' ?>>French</option>
                     <option value="ar" <?= (($settings['default_language'] ?? 'en') === 'ar') ? 'selected' : '' ?>>Arabic</option>
                     </select>
                <div class="form-text">The default language for the user interface.</div>
            </div>
            
            <hr>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>