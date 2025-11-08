<?php
/**
 * Class Create View
 *
 * Renders the form for adding a new class to the system.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $courses An array of course objects passed from ClassController::create().
 * @var array $teachers An array of teacher objects.
 * @var array $academic_years An array of academic year objects.
 * @var array $errors (Assumed to be passed from a Validator class on failure)
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Add New Class";
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Create New Class</h2>
    <a href="/classes" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Class List
    </a>
</div>

<!-- 
    New Class Form
    نموذج إضافة فصل جديد
-->
<div class="card shadow-sm">
    <div class="card-body">
        <form action="/classes" method="POST" id="createClassForm">
            
            <!-- 
                CSRF Token (Cross-Site Request Forgery Protection)
                حماية CSRF (حقل مخفي)
                @TODO: Replace with a dynamic CSRF token.
            -->
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">

            <div class="row">
                <!-- Class Name -->
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Class Name</label>
                    <input type="text" class="form-control" id="name" name="name" required placeholder="e.g., Section A, Morning Group">
                    <!-- @TODO: Display validation error for 'name' -->
                </div>

                <!-- Course Dropdown -->
                <div class="col-md-6 mb-3">
                    <label for="course_id" class="form-label">Course</label>
                    <select class="form-select" id="course_id" name="course_id" required>
                        <option value="" disabled selected>Select a Course...</option>
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= htmlspecialchars($course->id) ?>">
                                    <?= htmlspecialchars($course->name) ?> (<?= htmlspecialchars($course->code) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <!-- @TODO: Display validation error for 'course_id' -->
                </div>
            </div>

            <div class="row">
                <!-- Teacher Dropdown -->
                <div class="col-md-6 mb-3">
                    <label for="teacher_id" class="form-label">Teacher</label>
                    <select class="form-select" id="teacher_id" name="teacher_id">
                        <option value="">Select a Teacher (Optional)...</option>
                        <?php if (!empty($teachers)): ?>
                            <?php foreach ($teachers as $teacher): ?>
                                <?php $user = $teacher->user(); ?>
                                <option value="<?= htmlspecialchars($teacher->id) ?>">
                                    <?= htmlspecialchars($user->full_name ?? 'N/A') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <!-- Academic Year Dropdown -->
                <div class="col-md-6 mb-3">
                    <label for="academic_year_id" class="form-label">Academic Year</label>
                    <select class="form-select" id="academic_year_id" name="academic_year_id">
                        <option value="">Select an Academic Year (Optional)...</option>
                        <?php if (!empty($academic_years)): ?>
                            <?php foreach ($academic_years as $year): ?>
                                <option value="<?= htmlspecialchars($year->id) ?>" <?= $year->is_current ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($year->name) ?> <?= $year->is_current ? '(Current)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <!-- Semester -->
                <div class="col-md-6 mb-3">
                    <label for="semester" class="form-label">Semester</label>
                    <select class="form-select" id="semester" name="semester" required>
                        <option value="Fall" selected>Fall</option>
                        <option value="Spring">Spring</option>
                        <option value="Summer">Summer</option>
                        <option value="Winter">Winter</option>
                    </select>
                </div>

                <!-- Max Students -->
                <div class="col-md-6 mb-3">
                    <label for="max_students" class="form-label">Max Students</label>
                    <input type="number" class="form-control" id="max_students" name="max_students" required min="1" value="30">
                </div>
            </div>
            
            <!-- Schedule -->
            <div class="mb-3">
                <label for="schedule" class="form-label">Schedule Notes</label>
                <textarea class="form-control" id="schedule" name="schedule" rows="3" placeholder="e.g., Mondays & Wednesdays, 10:00 AM - 11:30 AM, Room C102"></textarea>
            </div>


            <hr>

            <!-- 
                Form Action Buttons
                أزرار الإجراءات
            -->
            <div class="d-flex justify-content-end">
                <a href="/classes" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Class</button>
            </div>
        </form>
    </div>
</div>