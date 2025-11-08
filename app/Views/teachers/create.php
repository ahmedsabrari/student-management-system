<?php
/**
 * Teacher Create View
 *
 * Renders the form for adding a new teacher to the system.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $departments An array of department objects passed from TeacherController::create().
 * @var array $errors (Assumed to be passed from a Validator class on failure)
 * @var array $old_input (Assumed for repopulating form on error)
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Add New Teacher";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Create New Teacher Record</h2>
    <a href="/teachers" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Teacher List
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="/teachers" method="POST" id="createTeacherForm">
            
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">

            <h5 class="card-title mb-3">User Account Details</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Optional, defaults to email">
                     </div>

                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    </div>
            </div>

            <hr class="my-4">
            <h5 class="card-title mb-3">Teacher Profile Details</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select class="form-select" id="department_id" name="department_id">
                        <option value="">Select a Department...</option>
                        <?php if (!empty($departments)): ?>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= htmlspecialchars($department->id) ?>">
                                    <?= htmlspecialchars($department->name) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="1">Computer Science</option>
                            <option value="2">Business Administration</option>
                        <?php endif; ?>
                    </select>
                     </div>

                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone">
                </div>
            </div>

             <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="hire_date" class="form-label">Hire Date</label>
                    <input type="date" class="form-control" id="hire_date" name="hire_date">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="specialization" class="form-label">Specialization</label>
                    <input type="text" class="form-control" id="specialization" name="specialization" placeholder="e.g., Web Development, Database Management">
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="2"></textarea>
            </div>

             <div class="mb-3">
                <label for="qualifications" class="form-label">Qualifications</label>
                <textarea class="form-control" id="qualifications" name="qualifications" rows="3" placeholder="Enter qualifications, certifications, etc."></textarea>
            </div>


            <hr>

            <div class="d-flex justify-content-end">
                <a href="/teachers" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Teacher</button>
            </div>
        </form>
    </div>
</div>