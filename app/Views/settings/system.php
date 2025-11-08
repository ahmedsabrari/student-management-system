<?php
/**
 * System Settings View (General Tab)
 *
 * Renders the form for editing general system-wide settings.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $settings An associative array of current settings (key => value) passed from SettingsController::index().
 * @var array $errors (Assumed from Validator)
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "System Settings";

// Get current values with defaults
// الحصول على القيم الحالية مع قيم افتراضية
$siteName = $settings['site_name'] ?? 'Student Management System';
$appUrl = $settings['app_url'] ?? 'http://localhost'; // Usually from .env
$defaultLanguage = $settings['default_language'] ?? 'en';
$timezone = $settings['timezone'] ?? 'UTC'; // Usually from .env
$maintenanceMode = (bool)($settings['maintenance_mode'] ?? false);

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
                       value="<?= htmlspecialchars($siteName) ?>" 
                       aria-describedby="siteNameHelp" required>
                <div id="siteNameHelp" class="form-text">The name displayed in titles and headers.</div>
                </div>

            <div class="mb-3">
                <label for="app_url" class="form-label">Application URL</label>
                <input type="url" class="form-control" id="app_url" name="app_url" 
                       value="<?= htmlspecialchars($appUrl) ?>" 
                       aria-describedby="appUrlHelp" readonly>
                <div id="appUrlHelp" class="form-text text-warning">This setting is typically managed in the <code>.env</code> file and requires server restart. Changing it here might not take effect.</div>
                 </div>


            <div class="mb-3">
                <label for="default_language" class="form-label">Default Language</label>
                <select class="form-select" id="default_language" name="default_language">
                     <option value="en" <?= ($defaultLanguage === 'en') ? 'selected' : '' ?>>English</option>
                     <option value="fr" <?= ($defaultLanguage === 'fr') ? 'selected' : '' ?>>French</option>
                     <option value="ar" <?= ($defaultLanguage === 'ar') ? 'selected' : '' ?>>Arabic</option>
                     </select>
                <div class="form-text">The default language for the user interface.</div>
            </div>

            <div class="mb-3">
                <label for="timezone" class="form-label">Timezone</label>
                 <select class="form-select" id="timezone" name="timezone" aria-describedby="timezoneHelp">
                    <option value="UTC" <?= ($timezone === 'UTC') ? 'selected' : '' ?>>UTC</option>
                    <option value="Africa/Casablanca" <?= ($timezone === 'Africa/Casablanca') ? 'selected' : '' ?>>Africa/Casablanca</option>
                    <option value="Europe/Paris" <?= ($timezone === 'Europe/Paris') ? 'selected' : '' ?>>Europe/Paris</option>
                    <option value="America/New_York" <?= ($timezone === 'America/New_York') ? 'selected' : '' ?>>America/New York</option>
                 </select>
                 <div id="timezoneHelp" class="form-text text-warning">Changing timezone might require server restart or clearing cache. Typically managed in <code>.env</code>.</div>
            </div>

             <div class="mb-3">
                <label class="form-label d-block">Maintenance Mode</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" role="switch" id="maintenance_mode" name="maintenance_mode" value="1" <?= $maintenanceMode ? 'checked' : '' ?>>
                  <label class="form-check-label" for="maintenance_mode">Enable Maintenance Mode</label>
                </div>
                 <div class="form-text">When enabled, only administrators can access the site.</div>
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