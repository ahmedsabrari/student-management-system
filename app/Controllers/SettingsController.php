<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Setting;
use App\Models\User;
use App\Helpers\Session;

/**
 * SettingsController
 *
 * Manages both global system settings (for admins) and
 * personal user profile settings (for logged-in users).
 */
class SettingsController extends Controller
{
    /** @var Setting The setting model instance. */
    protected $settingModel;

    /** @var User The user model instance. */
    protected $userModel;

    /**
     * SettingsController constructor.
     * Initializes the necessary models.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settingModel = new Setting();
        $this->userModel = new User();
    }

    // --- System Settings (Admin) ---

    /**
     * Display the system's general settings form.
     * Handles GET /settings
     *
     * @TODO: Apply AdminMiddleware to this method.
     * @return string The rendered view of the system settings.
     */
    public function index(): string
    {
        // @TODO: Create a helper method in SettingModel to fetch all settings
        // as a key-value associative array, e.g., $this->settingModel->getAllAsArray()
        
        // Simulating fetching settings for now
        $settings = [
            'site_name' => $this->settingModel->query("SELECT value FROM settings WHERE key_name = 'site_name'")->fetch()->value ?? 'Student Management System',
            'site_email' => $this->settingModel->query("SELECT value FROM settings WHERE key_name = 'site_email'")->fetch()->value ?? 'admin@example.com',
            'default_language' => $this->settingModel->query("SELECT value FROM settings WHERE key_name = 'default_language'")->fetch()->value ?? 'en',
        ];
        
        return $this->render('settings/system', ['settings' => $settings], 'admin');
    }

    /**
     * Update the system's general settings.
     * Handles POST /settings/update (renamed from 'update' to avoid conflict)
     *
     * @TODO: Apply AdminMiddleware, CSRF validation, and a Validator.
     */
    public function updateSystemSettings(): void // Renamed from update() to avoid conflict with profileUpdate()
    {
        $request = new Request();
        $settingsToUpdate = [
            'site_name' => $request->input('site_name'),
            'site_email' => $request->input('site_email'),
            'default_language' => $request->input('default_language'),
        ];

        // @TODO: This logic should be in the SettingModel, e.g., $this->settingModel->updateBatch($settingsToUpdate)
        // This is an "upsert" logic (update or insert if not exists).
        foreach ($settingsToUpdate as $key => $value) {
            $existing = $this->settingModel->query("SELECT id FROM settings WHERE key_name = ?", [$key])->fetch();
            if ($existing) {
                $this->settingModel->update($existing->id, ['value' => $value]);
            } else {
                $this->settingModel->create(['key_name' => $key, 'value' => $value]);
            }
        }

        Session::flash('success', 'System settings updated successfully.');
        $this->redirect('/settings');
    }

    // --- User Profile Settings (Logged-in User) ---

    /**
     * Display or update the current user's profile.
     * Handles GET (display) and POST (update) for /settings/profile
     *
     * @TODO: Apply AuthMiddleware (any logged-in user).
     * @return string The rendered view or redirects.
     */
    public function profile()
    {
        $request = new Request();
        $userId = Session::get('user_id');

        if (empty($userId)) {
            Session::flash('error', 'You must be logged in to view this page.');
            $this->redirect('/login');
            return;
        }

        if ($request->method() === 'POST') {
            // --- Handle POST (Update Profile) ---
            return $this->updateProfile($request, $userId);
        } else {
            // --- Handle GET (Show Profile Form) ---
            return $this->showProfile($userId);
        }
    }

    /**
     * Show the user profile form (GET request).
     *
     * @param int $userId
     * @return string
     */
    private function showProfile(int $userId): string
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->view->renderError(404, ['message' => 'User profile not found.']);
        }
        
        return $this->render('settings/profile', ['user' => $user], 'admin'); // or 'main' layout
    }

    /**
     * Update the user profile (POST request).
     *
     * @param Request $request
     * @param int $userId
     */
    private function updateProfile(Request $request, int $userId): void
    {
        // @TODO: Implement CSRF validation.
        // @TODO: Implement Validator (e.g., email must be unique, full_name is required).
        
        $data = [
            'full_name' => $request->input('full_name'),
            'email' => $request->input('email'),
            'username' => $request->input('username'),
        ];

        if ($this->userModel->update($userId, $data)) {
            Session::flash('success', 'Profile updated successfully.');
        } else {
            Session::flash('error', 'Failed to update profile or no changes were made.');
        }
        $this->redirect('/settings/profile');
    }

    /**
     * Process the user's password change request.
     * Handles POST /settings/change-password
     *
     * @TODO: Apply AuthMiddleware and CSRF validation.
     */
    public function changePassword(): void
    {
        $request = new Request();
        $userId = Session::get('user_id');

        if (empty($userId)) {
            $this->redirect('/login');
            return;
        }

        // @TODO: Implement a robust Validator class
        $old_password = $request->input('old_password');
        $new_password = $request->input('new_password');
        $confirm_password = $request->input('confirm_password');

        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
            Session::flash('error', 'All password fields are required.');
            $this->redirect('/settings/profile');
            return;
        }

        if ($new_password !== $confirm_password) {
            Session::flash('error', 'New passwords do not match.');
            $this->redirect('/settings/profile');
            return;
        }

        $user = $this->userModel->find($userId);

        // Verify old password
        if (!password_verify($old_password, $user->password)) {
            Session::flash('error', 'Your old password does not match our records.');
            $this->redirect('/settings/profile');
            return;
        }
        
        // Hash and update new password
        $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);
        if ($this->userModel->update($userId, ['password' => $hashedPassword])) {
            Session::flash('success', 'Password changed successfully.');
        } else {
            Session::flash('error', 'Failed to change password. Please try again.');
        }
        $this->redirect('/settings/profile');
    }
}