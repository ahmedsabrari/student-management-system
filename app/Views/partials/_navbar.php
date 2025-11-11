<?php
/**
 * File: app/Views/partials/_navbar.php
 *
 * This is the top navigation bar partial.
 * It is included by layouts/main.php.
 *
 * (هذا هو ملف شريط التنقل العلوي. يتم تضمينه بواسطة main.php)
 *
 * Expected variables:
 * @var array $user (From main.php) e.g., ['full_name' => 'Admin User', 'avatar' => null]
 * @var int $notifications_count (Optional) Number of unread notifications.
 */

// Get user data with safe defaults
// (جلب بيانات المستخدم مع قيم افتراضية آمنة)
$userName = htmlspecialchars($user['full_name'] ?? 'Guest User', ENT_QUOTES, 'UTF-8');
$userAvatar = htmlspecialchars($user['avatar'] ?? '/assets/images/avatar-default.png', ENT_QUOTES, 'UTF-8');
$userAvatarText = strtoupper(substr($userName, 0, 1));
$notificationCount = $notifications_count ?? 0;
?>

<!-- 
  Top Navbar
  - Uses Bootstrap 5 classes
  - Uses custom CSS variables for colors (var(--bg), var(--border-color))
  - (يستخدم كلاسات Bootstrap 5 ومتغيرات CSS للألوان)
-->
<nav class="navbar navbar-expand navbar-light bg-white shadow-sm sticky-top app-navbar" role="navigation" aria-label="Top Navigation">
    <div class="container-fluid">

        <!-- 
          Mobile Sidebar Toggle (Hamburger Menu)
          (زر فتح الشريط الجانبي للموبايل)
        -->
        <button class="btn btn-ghost d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar" aria-label="Toggle navigation">
            <span class="material-icons">menu</span>
        </button>

        <!-- 
          Global Search Form (Desktop)
          (نموذج البحث العام - لسطح المكتب)
        -->
        <form class="d-none d-md-flex ms-3" role="search" action="/search">
            <div class="input-group input-group-sm">
                <span class="input-group-text" id="basic-addon1">
                    <span class="material-icons">search</span>
                </span>
                <input class="form-control" type="search" name="q" placeholder="Search..." aria-label="Search">
            </div>
        </form>

        <!-- 
          Navbar Right-side Actions
          (الأزرار على الجانب الأيمن)
        -->
        <ul class="navbar-nav ms-auto align-items-center">

            <!-- 
              Theme Toggle Button (Light/Dark)
              (زر تبديل المظهر)
            -->
            <li class="nav-item">
                <button id="themeToggle" class="btn btn-ghost" type="button" aria-label="Toggle theme">
                    <span class="material-icons icon-light">light_mode</span>
                    <span class="material-icons icon-dark">dark_mode</span>
                </button>
            </li>

            <!-- 
              Notifications Dropdown
              (قائمة الإشعارات المنسدلة)
            -->
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                    <span class="material-icons">notifications</span>
                    <?php if ($notificationCount > 0): ?>
                        <span class="badge rounded-pill bg-danger badge-notification"><?= $notificationCount ?></span>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown">
                    <li class="dropdown-header">You have <?= $notificationCount ?> unread notifications</li>
                    <li><hr class="dropdown-divider"></li>
                    <!-- @TODO: Loop through notifications here -->
                    <!-- (يتم عرض الإشعارات هنا) -->
                    <li><a class="dropdown-item" href="#">Notification 1...</a></li>
                    <li><a class="dropdown-item" href="/notifications">View all notifications</a></li>
                </ul>
            </li>

            <!-- 
              Messages Dropdown
              (قائمة الرسائل المنسدلة)
            -->
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" id="msgDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Messages">
                    <span class="material-icons">message</span>
                    <!-- @TODO: Add message count badge -->
                    <!-- <span class="badge rounded-pill bg-success badge-notification">1</span> -->
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="msgDropdown">
                    <li class="dropdown-header">New Messages</li>
                    <li><hr class="dropdown-divider"></li>
                    <!-- @TODO: Loop through messages here -->
                    <li><a class="dropdown-item" href="/messages">View all messages</a></li>
                </ul>
            </li>

            <!-- 
              User Avatar & Dropdown
              (صورة المستخدم والقائمة المنسدلة)
            -->
            <li class="nav-item dropdown">
                <a class="nav-link d-flex align-items-center" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?= $userAvatar ?>" 
                         alt="<?= $userName ?>" 
                         class="rounded-circle avatar-sm"
                         onerror="this.src='https://placehold.co/32x32/1E3A8A/FFFFFF?text=<?= $userAvatarText ?>';">
                    <span class="ms-2 d-none d-md-inline"><?= $userName ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                    <li>
                        <a class="dropdown-item" href="/settings/profile">
                            <span class="material-icons me-2">person</span>
                            Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="/settings">
                            <span class="material-icons me-2">settings</span>
                            Settings
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <!-- Logout Form -->
                        <form action="/logout" method="POST" class="d-inline">
                            <?= \App\Helpers\CSRF::inputField() // CSRF Token ?>
                            <button type="submit" class="dropdown-item text-danger">
                                <span class="material-icons me-2">logout</span>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>

    </div>
</nav>