<?php
/**
 * Registration View (Enhanced Styling)
 *
 * This file contains the HTML form for new user registration with improved styling.
 * It is rendered within the 'auth.php' layout.
 */
use function App\Helpers\formOpen;
use function App\Helpers\formClose;
use function App\Helpers\formInput;
use function App\Helpers\formButton;

// Set the title for the layout
$title = "Register";

// Assume $errors and $old_input might be passed from the controller on validation failure
// افتراض أن $errors و $old_input قد يتم تمريرهما من الـ Controller عند فشل التحقق
$errors = $errors ?? [];
$old_input = $old_input ?? [];
?>

<!-- Add custom styles for transitions and animations -->
<!-- إضافة تنسيقات مخصصة للتأثيرات الانتقالية والرسوم المتحركة -->
<style>
    .register-form-container {
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
    
    .btn-register {
        transition: background-color 0.2s ease-out, transform 0.1s ease-out;
    }
    .btn-register:hover {
        background-color: #0d6efd; /* Slightly darker shade */
        transform: translateY(-2px);
    }
     .btn-register:active {
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

<div class="register-form-container">
    <?= formOpen('/register', 'POST', ['id' => 'registerForm', 'novalidate' => true, 'class' => 'needs-validation']) ?>

        <!-- Full Name Input -->
        <div class="form-floating mb-3">
             <?= formInput('full_name', $old_input['full_name'] ?? '', 'text', [
                'id' => 'full_name',
                'class' => 'form-control' . (isset($errors['full_name']) ? ' is-invalid' : ''),
                'placeholder' => 'Enter your full name',
                'required' => true
            ]) ?>
            <label for="full_name">Full Name</label>
            <div class="invalid-feedback">
                <?= htmlspecialchars($errors['full_name'][0] ?? 'Please enter your full name.') ?>
            </div>
        </div>

        <!-- Username Input -->
        <div class="form-floating mb-3">
             <?= formInput('username', $old_input['username'] ?? '', 'text', [
                'id' => 'username',
                'class' => 'form-control' . (isset($errors['username']) ? ' is-invalid' : ''),
                'placeholder' => 'Choose a username',
                'required' => true
            ]) ?>
            <label for="username">Username</label>
            <div class="invalid-feedback">
                 <?= htmlspecialchars($errors['username'][0] ?? 'Please choose a username.') ?>
            </div>
        </div>

        <!-- Email Address Input -->
        <div class="form-floating mb-3">
             <?= formInput('email', $old_input['email'] ?? '', 'email', [
                'id' => 'email',
                'class' => 'form-control' . (isset($errors['email']) ? ' is-invalid' : ''),
                'placeholder' => 'Enter your email',
                'required' => true
            ]) ?>
            <label for="email">Email Address</label>
            <div class="invalid-feedback">
                 <?= htmlspecialchars($errors['email'][0] ?? 'Please enter a valid email address.') ?>
            </div>
        </div>

        <!-- Password Input -->
        <div class="form-floating mb-3">
             <?= formInput('password', '', 'password', [
                'id' => 'password',
                'class' => 'form-control' . (isset($errors['password']) ? ' is-invalid' : ''),
                'placeholder' => 'Create a password',
                'required' => true
            ]) ?>
            <label for="password">Password</label>
             <div class="invalid-feedback">
                 <?= htmlspecialchars($errors['password'][0] ?? 'Please create a password.') ?>
            </div>
        </div>

        <!-- Confirm Password Input -->
        <div class="form-floating mb-4">
            <?= formInput('password_confirm', '', 'password', [
                'id' => 'password_confirm',
                'class' => 'form-control' . (isset($errors['password_confirm']) ? ' is-invalid' : ''),
                'placeholder' => 'Confirm your password',
                'required' => true
            ]) ?>
            <label for="password_confirm">Confirm Password</label>
            <div class="invalid-feedback">
                <?= htmlspecialchars($errors['password_confirm'][0] ?? 'Please confirm your password.') ?>
            </div>
        </div>

        <!-- Submit Button -->
        <?= formButton('Register', ['class' => 'btn btn-primary w-100 btn-lg btn-register']) ?>

        <!-- Link back to Login Page -->
        <div class="text-center mt-4">
            <p class="text-muted">Already have an account? <a href="/login" class="text-decoration-none">Login here</a></p>
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
