<?php
/*
 * File: app/Views/auth/register.php
 *
 * This is the registration form content, injected into the layouts/auth.php.
 * (هذا هو محتوى نموذج التسجيل، يتم حقنه داخل layouts/auth.php)
 *
 * Expected variables:
 * @var array $errors    (Optional) Validation errors (e.g., ['email' => 'Field is required'])
 * @var array $old_input (Optional) Old form input to repopulate fields (e.g., ['email' => 'test'])
 */

// Set title for the layout (will be picked up by layouts/auth.php)
// (تحديد العنوان للقالب)
$title = "Create Your Account";

// Get old input and errors, with safe defaults
// (جلب المدخلات القديمة والأخطاء، مع قيم افتراضية آمنة)
$errors = $errors ?? [];
$old_input = $old_input ?? [];
?>

<form action="/register" method="POST" id="registerForm" class="needs-validation" novalidate>

    <?= \App\Helpers\CSRF::inputField() // Assumes CSRF helper exists ?>

    <div class="form-floating mb-3">
        <input 
            type="text" 
            class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" 
            id="full_name" 
            name="full_name"
            value="<?= htmlspecialchars($old_input['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            placeholder="Enter your full name" 
            required 
            aria-describedby="error-full_name">
        <label for="full_name">Full Name</label>
        <?php if (!empty($errors['full_name'])): ?>
            <div id="error-full_name" class="invalid-feedback d-block">
                <?= htmlspecialchars($errors['full_name'][0], ENT_QUOTES, 'UTF-8') // Display first error ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="form-floating mb-3">
        <input 
            type="text" 
            class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
            id="username" 
            name="username"
            value="<?= htmlspecialchars($old_input['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            placeholder="Choose a username" 
            required 
            aria-describedby="error-username">
        <label for="username">Username</label>
        <?php if (!empty($errors['username'])): ?>
            <div id="error-username" class="invalid-feedback d-block">
                <?= htmlspecialchars($errors['username'][0], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-floating mb-3">
        <input 
            type="email" 
            class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
            id="email" 
            name="email"
            value="<?= htmlspecialchars($old_input['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            placeholder="Enter your email" 
            required 
            aria-describedby="error-email">
        <label for="email">Email Address</label>
        <?php if (!empty($errors['email'])): ?>
            <div id="error-email" class="invalid-feedback d-block">
                <?= htmlspecialchars($errors['email'][0], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group has-validation">
            <input 
                type="password" 
                class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                id="password" 
                name="password" 
                placeholder="Create a strong password" 
                required 
                aria-describedby="error-password password-strength-meter">
            <button class="btn btn-outline-secondary password-toggle-icon" type="button" id="togglePassword" aria-label="Show password">
                <i class="bi bi-eye-slash"></i>
            </button>
        </div>
        <div id="password-strength-meter" class="progress mt-2" style="height: 5px;">
            <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <small id="password-strength-text" class="form-text"></small>
        <?php if (!empty($errors['password'])): ?>
            <div id="error-password" class="invalid-feedback d-block">
                <?= htmlspecialchars($errors['password'][0], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <div class="input-group has-validation">
            <input 
                type="password" 
                class="form-control <?= isset($errors['password_confirmation']) ? 'is-invalid' : '' ?>" 
                id="password_confirmation" 
                name="password_confirmation" 
                placeholder="Confirm your password" 
                required 
                aria-describedby="error-password-confirm">
            <button class="btn btn-outline-secondary password-toggle-icon" type="button" id="togglePasswordConfirm" aria-label="Show password">
                <i class="bi bi-eye-slash"></i>
            </button>
        </div>
        <?php if (!empty($errors['password_confirmation'])): ?>
            <div id="error-password-confirm" class="invalid-feedback d-block">
                <?= htmlspecialchars($errors['password_confirmation'][0], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-check mb-4">
        <input 
            class="form-check-input <?= isset($errors['accept_terms']) ? 'is-invalid' : '' ?>" 
            type="checkbox" 
            value="1" 
            id="accept_terms" 
            name="accept_terms" 
            required
            aria-describedby="error-terms">
        <label class="form-check-label" for="accept_terms">
            I agree to the <a href="/terms-of-service" target="_blank" class="text-decoration-none">Terms & Conditions</a>
        </label>
        <?php if (!empty($errors['accept_terms'])): ?>
            <div id="error-terms" class="invalid-feedback d-block">
                <?= htmlspecialchars($errors['accept_terms'][0], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary w-100" aria-label="Create account">
        Create Account
    </button>
    
    <div class="text-center mt-4">
        <p class="text-muted">
            Already have an account? 
            <a href="/login" class="text-decoration-none">Login here</a>
        </p>
    </div>

</form>
<script>
    (function () {
      'use strict'
      var forms = document.querySelectorAll('.needs-validation')
      Array.prototype.slice.call(forms)
        .forEach(function (form) {
          form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
              event.preventDefault()
              event.stopPropagation()
            }
            form.classList.add('was-validated')
          }, false)
        })
    })()
</script>