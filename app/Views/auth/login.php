<?php
/*
 * File: app/Views/auth/login.php
 *
 * This is the login form content, injected into the layouts/auth.php.
 * (هذا هو محتوى نموذج تسجيل الدخول، يتم حقنه داخل layouts/auth.php)
 *
 * Expected variables:
 * @var array $errors    (Optional) Validation errors (e.g., ['email_or_username' => 'Field is required'])
 * @var array $old_input (Optional) Old form input to repopulate fields (e.g., ['email_or_username' => 'test'])
 */

// Set title for the layout (will be picked up by layouts/auth.php)
// (تحديد العنوان للقالب)
$title = "Login to your Account";

// Get old input and errors, with safe defaults
// (جلب المدخلات القديمة والأخطاء، مع قيم افتراضية آمنة)
$errors = $errors ?? [];
$old_input = $old_input ?? [];
?>

<!-- 
  START: Login Form
  (بداية نموذج تسجيل الدخول)
-->
<form action="/login" method="POST" id="loginForm" class="needs-validation" novalidate>

    <!-- CSRF Token (Security) -->
    <!-- (رمز الحماية من هجمات CSRF) -->
    <?= \App\Helpers\CSRF::inputField() // Assumes CSRF helper exists ?>

    <!-- Email or Username Field -->
    <!-- (حقل البريد الإلكتروني أو اسم المستخدم) -->
    <div class="mb-3">
        <label for="email_or_username" class="form-label">Email or Username</label>
        <input 
            type="text" 
            class="form-control <?= isset($errors['email_or_username']) ? 'is-invalid' : '' ?>" 
            id="email_or_username" 
            name="email_or_username"
            value="<?= htmlspecialchars($old_input['email_or_username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            required
            aria-describedby="error-email"
        >
        <!-- Validation Error -->
        <?php if (!empty($errors['email_or_username'])): ?>
            <div id="error-email" class="invalid-feedback d-block">
                <?= htmlspecialchars($errors['email_or_username'][0], ENT_QUOTES, 'UTF-8') // Display first error ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Password Field -->
    <!-- (حقل كلمة المرور) -->
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group has-validation">
            <input 
                type="password" 
                class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                id="password" 
                name="password" 
                required
                aria-describedby="error-password"
            >
            <!-- Show/Hide Password Toggle -->
            <!-- (زر إظهار/إخفاء كلمة المرور) -->
            <button class="btn btn-outline-secondary password-toggle-icon" type="button" id="togglePassword" aria-label="Show password">
                <i class="bi bi-eye-slash"></i>
            </button>
            <?php if (!empty($errors['password'])): ?>
                <div id="error-password" class="invalid-feedback d-block">
                    <?= htmlspecialchars($errors['password'][0], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Remember Me & Forgot Password -->
    <!-- (تذكرني ونسيت كلمة المرور) -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">
                Remember me
            </label>
        </div>
        <a href="/password/forgot" class="small text-decoration-none">Forgot password?</a>
    </div>

    <!-- Submit Button (Handles loading state via app.js) -->
    <!-- (زر الإرسال - يعالج حالة التحميل عبر app.js) -->
    <button type="submit" class="btn btn-primary w-100" aria-label="Log in">
        Login
    </button>

    <!-- Social Login Placeholder (Optional) -->
    <!-- (مكان تسجيل الدخول الاجتماعي - اختياري) -->
    <?php /* include __DIR__ . '/../partials/auth/_social_buttons.php'; */ ?>
    
    <!-- Link to Registration -->
    <!-- (رابط لصفحة التسجيل) -->
    <div class="text-center mt-4">
        <p class="text-muted">
            Don't have an account? 
            <a href="/register" class="text-decoration-none">Create an account</a>
        </p>
    </div>

</form>
<!-- END: Login Form -->