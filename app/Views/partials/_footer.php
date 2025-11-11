<?php
/**
 * File: app/Views/partials/_footer.php
 *
 * This is the application footer partial.
 * It is included by layouts/main.php at the bottom.
 *
 * (هذا هو ملف التذييل الجزئي. يتم تضمينه بواسطة main.php)
 *
 * Expected variables:
 * @var string $appName (Optional, passed from main.php or controller)
 */
?>

<!-- 
  Application Footer
  (تذييل التطبيق)
-->
<footer class="app-footer text-center py-3 mt-auto">
    <small class="text-muted">
        © <?= date('Y') ?> <?= htmlspecialchars($appName ?? 'Student Management System') ?>. App Version 1.0.0
    </small>
</footer>