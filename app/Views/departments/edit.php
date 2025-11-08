<?php
/**
 * Department Edit View
 *
 * Renders the form for editing an existing academic department.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $department The department object to be edited, passed from DepartmentController::edit().
 * @var array $errors (Assumed to be passed from a Validator class on failure)
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Edit Department: " . htmlspecialchars($department->name);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Edit Department</h2>
    <a href="/departments" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Department List
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="/departments/<?= htmlspecialchars($department->id) ?>/update" method="POST" id="editDepartmentForm">
            
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">

            <div class="mb-3">
                <label for="name" class="form-label">Department Name</label>
                <input type="text" class="form-control" id="name" name="name" required 
                       value="<?= htmlspecialchars($department->name ?? '') ?>">
                </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($department->description ?? '') ?></textarea>
            </div>

            <hr>

            <div class="d-flex justify-content-end">
                <a href="/departments" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Department</button>
            </div>
        </form>
    </div>
</div>