<?php
/**
 * Course Create View
 *
 * Renders the form for adding a new course to the system.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $departments An array of department objects passed from CourseController::create().
 * @var array $teachers An array of teacher objects passed from CourseController::create().
 * @var array $errors (Assumed to be passed from a Validator class on failure)
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Add New Course";
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Create New Course</h2>
    <a href="/courses" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Course List
    </a>
</div>

<!-- 
    New Course Form
    نموذج إضافة مقرر جديد
-->
<div class="card shadow-sm">
    <div class="card-body">
        <form action="/courses" method="POST" id="createCourseForm">
            
            <!-- 
                CSRF Token (Cross-Site Request Forgery Protection)
                حماية CSRF (حقل مخفي)
                @TODO: Replace with a dynamic CSRF token.
            -->
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">

            <div class="row">
                <!-- Course Code -->
                <div class="col-md-6 mb-3">
                    <label for="code" class="form-label">Course Code</label>
                    <input type="text" class="form-control" id="code" name="code" required placeholder="e.g., CS101">
                    <!-- @TODO: Display validation error for 'code' -->
                </div>

                <!-- Course Name -->
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Course Name</label>
                    <input type="text" class="form-control" id="name" name="name" required placeholder="e.g., Introduction to Programming">
                    <!-- @TODO: Display validation error for 'name' -->
                </div>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter a brief description of the course..."></textarea>
            </div>

            <div class="row">
                <!-- Department Dropdown -->
                <div class="col-md-6 mb-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select class="form-select" id="department_id" name="department_id" required>
                        <option value="" disabled selected>Select a Department...</option>
                        <?php if (!empty($departments)): ?>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= htmlspecialchars($department->id) ?>">
                                    <?= htmlspecialchars($department->name) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Placeholder for demonstration -->
                            <option value="1">Computer Science</option>
                            <option value="2">Business Administration</option>
                        <?php endif; ?>
                    </select>
                    <!-- @TODO: Display validation error for 'department_id' -->
                </div>

                <!-- Teacher Dropdown -->
                <div class="col-md-6 mb-3">
                    <label for="teacher_id" class="form-label">Assigned Teacher</label>
                    <select class="form-select" id="teacher_id" name="teacher_id">
                        <option value="">Select a Teacher (Optional)...</option>
                        <?php if (!empty($teachers)): ?>
                            <?php foreach ($teachers as $teacher): ?>
                                <?php $user = $teacher->user(); // Get the associated user to display the name ?>
                                <option value="<?= htmlspecialchars($teacher->id) ?>">
                                    <?= htmlspecialchars($user->full_name ?? 'N/A') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                             <!-- Placeholder for demonstration -->
                             <option value="1">Dr. John Doe</option>
                             <option value="2">Prof. Jane Smith</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <!-- Credits / Hours -->
                <div class="col-md-6 mb-3">
                    <label for="credit_hours" class="form-label">Credits / Hours</label>
                    <input type="number" class="form-control" id="credit_hours" name="credit_hours" required min="1" max="10" value="3">
                </div>

                <!-- Status (Optional) -->
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <hr>

            <!-- 
                Form Action Buttons
                أزرار الإجراءات
            -->
            <div class="d-flex justify-content-end">
                <a href="/courses" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Course</button>
            </div>
        </form>
    </div>
</div>