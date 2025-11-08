<?php
/**
 * Grade Manage View
 *
 * Renders the form for adding or editing a grade for a specific enrollment.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $enrollment The specific enrollment record.
 * @var object|null $grade The existing grade record for this enrollment, if any.
 * @var object $student The student associated with the enrollment.
 * @var object $class The class associated with the enrollment.
 * @var object $course The course associated with the class.
 * @var array $errors (Assumed from a Validator class on failure)
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Manage Grade for " . htmlspecialchars($student->first_name . ' ' . $student->last_name);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Manage Grade</h2>
    <a href="/grades" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Grades List
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0">Enrollment Details</h5>
    </div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Student</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($student->first_name . ' ' . $student->last_name) ?> (<?= htmlspecialchars($student->student_number) ?>)</dd>

            <dt class="col-sm-3">Course</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($course->name ?? 'N/A') ?></dd>

            <dt class="col-sm-3">Class</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($class->name ?? 'N/A') ?></dd>
            
            <dt class="col-sm-3">Enrollment Status</dt>
            <dd class="col-sm-9">
                <span class="badge <?= $enrollment->status === 'enrolled' ? 'bg-success' : 'bg-secondary' ?>">
                    <?= ucfirst(htmlspecialchars($enrollment->status)) ?>
                </span>
            </dd>
        </dl>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
         <h5 class="mb-0"><?= $grade ? 'Edit Grade' : 'Add Grade' ?></h5>
    </div>
    <div class="card-body">
        <form action="/grades/store/<?= htmlspecialchars($enrollment->id) ?>" method="POST" id="manageGradeForm">
            
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="grade" class="form-label">Grade (e.g., A+, B, C-)</label>
                    <input type="text" class="form-control" id="grade" name="grade" 
                           value="<?= htmlspecialchars($grade->grade ?? '') ?>" 
                           placeholder="Enter letter grade">
                    </div>

                <div class="col-md-6 mb-3">
                    <label for="grade_points" class="form-label">Grade Points (e.g., 4.00, 3.33)</label>
                    <input type="number" step="0.01" min="0" max="4.00" class="form-control" id="grade_points" name="grade_points"
                           value="<?= htmlspecialchars($grade->grade_points ?? '') ?>"
                           placeholder="Enter numeric points">
                     </div>
            </div>

            <div class="mb-3">
                <label for="remarks" class="form-label">Remarks / Comments</label>
                <textarea class="form-control" id="remarks" name="remarks" rows="3" 
                          placeholder="Optional comments..."><?= htmlspecialchars($grade->remarks ?? '') ?></textarea>
            </div>
            
            <hr>

            <div class="d-flex justify-content-end">
                 <a href="/grades" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Grade</button>
            </div>
        </form>
    </div>
</div>