<?php
/**
 * File: app/Views/partials/_sidebar.php
 *
 * This is the main sidebar navigation partial.
 * It is included by layouts/main.php.
 * It uses the $activeMenu variable (passed from the controller) to highlight
 * the current page, and helper functions (e.g., \App\Helpers\isAdmin())
 * to show/hide links based on user roles.
 *
 * (هذا هو ملف الشريط الجانبي الجزئي. يتم تضمينه بواسطة main.php.
 * يستخدم متغير $activeMenu لتحديد الرابط النشط،
 * ويستخدم دوال المساعد لإظهار/إخفاء الروابط بناءً على دور المستخدم.)
 */

// Set default active menu if not provided
// (تعيين القائمة النشطة الافتراضية إذا لم يتم توفيرها)
$activeMenu = $activeMenu ?? 'dashboard'; 
?>

<!-- 
  Sidebar
  - Uses Bootstrap 5 off-canvas for mobile (offcanvas-lg).
  - Uses custom CSS classes (.sidebar, .sidebar-collapsed) for desktop toggle.
  - (يستخدم Bootstrap off-canvas للموبايل، وكلاسات مخصصة لطي سطح المكتب)
-->
<nav class="sidebar offcanvas-lg offcanvas-start" id="appSidebar" role="navigation" aria-label="Main Navigation">
    
    <!-- Offcanvas Header (Mobile Only) -->
    <!-- (ترويسة الموبايل فقط) -->
    <div class="offcanvas-header d-lg-none">
        <h5 class="offcanvas-title">
            <i class="bi bi-journal-bookmark-fill me-2" style="color: var(--color-primary);"></i>
            <span>Student Management</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#appSidebar" aria-label="Close"></button>
    </div>

    <!-- Sidebar Body (Main Nav) -->
    <div class="offcanvas-body d-flex flex-column p-0">
        
        <!-- Sidebar Header (Desktop) -->
        <!-- (ترويسة سطح المكتب) -->
        <div class="sidebar-header d-none d-lg-flex align-items-center justify-content-between">
            <a href="/dashboard" class="sidebar-brand">
                <!-- Material Icon (e.g., school) -->
                <span class="material-icons brand-icon">school</span>
                <span class="sidebar-text brand-text ms-2">SMS</span>
            </a>
            
            <!-- Desktop Collapse Toggle Button -->
            <!-- (زر طي الشريط الجانبي لسطح المكتب) -->
            <button class="btn btn-ghost" id="sidebarToggle" data-bs-toggle="tooltip" data-bs-placement="right" title="Toggle Sidebar">
                <span class="material-icons">menu_open</span>
            </button>
        </div>

        <!-- Scrollable Navigation Links -->
        <!-- (روابط التنقل القابلة للتمرير) -->
        <ul class_exists"nav nav-pills flex-column sidebar-nav flex-grow-1">

            <!-- 
              Main Navigation Section
              (قسم التنقل الرئيسي)
            -->
            <li class="nav-item">
                <a href="/dashboard" class="nav-link <?= ($activeMenu === 'dashboard') ? 'active' : '' ?>" aria-current="<?= ($activeMenu === 'dashboard') ? 'page' : 'false' ?>">
                    <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">dashboard</span>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>

            <!-- 
              Administration Section (Admin Only)
              (قسم الإدارة - للمدير فقط)
            -->
            <?php if (\App\Helpers\isAdmin()): ?>
                <li class="nav-item sidebar-heading">
                    <span class="sidebar-text">Management</span>
                </li>
                <li class="nav-item">
                    <a href="/students" class="nav-link <?= ($activeMenu === 'students') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="Students">group</span>
                        <span class="sidebar-text">Students</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/teachers" class="nav-link <?= ($activeMenu === 'teachers') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="Teachers">school</span>
                        <span class="sidebar-text">Teachers</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/departments" class="nav-link <?= ($activeMenu === 'departments') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="Departments">corporate_fare</span>
                        <span class="sidebar-text">Departments</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- 
              Academics Section (Admin & Teacher)
              (قسم الأكاديميات - للمدير والمعلم)
            -->
            <?php if (\App\Helpers\isAdmin() || \App\Helpers\isTeacher()): ?>
                <li class="nav-item sidebar-heading">
                    <span class="sidebar-text">Academics</span>
                </li>
                <li class="nav-item">
                    <a href="/courses" class="nav-link <?= ($activeMenu === 'courses') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="Courses">book</span>
                        <span class="sidebar-text">Courses</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/classes" class="nav-link <?= ($activeMenu === 'classes') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="Classes">class</span>
                        <span class="sidebar-text">Classes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/assignments" class="nav-link <?= ($activeMenu === 'assignments') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="Assignments">assignment</span>
                        <span class="sidebar-text">Assignments</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- 
              Student-Specific Links (Student Only)
              (روابط خاصة بالطالب - للطالب فقط)
            -->
            <?php if (\App\Helpers\isStudent()): ?>
                <li class="nav-item sidebar-heading">
                    <span class="sidebar-text">My Portal</span>
                </li>
                <li class="nav-item">
                    <a href="/my-courses" class="nav-link <?= ($activeMenu === 'my-courses') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="My Courses">book</span>
                        <span class="sidebar-text">My Courses</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="/my-grades" class="nav-link <?= ($activeMenu === 'my-grades') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="My Grades">grading</span>
                        <span class="sidebar-text">My Grades</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="/my-attendance" class="nav-link <?= ($activeMenu === 'my-attendance') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="My Attendance">checklist</span>
                        <span class="sidebar-text">My Attendance</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="/my-assignments" class="nav-link <?= ($activeMenu === 'my-assignments') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="My Assignments">assignment_turned_in</span>
                        <span class="sidebar-text">My Assignments</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- 
              Operations Section (Admin & Staff)
              (قسم العمليات - للمدير والموظفين)
            -->
            <?php if (\App\Helpers\isAdmin() || \App\Helpers\isStaff()): ?>
                <li class="nav-item sidebar-heading">
                    <span class="sidebar-text">Operations</span>
                </li>
                <li class="nav-item">
                    <a href="/enrollments" class="nav-link <?= ($activeMenu === 'enrollments') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="Enrollments">person_add</span>
                        <span class="sidebar-text">Enrollments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/fees" class="nav-link <?= ($activeMenu === 'fees') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="Fees">payments</span>
                        <span class="sidebar-text">Fees</span>
                    </a>
                </li>
            <?php endif; ?>
            
            <!-- 
              Teacher-Specific (Grades & Attendance)
              (خاص بالمعلم - الدرجات والحضور)
            -->
             <?php if (\App\Helpers\isTeacher()): ?>
                <li class="nav-item sidebar-heading">
                    <span class="sidebar-text">Teaching Tools</span>
                </li>
                <li class="nav-item">
                    <a href="/grades" class="nav-link <?= ($activeMenu === 'grades') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="Grades">grading</span>
                        <span class="sidebar-text">Manage Grades</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="/attendance" class="nav-link <?= ($activeMenu === 'attendance') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="Attendance">checklist</span>
                        <span class="sidebar-text">Manage Attendance</span>
                    </a>
                </li>
            <?php endif; ?>


            <!-- 
              System Section (Admin Only)
              (قسم النظام - للمدير فقط)
            -->
            <?php if (\App\Helpers\isAdmin()): ?>
                <li class="nav-item sidebar-heading">
                    <span class="sidebar-text">System</span>
                </li>
                <li class="nav-item">
                    <a href="/reports" class="nav-link <?= ($activeMenu === 'reports') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="Reports">bar_chart</span>
                        <span class="sidebar-text">Reports</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="/settings" class="nav-link <?= ($activeMenu === 'settings') ? 'active' : '' ?>">
                        <span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="right" title="Settings">settings</span>
                        <span class="sidebar-text">Settings</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>

    </div>
</nav>
