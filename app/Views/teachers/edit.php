<?php
/**
 * Teacher Edit View
 *
 * Renders the form for editing an existing teacher's record.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $teacher The teacher object passed from TeacherController::edit().
 * @var object $user The associated user object passed from TeacherController::edit().
 * @var array $departments An array of department objects.
 * @var array $errors (Assumed from Validator)
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Edit Teacher: " . htmlspecialchars($user->full_name ?? '');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Edit Teacher Record</h2>
    <a href="/teachers" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Teacher List
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="/teachers/<?= htmlspecialchars($teacher->id) ?>/update" method="POST" id="editTeacherForm">
            
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">

            <h5 class="card-title mb-3">User Account Details</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required 
                           value="<?= htmlspecialchars($user->full_name ?? '') ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required
                           value="<?= htmlspecialchars($user->email ?? '') ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" 
                           value="<?= htmlspecialchars($user->username ?? '') ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" 
                           aria-describedby="passwordHelp" disabled>
                    <div id="passwordHelp" class="form-text">Password cannot be edited here. Use the reset password feature.</div>
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
                                <option value="<?= htmlspecialchars($department->id) ?>"
                                    <?= ($teacher->department_id == $department->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($department->name) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone"
                           value="<?= htmlspecialchars($teacher->phone ?? '') ?>">
                </div>
            </div>

             <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="hire_date" class="form-label">Hire Date</label>
                    <input type="date" class="form-control" id="hire_date" name="hire_date"
                           value="<?= htmlspecialchars($teacher->hire_date ?? '') ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="specialization" class="form-label">Specialization</label>
                    <input type="text" class="form-control" id="specialization" name="specialization" 
                           value="<?= htmlspecialchars($teacher->specialization ?? '') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($teacher->address ?? '') ?></textarea>
            </div>

             <div class="mb-3">
                <label for="qualifications" class="form-label">Qualifications</label>
                <textarea class="form-control" id="qualifications" name="qualifications" rows="3"><?= htmlspecialchars($teacher->qualifications ?? '') ?></textarea>
            </div>


            <hr>

            <div class="d-flex justify-content-end">
                <a href="/teachers" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Teacher</button>
            </div>
        </form>
    </div>
</div>