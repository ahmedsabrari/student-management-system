<?php
/**
 * File: app/Views/partials/_flash.php
 *
 * This partial renders flash messages (success, error, warning, info)
 * stored in the session. It uses the custom helper \App\Helpers\displayFlash()
 * which should be configured to return Bootstrap 5 styled alerts.
 *
 * (يعرض هذا الملف رسائل الفلاش المخزنة في الجلسة)
 */
?>

<!-- 
  This container is included by main.php right above the content.
  (يتم تضمين هذه الحاوية بواسطة main.php)
  
  The \App\Helpers\displayFlash() function is expected to:
  1. Check Session for 'success', 'error', 'warning' keys.
  2. Get the message.
  3. Echo the full Bootstrap 5 alert HTML (as defined in the helper).
  4. Clear the flash message from the session.
  
  (دالة displayFlash() هي المسؤولة عن طباعة كود HTML الخاص بالتنبيه)
-->
<?php \App\Helpers\displayFlash(); ?>