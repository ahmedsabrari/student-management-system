<?php
/*
 * File: app/Views/layouts/auth.php
 *
 * This is the main layout for all authentication pages (Login, Register, Forgot Password).
 * It uses a Bootstrap 5 + Custom CSS variable theme.
 *
 * Expected variables from Controller/View:
 * ------------------------------------------------
 * @var string $title     (Required) Page title (e.g., "Login", "Register Account").
 * @var string $content   (Injected by View::render) The main form content (e.g., auth/login.php).
 * @var string $langDir   (Optional) 'ltr' or 'rtl' for language direction.
 * @var string $theme     (Optional) 'light' or 'dark' (loaded from JS).
 * @var array  $errors    (Optional) Validation errors to display.
 * @var array  $old_input (Optional) Old form input to repopulate fields on error.
 */
?>
<!doctype html>
<!-- 
  Theme is controlled by data-theme, toggled by app.js
  Direction is controlled by $langDir
-->
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>" dir="<?= htmlspecialchars($langDir ?? 'ltr') ?>" data-theme="<?= htmlspecialchars($theme ?? 'light') ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token for JS-based requests (if needed) -->
    <meta name="csrf-token" content="<?= \App\Helpers\CSRF::getToken() // Assumes CSRF helper exists ?>">

    <title><?= htmlspecialchars($title ?? 'Welcome') ?> | Student Management System</title>

    <!-- Google Fonts: Poppins (Latin) + Tajawal (Arabic) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Icons: Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- 
      CSS: 
      1. Bootstrap 5 Core
      2. Our compiled custom theme (style.css)
    -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/style.css"> <!-- Contains variables and auth layout styles -->

    <!-- Theme Loader Script (Prevents FOUC) -->
    <script>
        // This script must be in the <head> to prevent flash of unstyled content
        // (يجب أن يكون هذا السكريبت في الهيد لمنع وميض التصميم)
        (function() {
            const savedTheme = localStorage.getItem('sms_theme');
            if (savedTheme) {
                document.documentElement.dataset.theme = savedTheme;
            } else {
                // Fallback to system preference
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.dataset.theme = prefersDark ? 'dark' : 'light';
            }
        })();
    </script>
</head>
<body class="app-body auth-layout">

    <div class="container-fluid g-0">
        <div class="row g-0 min-vh-100">
            
            <!-- 
              Column 1: Illustration (Hidden on mobile)
              (العمود الأول: الرسم التوضيحي - مخفي على الموبايل)
            -->
            <div class="col-lg-7 d-none d-lg-block auth-illustration-wrapper">
                <!-- 
                  This background is styled in style.css 
                  (يتم تنسيق هذه الخلفية في style.css)
                -->
                <div class="auth-illustration-content">
                    <span class="material-icons">school</span>
                    <h2>Welcome to the Student Management System</h2>
                    <p>Your complete solution for academic administration.</p>
                </div>
            </div>

            <!-- 
              Column 2: Form Content (Centered)
              (العمود الثاني: محتوى النموذج - في المنتصف)
            -->
            <div class="col-lg-5 d-flex flex-column justify-content-center align-items-center p-4 p-md-5">
                
                <div class="auth-card">
                    <!-- Brand/Logo -->
                    <div class="auth-brand text-center mb-4">
                        <span class="material-icons brand-icon">school</span>
                        <h2 class="h4 mb-1">Student Management System</h2>
                    </div>

                    <!-- Page Title -->
                    <h1 class="h5 text-center mb-3"><?= htmlspecialchars($title ?? 'Login') ?></h1>

                    <!-- 
                      Flash Messages
                      (We include the partial _flash.php)
                      (نقوم بتضمين ملف رسائل الفلاش)
                    -->
                    <?php include __DIR__ . '/../partials/_flash.php'; ?>

                    <!-- 
                      Dynamic Page Content
                      (This is where auth/login.php or auth/register.php gets injected)
                      (هنا يتم حقن محتوى auth/login.php أو auth/register.php)
                    -->
                    <?= $content /* Main form content injected by View::render() */ ?>
                    
                    <!-- Theme Toggle (Small button at the bottom of the card) -->
                    <!-- (زر تبديل المظهر - أسفل البطاقة) -->
                    <div class="text-center mt-3">
                         <button id="themeToggle" class="btn btn-ghost" type="button" aria-label="Toggle theme">
                            <span class="material-icons icon-light">light_mode</span>
                            <span class="material-icons icon-dark">dark_mode</span>
                        </button>
                    </div>
                </div>

                <!-- Footer (Simple) -->
                <footer class="app-footer-auth text-center py-3 mt-4">
                    <small class="text-muted">
                        © <?= date('Y') ?> <?= htmlspecialchars($appName ?? 'Student Management System') ?>
                    </small>
                </footer>
            </div>

        </div>
    </div>


    <!-- 
      JavaScript
      1. Bootstrap 5 Bundle (for Modals, Dropdowns, etc.)
      2. Main application logic (app.js)
    -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script type="module" src="/assets/js/app.js"></script>
    
    <!-- Place for page-specific script injections -->
    <?php if (!empty($footerExtra)) echo $footerExtra; ?>
</body>
</html>