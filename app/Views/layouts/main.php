<?php
// We will call all helpers using their full namespace path
// سنقوم باستدعاء جميع المساعدين باستخدام مسارهم الكامل
?>
<!DOCTYPE html>
<html lang="en" dir="<?= htmlspecialchars($langDir ?? 'ltr') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= htmlspecialchars($title ?? 'Student Management System') ?></title>
    
    <!-- Bootstrap 5 CSS (from CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Font Awesome CDN (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Your custom CSS (Must be loaded AFTER Bootstrap) -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

    <!-- Simple Bootstrap Navbar -->
    <header class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a href="/dashboard" class="navbar-brand">
                <i class="fas fa-graduation-cap me-2"></i> Student System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php if (\App\Helpers\isLoggedIn()): ?>
                        <li class="nav-item">
                            <a href="/settings/profile" class="nav-link">
                                <i class="fas fa-user me-1"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- Logout Form -->
                            <form action="/logout" method="POST" class="d-inline">
                                <?= \App\Helpers\CSRF::inputField() ?>
                                <button type="submit" class="btn btn-link nav-link" style="padding: 0.5rem 1rem;">
                                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                                </button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="/login" class="nav-link">Login</a>
                        </li>
                        <li class="nav-item">
                            <a href="/register" class="nav-link">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <div class="row">
            <!-- Sidebar Navigation (Roles Applied for Student) -->
            <aside class="col-lg-3">
                <div class="list-group shadow-sm">
                    <!-- Check if user is logged in -->
                    <?php if (\App\Helpers\isLoggedIn()): ?>
                    
                        <a class="list-group-item list-group-item-action <?= \App\Helpers\Utils::isActiveRoute('/dashboard') ? 'active' : '' ?>" href="/dashboard">
                            <i class="fas fa-home fa-fw me-2"></i> Dashboard
                        </a>
                        <a class="list-group-item list-group-item-action <?= \App\Helpers\Utils::isActiveRoute('/settings/profile') ? 'active' : '' ?>" href="/settings/profile">
                            <i class="fas fa-user-cog fa-fw me-2"></i> My Profile
                        </a>

                        <!-- Student-Specific Links -->
                        <?php if (\App\Helpers\isStudent()): ?>
                            <div class="list-group-item list-group-item-light bg-light fw-bold">Academics</div>
                            <a class="list-group-item list-group-item-action <?= \App\Helpers\Utils::isActiveRoute('/my-courses') ? 'active' : '' ?>" href="/my-courses">
                                <i class="fas fa-book fa-fw me-2"></i> My Courses
                            </a>
                            <a class="list-group-item list-group-item-action <?= \App\Helpers\Utils::isActiveRoute('/my-grades') ? 'active' : '' ?>" href="/my-grades">
                                <i class="fas fa-graduation-cap fa-fw me-2"></i> My Grades
                            </a>
                            <a class="list-group-item list-group-item-action <?= \App\Helpers\Utils::isActiveRoute('/my-attendance') ? 'active' : '' ?>" href="/my-attendance">
                                <i class="fas fa-list-check fa-fw me-2"></i> My Attendance
                            </a>
                            <a class="list-group-item list-group-item-action <?= \App\Helpers\Utils::isActiveRoute('/my-assignments') ? 'active' : '' ?>" href="/my-assignments">
                                <i class="fas fa-tasks fa-fw me-2"></i> My Assignments
                            </a>
                        <?php endif; ?>

                        <!-- Admin Links (Hidden) -->
                        <?php if (\App\Helpers\isAdmin()): ?>
                             <div class="list-group-item list-group-item-light bg-light fw-bold">Admin</div>
                            <a class="list-group-item list-group-item-action" href="/students">Student Management</a>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <a class="list-group-item list-group-item-action" href="/login">
                            <i class="fas fa-sign-in-alt me-2"></i> Login
                        </a>
                        <a class="list-group-item list-group-item-action" href="/register">
                            <i class="fas fa-user-plus me-2"></i> Register
                        </a>
                    <?php endif; ?>
                </ul>
            </aside>

            <!-- Main Content Area -->
            <main class="col-lg-9">
                
                <!-- 
                    Display Flash Messages (Bootstrap Styled)
                    عرض رسائل الفلاش (بتنسيق Bootstrap)
                -->
                <?php \App\Helpers\displayFlash(); ?>

                <!-- 
                    Dynamic Page Content Injection
                    حقن محتوى الصفحة الديناميكي
                -->
                <?php echo $content; ?>
                
            </main>
        </div>
    </div>

    <footer class="footer mt-auto py-3 bg-light">
         <div class="container text-center">
            <p class="text-muted mb-0">&copy; <?= date('Y') ?> Student Management System. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <!-- Your custom JS file -->
    <script src="/assets/js/app.js"></script>
</body>
</html>