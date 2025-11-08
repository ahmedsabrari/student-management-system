<?php
/**
 * Student Edit View
 *
 * Renders the form for editing an existing student's record.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $student The student object passed from StudentController::edit().
 * @var array $departments (Assumed to be passed from StudentController::edit())
 * @var array $errors (Assumed to be passed from a Validator class on failure)
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Edit Student: " . htmlspecialchars($student->first_name . ' ' . $student->last_name);
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Edit Student Record</h2>
    <a href="/students" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Student List
    </a>
</div>

<!-- 
    Edit Student Form
    نموذج تعديل بيانات الطالب
-->
<div class="card shadow-sm">
    <div class="card-body">
        <!-- The form action points to the update route for this specific student -->
        <!-- مسار النموذج يشير إلى رابط التحديث الخاص بهذا الطالب المحدد -->
        <form action="/students/<?= htmlspecialchars($student->id) ?>/update" method="POST" id="editStudentForm">
            
            <!-- 
                CSRF Token (Cross-Site Request Forgery Protection)
                حماية CSRF (حقل مخفي)
                @TODO: Replace with a dynamic CSRF token.
            -->
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">
            
            <!-- 
                Method Spoofing for PUT/PATCH if you use them. We use POST as defined in routes.
                يمكن استخدام هذا لتحديد نوع الطلب، لكننا نستخدم POST كما هو محدد في المسارات.
            -->
            <!-- <input type="hidden" name="_method" value="PUT"> -->

            <div class="row">
                <!-- First Name -->
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required 
                           value="<?= htmlspecialchars($student->first_name ?? '') ?>">
                </div>

                <!-- Last Name -->
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required
                           value="<?= htmlspecialchars($student->last_name ?? '') ?>">
                </div>
            </div>

            <div class="row">
                <!-- Student Number -->
                <div class="col-md-6 mb-3">
                    <label for="student_number" class="form-label">Student Number</label>
                    <input type="text" class="form-control" id="student_number" name="student_number" required
                           value="<?= htmlspecialchars($student->student_number ?? '') ?>">
                </div>

                <!-- Email Address -->
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required
                           value="<?= htmlspecialchars($student->email ?? '') ?>">
                </div>
            </div>

            <div class="row">
                <!-- Department Dropdown -->
                <div class="col-md-6 mb-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select class="form-select" id="department_id" name="department_id">
                        <option value="">Select a Department...</option>
                        <?php /* if (!empty($departments)): ?>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= htmlspecialchars($department->id) ?>" 
                                    <?= ($student->department_id == $department->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($department->name) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; */ ?>
                        <!-- Placeholder for demonstration -->
                        <option value="1" <?= ($student->department_id == 1) ? 'selected' : '' ?>>Computer Science</option>
                        <option value="2" <?= ($student->department_id == 2) ? 'selected' : '' ?>>Business Administration</option>
                    </select>
                </div>

                <!-- Date of Birth -->
                <div class="col-md-6 mb-3">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                           value="<?= htmlspecialchars($student->date_of_birth ?? '') ?>">
                </div>
            </div>
            
            <div class="row">
                <!-- Gender Dropdown -->
                <div class="col-md-6 mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="male" <?= ($student->gender === 'male') ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= ($student->gender === 'female') ? 'selected' : '' ?>>Female</option>
                        <option value="other" <?= ($student->gender === 'other') ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>

                <!-- Phone Number -->
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone"
                           value="<?= htmlspecialchars($student->phone ?? '') ?>">
                </div>
            </div>
            
            <!-- Address -->
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($student->address ?? '') ?></textarea>
            </div>

            <hr>

            <!-- 
                Form Action Buttons
                أزرار الإجراءات
            -->
            <div class="d-flex justify-content-end">
                <a href="/students/<?= htmlspecialchars($student->id) ?>" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Student</button>
            </div>
        </form>
    </div>
</div>