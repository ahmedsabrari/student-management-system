<?php
/**
 * File: app/Views/partials/_confirm_modal.php
 *
 * A generic, reusable Bootstrap 5 confirmation modal.
 * It is triggered by buttons on the page (e.g., delete buttons).
 * JavaScript in app.js populates its content and form action.
 *
 * (نافذة تأكيد عامة قابلة لإعادة الاستخدام. يتم تشغيلها بواسطة JavaScript)
 */
?>

<!-- 
  Generic Confirmation Modal
  (نافذة تأكيد عامة)
-->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- 
                  This text is dynamically set by app.js using 'data-message' attribute
                  (يتم تعيين هذا النص ديناميكيًا بواسطة app.js)
                -->
                <p class="modal-body-text">Are you sure you want to proceed?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                
                <!-- 
                  This form's 'action' attribute is set dynamically by app.js
                  (يتم تعيين 'action' لهذا النموذج ديناميكيًا بواسطة app.js)
                -->
                <form id="confirmModalForm" method="POST" class="d-inline">
                    <!-- CSRF Token -->
                    <?= \App\Helpers\CSRF::inputField() ?>
                    
                    <!-- Method Spoofing (if needed for DELETE) -->
                    <!-- (تزييف نوع الطلب إذا احتجنا DELETE) -->
                    <input type="hidden" name="_method" value="DELETE"> <!-- Assuming deletes are POST with _method=DELETE -->
                    
                    <button type="submit" class="btn btn-danger">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>