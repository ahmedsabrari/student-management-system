<?php
/**
 * Login View (Enhanced Styling)
 *
 * This file contains the HTML form for user login with improved styling.
 * It is rendered within the 'auth.php' layout.
 */
use function App\Helpers\formInput; // Using form helper for consistency
use function App\Helpers\formButton;
use function App\Helpers\formOpen;
use function App\Helpers\formClose;

// Set the title for the layout
$title = "Login";
?>

<!-- Add custom styles for transitions and animations -->
<!-- إضافة تنسيقات مخصصة للتأثيرات الانتقالية والرسوم المتحركة -->
<style>
    .login-form-container {
        opacity: 0;
        animation: fadeIn 0.5s ease-out forwards;
        animation-delay: 0.1s; /* Slight delay */
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-floating .form-control {
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .btn-login {
        transition: background-color 0.2s ease-out, transform 0.1s ease-out;
    }
    .btn-login:hover {
        background-color: #0b5ed7; /* Darker blue */
        transform: translateY(-2px);
    }
    .btn-login:active {
         transform: translateY(0px);
    }
    
    /* Style for validation errors (to be shown by JS/PHP) */
    .invalid-feedback {
        display: none; /* Hide by default */
        width: 100%;
        margin-top: .25rem;
        font-size: .875em;
        color: #dc3545; /* Bootstrap danger color */
    }
    .form-control.is-invalid ~ .invalid-feedback,
    .form-select.is-invalid ~ .invalid-feedback {
         display: block; /* Show when is-invalid class is added */
    }

</style>

<div class="login-form-container">
    <?= formOpen('/login', 'POST', ['id' => 'loginForm', 'novalidate' => true, 'class' => 'needs-validation']) ?>
    
        <!-- Email Address Input with Floating Label -->
        <!-- حقل البريد الإلكتروني مع تسمية عائمة -->
        <div class="form-floating mb-3">
            <?= formInput('email', $old_input['email'] ?? '', 'email', [
                'id' => 'email',
                'class' => 'form-control' . (isset($errors['email']) ? ' is-invalid' : ''), // Add is-invalid if error exists
                'placeholder' => 'Enter your email', 
                'required' => true
            ]) ?>
            <label for="email">Email Address</label>
            <!-- Placeholder for server-side validation error -->
            <!-- مكان لرسالة خطأ التحقق من جانب الخادم -->
            <div class="invalid-feedback">
                <?= htmlspecialchars($errors['email'][0] ?? 'Please enter a valid email.') ?>
            </div>
        </div>

        <!-- Password Input with Floating Label -->
        <!-- حقل كلمة المرور مع تسمية عائمة -->
        <div class="form-floating mb-3">
             <?= formInput('password', '', 'password', [
                'id' => 'password',
                'class' => 'form-control' . (isset($errors['password']) ? ' is-invalid' : ''),
                'placeholder' => 'Enter your password', 
                'required' => true
            ]) ?>
            <label for="password">Password</label>
             <!-- Placeholder for server-side validation error -->
            <div class="invalid-feedback">
                 <?= htmlspecialchars($errors['password'][0] ?? 'Password is required.') ?>
            </div>
        </div>
        
        <!-- Remember Me & Forgot Password -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">
                    Remember Me
                </label>
            </div>
            <!-- <a href="/forgot-password" class="small text-decoration-none">Forgot Password?</a> -->
        </div>

        <!-- Submit Button with custom class -->
        <!-- زر الإرسال مع كلاس مخصص -->
         <?= formButton('Login', ['class' => 'btn btn-primary w-100 btn-lg btn-login']) ?>

        <!-- Link to Registration Page -->
        <!-- رابط لصفحة التسجيل -->
        <div class="text-center mt-4">
            <p class="text-muted">Don't have an account? <a href="/register" class="text-decoration-none">Register here</a></p>
        </div>

    <?= formClose() ?>
</div>

<!-- Bootstrap client-side validation script (optional but recommended) -->
<!-- سكريبت التحقق من جانب العميل الخاص بـ Bootstrap (اختياري لكن موصى به) -->
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
