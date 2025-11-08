<?php

declare(strict_types=1); // Enforce strict types

namespace App\Services;

use App\Models\User;
use App\Models\Role; // Needed to assign default role on registration
use App\Helpers\Session;
// Note: We don't need Response here typically, Controller handles redirection.
// Hash helper is not needed as we use built-in password functions.

/**
 * Authentication Service
 *
 * Handles the business logic for user authentication, including login,
 * registration, and logout processes. Interacts with the User model
 * and manages session state.
 */
class AuthService
{
    /**
     * @var User The User model instance.
     */
    protected User $userModel;

    /**
     * AuthService constructor.
     * Initializes the User model.
     */
    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Attempt to log in a user with given credentials.
     * محاولة تسجيل دخول مستخدم بالبيانات المعطاة.
     *
     * @param string $emailOrUsername The user's email or username.
     * @param string $password The user's plain-text password.
     * @return User|false The authenticated User object on success, false on failure.
     */
    public function attemptLogin(string $emailOrUsername, string $password): User|false
    {
        // Find the user by email or username
        // البحث عن المستخدم بواسطة البريد الإلكتروني أو اسم المستخدم
        // @TODO: Use login_field from config/auth.php to decide which field to use
        // استخدام login_field من config/auth.php لتحديد الحقل المستخدم
        $user = $this->userModel->findByEmail($emailOrUsername);
        if (!$user) {
            // Optionally check by username if email lookup failed
             $user = $this->userModel->findByUsername($emailOrUsername);
             if (!$user) {
                return false; // User not found
             }
        }

        // Verify the password
        // التحقق من كلمة المرور
        if (password_verify($password, $user->password)) {
            // Password is correct, login successful

            // Regenerate session ID for security
            // إعادة إنشاء معرف الجلسة للأمان
            Session::regenerate();

            // Store essential user data in the session
            // تخزين بيانات المستخدم الأساسية في الجلسة
            Session::set('user_id', $user->id);
            Session::set('user_name', $user->full_name);
            
            // Get and store the role name
            // الحصول على اسم الدور وتخزينه
            $role = $user->role(); 
            Session::set('user_role', $role ? $role->name : null); // Store role name
            
            // @TODO: Store teacher_id or student_id if applicable based on role
            // تخزين teacher_id أو student_id إذا كان منطبقًا بناءً على الدور

            return $user; // Return the user object
        }

        // Password incorrect
        // كلمة المرور غير صحيحة
        return false;
    }

    /**
     * Register a new user.
     * تسجيل مستخدم جديد.
     *
     * @param array $data User data (e.g., ['full_name' => ..., 'email' => ..., 'password' => ...]).
     * @return User|false The newly created User object on success, false on failure (e.g., email exists).
     */
    public function registerUser(array $data): User|false
    {
        // @TODO: Add more robust validation here or rely on Validator in Controller.
        
        // Check if email or username already exists
        // التحقق مما إذا كان البريد الإلكتروني أو اسم المستخدم موجودًا بالفعل
        if ($this->userModel->findByEmail($data['email'])) {
            // error_log('Registration failed: Email already exists.');
            return false;
        }
        if (isset($data['username']) && $this->userModel->findByUsername($data['username'])) {
             // error_log('Registration failed: Username already exists.');
            return false;
        }

        // Hash the password
        // تجزئة كلمة المرور
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT); // Or use algo from config
        } else {
             // @codeCoverageIgnoreStart
             // error_log('Registration failed: Password not provided.');
             return false; // Password is required
             // @codeCoverageIgnoreEnd
        }

        // Assign a default role (e.g., 'student')
        // تعيين دور افتراضي (مثل 'student')
        // @TODO: Make the default role configurable
        // جعل الدور الافتراضي قابلاً للتكوين
        $defaultRole = (new Role())->findByName('student'); 
        $data['role_id'] = $defaultRole ? $defaultRole->id : null; 
        
        // Ensure only allowed fields are passed to create
        // التأكد من تمرير الحقول المسموح بها فقط إلى create
        $allowedFields = ['full_name', 'email', 'username', 'password', 'role_id', /* add others if needed */];
        $userData = array_intersect_key($data, array_flip($allowedFields));

        try {
            $newUserId = $this->userModel->create($userData);
            if ($newUserId) {
                return $this->userModel->find($newUserId);
            }
             // @codeCoverageIgnoreStart
             return false;
             // @codeCoverageIgnoreEnd
             // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
             // error_log('User creation failed: ' . $e->getMessage());
             return false; // Database error during creation
        }
         // @codeCoverageIgnoreEnd
    }

    /**
     * Log the user out by destroying the session.
     * تسجيل خروج المستخدم عن طريق تدمير الجلسة.
     *
     * @return void
     */
    public function logout(): void
    {
        Session::destroy();
    }

    // @TODO: Add methods for password reset (requestToken, verifyToken, resetPassword) if needed.
    // إضافة دوال لإعادة تعيين كلمة المرور إذا لزم الأمر.
}
