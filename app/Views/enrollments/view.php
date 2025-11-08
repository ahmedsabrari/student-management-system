<?php
/**
 * Enrollment View/Details Page
 *
 * Displays detailed information about a single student enrollment record.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $enrollment The enrollment object passed from EnrollmentController::view().
 * @var object $student The student associated with the enrollment.
 * @var object $class The class associated with the enrollment.
 * @var object $course The course associated with the class.
 * @var object $academicYear The academic year associated with the class.
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Enrollment Details #" . htmlspecialchars($enrollment->id);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-0">Enrollment Details</h2>
        <small class="text-muted">Record ID: <?= htmlspecialchars($enrollment->id) ?></small>
    </div>
    <a href="/enrollments" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Enrollment List
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Enrollment Information</h5>
        <div>
            <a href="/grades/manage/<?= htmlspecialchars($enrollment->id) ?>" class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i> Manage Grade/Attendance 
            </a> 
            <form action="/enrollments/delete/<?= htmlspecialchars($enrollment->id) ?>" method="POST" class="d-inline ms-1" onsubmit="return confirm('Are you sure you want to un-enroll this student?');">
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-times"></i> Un-enroll
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Student Name</dt>
            <dd class="col-sm-9">
                <a href="/students/<?= htmlspecialchars($student->id ?? '') ?>">
                    <?= htmlspecialchars($student->first_name . ' ' . $student->last_name ?? 'N/A') ?>
                </a> 
                (ID: <?= htmlspecialchars($student->student_number ?? 'N/A') ?>)
            </dd>

            <dt class="col-sm-3">Class Name</dt>
            <dd class="col-sm-9">
                 <a href="/classes/<?= htmlspecialchars($class->id ?? '') ?>">
                    <?= htmlspecialchars($class->name ?? 'N/A') ?>
                 </a>
            </dd>

            <dt class="col-sm-3">Course Name</dt>
            <dd class="col-sm-9">
                <a href="/courses/<?= htmlspecialchars($course->id ?? '') ?>">
                    <?= htmlspecialchars($course->name ?? 'N/A') ?>
                </a> 
                (<?= htmlspecialchars($course->code ?? 'N/A') ?>)
            </dd>

            <dt class="col-sm-3">Academic Year</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($academicYear->name ?? 'N/A') ?></dd>

            <dt class="col-sm-3">Enrollment Date</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($enrollment->enrollment_date ? date('F j, Y', strtotime($enrollment->enrollment_date)) : 'N/A') ?></dd>
            
            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9">
                <span class="badge 
                    <?= $enrollment->status === 'enrolled' ? 'bg-success' : ($enrollment->status === 'completed' ? 'bg-secondary' : 'bg-danger') ?>">
                    <?= ucfirst(htmlspecialchars($enrollment->status)) ?>
                </span>
            </dd>
        </dl>
    </div>
</div>