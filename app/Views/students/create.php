<?php
/**
 * Student Create View
 *
 * Renders the form for adding a new student to the system.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $departments (Assumed to be passed from StudentController::create())
 * @var array $errors (Assumed to be passed from a Validator class on failure)
 * @var array $old_input (Assumed to be passed on validation failure to repopulate the form)
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Add New Student";
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Create Student Record</h2>
    <a href="/students" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Student List
    </a>
</div>

<!-- 
    New Student Form
    نموذج إضافة طالب جديد
-->
<div class="card shadow-sm">
    <div class="card-body">
        <form action="/students" method="POST" id="createStudentForm">
            
            <!-- 
                CSRF Token (Cross-Site Request Forgery Protection)
                حماية CSRF (حقل مخفي)
                @TODO: Replace with a dynamic CSRF token.
            -->
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">

            <!-- Form fields are organized in a grid system for responsiveness -->
            <div class="row">
                <!-- First Name -->
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                    <!-- @TODO: Display validation error for 'first_name' -->
                </div>

                <!-- Last Name -->
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                    <!-- @TODO: Display validation error for 'last_name' -->
                </div>
            </div>

            <div class="row">
                <!-- Student Number -->
                <div class="col-md-6 mb-3">
                    <label for="student_number" class="form-label">Student Number</label>
                    <input type="text" class="form-control" id="student_number" name="student_number" required>
                    <!-- @TODO: Display validation error for 'student_number' -->
                </div>

                <!-- Email Address -->
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <!-- @TODO: Display validation error for 'email' -->
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
                                <option value="<?= htmlspecialchars($department->id) ?>">
                                    <?= htmlspecialchars($department->name) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; */ ?>
                        <!-- Placeholder for demonstration -->
                        <option value="1">Computer Science</option>
                        <option value="2">Business Administration</option>
                    </select>
                    <!-- @TODO: Display validation error for 'department_id' -->
                </div>

                <!-- Date of Birth -->
                <div class="col-md-6 mb-3">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                </div>
            </div>
            
            <div class="row">
                <!-- Gender Dropdown -->
                <div class="col-md-6 mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Phone Number -->
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone">
                </div>
            </div>

            <!-- Address -->
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
            </div>

            <hr>

            <!-- 
                Form Action Buttons
                أزرار الإجراءات
            -->
            <div class="d-flex justify-content-end">
                <a href="/students" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Student</button>
            </div>
        </form>
    </div>
</div>