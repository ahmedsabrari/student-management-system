<?php
// Call helpers using their full namespace path
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= htmlspecialchars($title ?? 'Admin Dashboard') ?> | Student System</title>
    
    <!-- Bootstrap 5 CSS (FIXED integrity attribute) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Font Awesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Your custom CSS file -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

    <!-- Top Navigation Bar -->
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="/dashboard">Student System</a>
        
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">
        
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <!-- Logout Form -->
                <form action="/logout" method="POST" style="display: inline;">
                    <?= \App\Helpers\CSRF::inputField() ?>
                    <button type="submit" class="nav-link px-3 bg-dark border-0">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <!-- Main Sidebar Navigation (Role checks are correctly implemented) -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        
                        <!-- Visible to All -->
                        <li class="nav-item">
                            <a class="nav-link" href="/dashboard">
                                <i class="fas fa-home me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/settings/profile">
                                <i class="fas fa-user-cog me-2"></i> My Profile
                            </a>
                        </li>
                        
                        <!-- Admin Only Links -->
                        <?php if (\App\Helpers\isAdmin()): ?>
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                                <span>Administration</span>
                            </h6>
                            <li class="nav-item">
                                <a class="nav-link" href="/students">
                                    <i class="fas fa-user-graduate me-2"></i> Students
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/teachers">
                                    <i class="fas fa-chalkboard-teacher me-2"></i> Teachers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/departments">
                                    <i class="fas fa-building me-2"></i> Departments
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Admin & Teacher Links -->
                        <?php if (\App\Helpers\isAdmin() || \App\Helpers\isTeacher()): ?>
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                                <span>Academics</span>
                            </h6>
                            <li class="nav-item">
                                <a class="nav-link" href="/courses">
                                    <i class="fas fa-book me-2"></i> Courses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/classes">
                                    <i class="fas fa-school me-2"></i> Classes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/enrollments">
                                    <i class="fas fa-user-plus me-2"></i> Enrollments
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/grades">
                                    <i class="fas fa-graduation-cap me-2"></i> Grades
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/attendance">
                                    <i class="fas fa-list-check me-2"></i> Attendance
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Admin Only (Reports & Settings) -->
                        <?php if (\App\Helpers\isAdmin()): ?>
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                                <span>System</span>
                            </h6>
                            <ul class="nav flex-column mb-2">
                                <li class="nav-item">
                                    <a class="nav-link" href="/reports/students">
                                        <i class="fas fa-chart-bar me-2"></i> Student Reports
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/reports/financial">
                                        <i class="fas fa-chart-line me-2"></i> Financial Reports
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/settings">
                                        <i class="fas fa-cogs me-2"></i> System Settings
                                    </a>
                                </li>
                            </ul>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>

            <!-- Main Content Area -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?= htmlspecialchars($title ?? 'Dashboard') ?></h1>
                </div>

                <!-- Flash Messages Area -->
                <?php \App\Helpers\displayFlash(); ?>

                <!-- Dynamic Page Content Injection -->
                <?php echo $content; ?>

                <footer class="pt-3 my-4 text-muted border-top">
                    &copy; <?= date('Y') ?> Student Management System
                </footer>

            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle (Fixed integrity) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <!-- Your custom JS file -->
    <script src="/assets/js/app.js"></script>
</body>
</html>

