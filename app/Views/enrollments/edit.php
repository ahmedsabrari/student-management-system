<?php
/**
 * Enrollment Edit View
 *
 * Renders the form for editing an existing student enrollment record.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $enrollment The enrollment object to be edited.
 * @var array $students An array of all student objects.
 * @var array $classes An array of all class objects.
 * @var array $academic_years An array of all academic year objects.
 * @var array $errors (Assumed from a Validator class on failure)
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Edit Enrollment #" . htmlspecialchars($enrollment->id);
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Edit Enrollment Record</h2>
    <a href="/enrollments" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Enrollment List
    </a>
</div>

<!-- 
    Edit Enrollment Form
    نموذج تعديل التسجيل
-->
<div class="card shadow-sm">
    <div class="card-body">
        <!-- The form action points to the update route for this specific enrollment -->
        <!-- مسار النموذج يشير إلى رابط التحديث الخاص بهذا التسجيل المحدد -->
        <form action="/enrollments/update/<?= htmlspecialchars($enrollment->id) ?>" method="POST" id="editEnrollmentForm">
            
            <!-- 
                CSRF Token (Cross-Site Request Forgery Protection)
                حماية CSRF (حقل مخفي)
                @TODO: Replace with a dynamic CSRF token.
            -->
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">

            <div class="row">
                <!-- Student Dropdown (Disabled) -->
                <div class="col-md-6 mb-3">
                    <label for="student_id" class="form-label">Student</label>
                    <select class="form-select" id="student_id" name="student_id" required disabled>
                        <option>
                            <?php 
                                // Find and display the student's name
                                // البحث عن اسم الطالب وعرضه
                                foreach ($students as $student) {
                                    if ($student->id == $enrollment->student_id) {
                                        echo htmlspecialchars($student->first_name . ' ' . $student->last_name);
                                        break;
                                    }
                                }
                            ?>
                        </option>
                    </select>
                    <small class="form-text text-muted">The student cannot be changed for an existing enrollment.</small>
                </div>

                <!-- Class Dropdown -->
                <div class="col-md-6 mb-3">
                    <label for="class_id" class="form-label">Class</label>
                    <select class="form-select" id="class_id" name="class_id" required>
                        <option value="" disabled>Select a Class...</option>
                         <?php if (!empty($classes)): ?>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= htmlspecialchars($class->id) ?>"
                                    <?= ($enrollment->class_id == $class->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($class->name) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <!-- @TODO: Display validation error for 'class_id' -->
                </div>
            </div>
            
            <div class="row">
                <!-- Enrollment Date -->
                <div class="col-md-6 mb-3">
                    <label for="enrollment_date" class="form-label">Enrollment Date</label>
                    <input type="date" class="form-control" id="enrollment_date" name="enrollment_date"
                           value="<?= htmlspecialchars($enrollment->enrollment_date ? date('Y-m-d', strtotime($enrollment->enrollment_date)) : '') ?>">
                </div>

                <!-- Status Dropdown -->
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="enrolled" <?= ($enrollment->status === 'enrolled') ? 'selected' : '' ?>>Enrolled</option>
                        <option value="completed" <?= ($enrollment->status === 'completed') ? 'selected' : '' ?>>Completed</option>
                        <option value="dropped" <?= ($enrollment->status === 'dropped') ? 'selected' : '' ?>>Dropped</option>
                    </select>
                </div>
            </div>
            
            <!-- 
                Note on Academic Year:
                The academic year is tied to the selected Class and does not need to be a separate field here.
                ملاحظة على السنة الأكاديمية:
                السنة الأكاديمية مرتبطة بالفصل المختار ولا تحتاج إلى حقل منفصل هنا.
            -->

            <hr>

            <!-- 
                Form Action Buttons
                أزرار الإجراءات
            -->
            <div class="d-flex justify-content-end">
                <a href="/enrollments" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Enrollment</button>
            </div>
        </form>
    </div>
</div>