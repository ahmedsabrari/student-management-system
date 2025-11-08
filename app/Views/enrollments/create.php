<?php
/**
 * Enrollment Create View
 *
 * Renders the form for enrolling a student into a class.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $students An array of student objects passed from EnrollmentController::create().
 * @var array $classes An array of class objects.
 * @var array $errors (Assumed from a Validator class on failure)
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Create New Enrollment";
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Enroll Student in a Class</h2>
    <a href="/enrollments" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Enrollment List
    </a>
</div>

<!-- 
    New Enrollment Form
    نموذج إضافة تسجيل جديد
-->
<div class="card shadow-sm">
    <div class="card-body">
        <form action="/enrollments" method="POST" id="createEnrollmentForm">
            
            <!-- 
                CSRF Token (Cross-Site Request Forgery Protection)
                حماية CSRF (حقل مخفي)
                @TODO: Replace with a dynamic CSRF token.
            -->
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">

            <div class="row">
                <!-- Student Dropdown -->
                <div class="col-md-6 mb-3">
                    <label for="student_id" class="form-label">Student</label>
                    <select class="form-select" id="student_id" name="student_id" required>
                        <option value="" disabled selected>Select a Student...</option>
                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= htmlspecialchars($student->id) ?>">
                                    <?= htmlspecialchars($student->first_name . ' ' . $student->last_name) ?> (ID: <?= htmlspecialchars($student->student_number) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <!-- @TODO: Display validation error for 'student_id' -->
                </div>

                <!-- Class Dropdown -->
                <div class="col-md-6 mb-3">
                    <label for="class_id" class="form-label">Class</label>
                    <select class="form-select" id="class_id" name="class_id" required>
                        <option value="" disabled selected>Select a Class...</option>
                         <?php if (!empty($classes)): ?>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= htmlspecialchars($class->id) ?>">
                                    <?= htmlspecialchars($class->name) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <!-- @TODO: Display validation error for 'class_id' -->
                </div>
            </div>
            
            <!-- 
                Note on other fields:
                - Enrollment Date is set automatically by the controller upon submission.
                - Status defaults to 'enrolled' automatically in the controller.
                - Academic Year is implicitly defined by the chosen Class.
                
                ملاحظة على الحقول الأخرى:
                - تاريخ التسجيل يتم تعيينه تلقائيًا بواسطة الـ Controller عند الإرسال.
                - الحالة تكون 'enrolled' بشكل افتراضي في الـ Controller.
                - السنة الأكاديمية يتم تحديدها ضمنيًا من خلال الفصل المختار.
            -->

            <hr>

            <!-- 
                Form Action Buttons
                أزرار الإجراءات
            -->
            <div class="d-flex justify-content-end">
                <a href="/enrollments" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Enroll Student</button>
            </div>
        </form>
    </div>
</div>