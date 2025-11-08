<?php
/**
 * Assignment Create View
 *
 * Renders the form for adding a new assignment.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $classes An array of class objects passed from AssignmentController::create().
 * @var array $errors (Assumed from a Validator class on failure)
 * @var array $old_input (Assumed for repopulating form on error)
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Create New Assignment";
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Create New Assignment</h2>
    <a href="/assignments" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Assignment List
    </a>
</div>

<!-- 
    New Assignment Form
    نموذج إضافة واجب جديد
-->
<div class="card shadow-sm">
    <div class="card-body">
        <!-- Add enctype for file uploads -->
        <!-- إضافة enctype لرفع الملفات -->
        <form action="/assignments" method="POST" id="createAssignmentForm" enctype="multipart/form-data">
            
            <!-- 
                CSRF Token (Cross-Site Request Forgery Protection)
                حماية CSRF (حقل مخفي)
                @TODO: Replace with a dynamic CSRF token.
            -->
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">

            <!-- Assignment Title -->
            <div class="mb-3">
                <label for="title" class="form-label">Assignment Title</label>
                <input type="text" class="form-control" id="title" name="title" required placeholder="e.g., Chapter 5 Homework, Midterm Project Proposal">
                <!-- @TODO: Display validation error for 'title' -->
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description / Instructions</label>
                <textarea class="form-control" id="description" name="description" rows="5" placeholder="Provide detailed instructions for the assignment..."></textarea>
            </div>

            <div class="row">
                <!-- Class Dropdown -->
                <div class="col-md-6 mb-3">
                    <label for="class_id" class="form-label">Class</label>
                    <select class="form-select" id="class_id" name="class_id" required>
                        <option value="" disabled selected>Select a Class...</option>
                        <?php if (!empty($classes)): ?>
                            <?php foreach ($classes as $class): ?>
                                <?php $course = $class->course(); // Get course for display ?>
                                <option value="<?= htmlspecialchars($class->id) ?>">
                                    <?= htmlspecialchars($class->name) ?> (<?= htmlspecialchars($course->name ?? 'N/A') ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <!-- @TODO: Display validation error for 'class_id' -->
                </div>
                
                 <!-- Due Date -->
                <div class="col-md-6 mb-3">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" class="form-control" id="due_date" name="due_date">
                </div>
            </div>

             <div class="row">
                 <!-- Max Points -->
                <div class="col-md-6 mb-3">
                    <label for="max_points" class="form-label">Total Marks / Max Points</label>
                    <input type="number" class="form-control" id="max_points" name="max_points" min="0" step="1" value="100">
                </div>
                
                <!-- Optional File Upload -->
                <div class="col-md-6 mb-3">
                    <label for="assignment_file" class="form-label">Attach File (Optional)</label>
                    <input class="form-control" type="file" id="assignment_file" name="assignment_file">
                     <!-- @TODO: Display validation error for file upload (size, type) -->
                </div>
            </div>

            <hr>

            <!-- 
                Form Action Buttons
                أزرار الإجراءات
            -->
            <div class="d-flex justify-content-end">
                <a href="/assignments" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Assignment</button>
            </div>
        </form>
    </div>
</div>