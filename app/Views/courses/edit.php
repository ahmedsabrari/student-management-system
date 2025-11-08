<?php
/**
 * Course Edit View
 *
 * Renders the form for editing an existing course.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $course The course object to be edited, passed from CourseController::edit().
 * @var array $departments An array of department objects.
 * @var array $teachers An array of teacher objects.
 * @var array $errors (Assumed to be passed from a Validator class on failure)
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Edit Course: " . htmlspecialchars($course->name);
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Edit Course</h2>
    <a href="/courses" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Course List
    </a>
</div>

<!-- 
    Edit Course Form
    نموذج تعديل المقرر
-->
<div class="card shadow-sm">
    <div class="card-body">
        <!-- The form action points to the update route for this specific course -->
        <!-- مسار النموذج يشير إلى رابط التحديث الخاص بهذا المقرر المحدد -->
        <form action="/courses/<?= htmlspecialchars($course->id) ?>/update" method="POST" id="editCourseForm">
            
            <!-- 
                CSRF Token (Cross-Site Request Forgery Protection)
                حماية CSRF (حقل مخفي)
                @TODO: Replace with a dynamic CSRF token.
            -->
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">

            <div class="row">
                <!-- Course Code (often read-only) -->
                <div class="col-md-6 mb-3">
                    <label for="code" class="form-label">Course Code</label>
                    <input type="text" class="form-control" id="code" name="code" required 
                           value="<?= htmlspecialchars($course->code ?? '') ?>" readonly>
                    <small class="form-text text-muted">Course code cannot be changed.</small>
                </div>

                <!-- Course Name -->
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Course Name</label>
                    <input type="text" class="form-control" id="name" name="name" required
                           value="<?= htmlspecialchars($course->name ?? '') ?>">
                </div>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($course->description ?? '') ?></textarea>
            </div>

            <div class="row">
                <!-- Department Dropdown -->
                <div class="col-md-6 mb-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select class="form-select" id="department_id" name="department_id" required>
                        <option value="" disabled>Select a Department...</option>
                        <?php if (!empty($departments)): ?>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= htmlspecialchars($department->id) ?>" 
                                    <?= ($course->department_id == $department->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($department->name) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Teacher Dropdown -->
                <div class="col-md-6 mb-3">
                    <label for="teacher_id" class="form-label">Assigned Teacher</label>
                    <select class="form-select" id="teacher_id" name="teacher_id">
                        <option value="">Select a Teacher (Optional)...</option>
                        <?php if (!empty($teachers)): ?>
                            <?php foreach ($teachers as $teacher): ?>
                                <?php $user = $teacher->user(); ?>
                                <option value="<?= htmlspecialchars($teacher->id) ?>"
                                    <?= ($course->teacher_id == $teacher->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user->full_name ?? 'N/A') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <!-- Credits / Hours -->
                <div class="col-md-6 mb-3">
                    <label for="credit_hours" class="form-label">Credits / Hours</label>
                    <input type="number" class="form-control" id="credit_hours" name="credit_hours" required min="1" max="10"
                           value="<?= htmlspecialchars($course->credit_hours ?? '3') ?>">
                </div>

                <!-- Status (Placeholder, as status is not in the `courses` table schema) -->
                <!-- 
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                -->
            </div>

            <hr>

            <!-- 
                Form Action Buttons
                أزرار الإجراءات
            -->
            <div class="d-flex justify-content-end">
                <a href="/courses" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Course</button>
            </div>
        </form>
    </div>
</div>