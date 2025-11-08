<?php
/**
 * Department Create View
 *
 * Renders the form for adding a new academic department.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $errors (Assumed to be passed from a Validator class on failure)
 * @var array $old_input (Assumed for repopulating form on error)
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Add New Department";
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Create New Department</h2>
    <a href="/departments" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Department List
    </a>
</div>

<!-- 
    New Department Form
    نموذج إضافة قسم جديد
-->
<div class="card shadow-sm">
    <div class="card-body">
        <form action="/departments" method="POST" id="createDepartmentForm">
            
            <!-- 
                CSRF Token (Cross-Site Request Forgery Protection)
                حماية CSRF (حقل مخفي)
                @TODO: Replace with a dynamic CSRF token.
            -->
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">

            <!-- Department Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Department Name</label>
                <input type="text" class="form-control" id="name" name="name" required placeholder="e.g., Computer Science, Business Administration">
                <!-- @TODO: Display validation error for 'name' if it exists -->
                <!-- مثال: <?php // if(isset($errors['name'])): ?> -->
                <!-- <div class="text-danger small mt-1"><?= $errors['name'] ?></div> -->
                <!-- <?php // endif; ?> -->
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter a brief description of the department..."></textarea>
            </div>

            <hr>

            <!-- 
                Form Action Buttons
                أزرار الإجراءات
            -->
            <div class="d-flex justify-content-end">
                <a href="/departments" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Department</button>
            </div>
        </form>
    </div>
</div>