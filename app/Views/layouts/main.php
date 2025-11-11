<?php
/*
 * File: app/Views/layouts/main.php
 *
 * This is the main application layout for the Admin/Dashboard area.
 * It follows the specified design brief, using Bootstrap 5 enhanced with
 * custom CSS variables for a modern, flat, light/dark, and RTL-ready theme.
 *
 * Expected variables from Controller/View:
 * ------------------------------------------------
 * @var string $title           (Required) Page title.
 * @var array  $user            (Required) Associative array of the logged-in user.
 * e.g., ['id','full_name','email','role','avatar']
 * @var array  $breadcrumbs     (Optional) Array of breadcrumb links.
 * e.g., [['label'=>'Dashboard','url'=>'/dashboard'], ['label'=>'Students','url'=>null]]
 * @var string $activeMenu      (Optional) Key for the active sidebar item (e.g., 'students').
 * @var string $pageActionButton (Optional) Safe HTML string for the primary action button(s) in the page header.
 * @var string $content         (Injected by View::render) The main page content.
 * @var string $theme           (Optional) 'light' or 'dark', usually loaded from user preference/session.
 * @var string $langDir         (Optional) 'ltr' or 'rtl' for language direction.
 * @var string $headExtra       (Optional) String to inject extra <link> or <style> tags into the <head>.
 * @var string $footerExtra     (Optional) String to inject extra <script> tags before <body> close.
 * @var string $appName         (Optional) The application name from config.
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
  
  <!-- CSRF Token for JS-based requests (e.g., Axios/Fetch) -->
  <meta name="csrf-token" content="<?= \App\Helpers\CSRF::getToken() // Assumes CSRF helper exists ?>">

  <title><?= htmlspecialchars($title ?? 'Dashboard') ?> | Student Management System</title>

  <!-- Google Fonts: Poppins (Latin) + Tajawal (Arabic) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

  <!-- Icons: Material Icons (as requested) & Bootstrap Icons (for snippets) -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <!-- 
    CSS: 
    1. Bootstrap 5 Core (Loaded first)
    2. Our compiled custom theme (style.css)
  -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="/assets/css/style.css"> <!-- This file contains all custom vars and overrides -->

  <!-- Place for page-specific head injections (e.g., custom CSS) -->
  <?php if (!empty($headExtra)) echo $headExtra; ?>

  <script>
      // Theme loader script (prevents FOUC)
      if (localStorage.getItem('sms_theme') === 'dark' || (!('sms_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
          document.documentElement.dataset.theme = 'dark';
      } else {
          document.documentElement.dataset.theme = 'light';
      }
  </script>
</head>
<body class="app-body" data-sidebar-collapsed="false">
  
  <!-- 
    app-layout manages the sidebar + main content flex
    (يتحكم في تخطيط الشريط الجانبي والمحتوى الرئيسي)
  -->
  <div id="app" class="app-layout">
    
    <!-- 
      Sidebar
      (We include the partial, passing required variables)
      (نقوم بتضمين الشريط الجانبي، مع تمرير المتغيرات المطلوبة)
    -->
    <?php include __DIR__ . '/../partials/_sidebar.php'; ?>

    <!-- 
      Main content wrapper (includes navbar, header, content, footer)
      (غلاف المحتوى الرئيسي)
    -->
    <div class="main-wrapper">
      
      <!-- 
        Navbar
        (We include the partial, passing required variables)
        (نقوم بتضمين شريط التنقل العلوي)
      -->
      <?php include __DIR__ . '/../partials/_navbar.php'; ?>

      <!-- 
        Flash Messages
        (We include the partial)
        (نقوم بتضمين رسائل الفلاش)
      -->
      <div class="flash-container container-fluid">
        <?php include __DIR__ . '/../partials/_flash.php'; ?>
      </div>

      <!-- 
        Page Header (Title, Breadcrumbs, Actions)
        (ترويسة الصفحة: العنوان، مسار التنقل، الأزرار)
      -->
      <div class="page-header container-fluid">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <!-- Breadcrumbs -->
            <?php include __DIR__ . '/../partials/_breadcrumb.php'; ?>
            <!-- Page Title -->
            <h1 class="h4 mt-2 mb-0"><?= htmlspecialchars($title ?? '') ?></h1>
          </div>

          <!-- Page Actions (e.g., "Add New Student" button) -->
          <div class="page-actions">
            <!-- 
              This placeholder is dynamically filled by the controller/view
              (يتم ملء هذا المكان ديناميكيًا بواسطة الـ Controller)
            -->
            <?php if (!empty($pageActionButton)): ?>
              <?= $pageActionButton /* Controller MUST ensure this is safe HTML */ ?>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- 
        Main Page Content
        (This is where the specific view (e.g., students/index.php) gets injected)
        (هنا يتم حقن محتوى الصفحة المحدد)
      -->
      <div classs="content container-fluid py-4">
        <?= $content /* Main page content injected by View::render() or Controller */ ?>
      </div>

      <!-- 
        Footer
        (Inlined as per skeleton example)
        (مدمج حسب مثال الهيكل)
      -->
      <?php include __DIR__ . '/../partials/_footer.php'; ?>
    </div>
  </div>

  <!-- 
    Modals
    (We include the generic confirmation modal)
    (نقوم بتضمين نافذة التأكيد العامة)
  -->
  <?php include __DIR__ . '/../partials/_confirm_modal.php'; ?>

  <!-- 
    JavaScript
    1. Bootstrap 5 Bundle (for Modals, Dropdowns, Offcanvas)
    2. Main application logic (app.js)
  -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script type="module" src="/assets/js/app.js"></script>
  
  <!-- Place for page-specific script injections (e.g., Chart.js init) -->
  <?php if (!empty($footerExtra)) echo $footerExtra; ?>
</body>
</html>