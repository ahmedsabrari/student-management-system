<?php
/**
 * Teacher Profile View
 *
 * Displays the detailed profile page for a teacher.
 * This view is rendered within a main layout (e.g., 'admin.php').
 *
 * @var object $teacher The teacher object passed from the controller.
 * @var object $user The associated user object.
 * @var object $department The associated department object.
 * @var array $classes (Assumed to be passed, classes taught by the teacher)
 * @var array $courses (Assumed to be passed, courses assigned to the teacher)
 */

// Set the dynamic title for the layout
// (This $title variable will be used by the layout file)
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Teacher Profile: " . htmlspecialchars($user->full_name ?? '');

// Assume Font Awesome is included in the main layout
?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-2 text-center mb-3 mb-md-0">
                <img src="<?= htmlspecialchars($user->avatar ?? '/assets/images/default-avatar.png') ?>" 
                     alt="Teacher Avatar" 
                     class="img-fluid rounded-circle" 
                     style="width: 100px; height: 100px; object-fit: cover;">
            </div>
            <div class="col-md-7">
                <h2 class="mb-0"><?= htmlspecialchars($user->full_name ?? 'N/A') ?></h2>
                <p class="text-muted mb-1">
                    Specialization: <?= htmlspecialchars($teacher->specialization ?? 'N/A') ?>
                </p>
                <p class="text-muted mb-0"><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($user->email ?? 'N/A') ?></p>
                 <p class="text-muted mb-0"><i class="fas fa-phone me-2"></i><?= htmlspecialchars($teacher->phone ?? 'N/A') ?></p>
            </div>
            <div class="col-md-3 text-md-end mt-3 mt-md-0">
                <a href="/teachers" class="btn btn-outline-secondary btn-sm mb-1 w-100">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
                <a href="/teachers/<?= htmlspecialchars($teacher->id) ?>/edit" class="btn btn-warning btn-sm mb-1 w-100"> 
                    <i class="fas fa-edit me-1"></i> Edit Profile
                </a>
                </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Professional Details</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Full Name</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($user->full_name ?? 'N/A') ?></dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($user->email ?? 'N/A') ?></dd>
                    
                    <dt class="col-sm-4">Department</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($department->name ?? 'N/A') ?></dd>

                    <dt class="col-sm-4">Specialization</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($teacher->specialization ?? 'N/A') ?></dd>
                </dl>
            </div>
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Hire Date</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($teacher->hire_date ?? 'N/A') ?></dd>

                    <dt class="col-sm-4">Phone</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($teacher->phone ?? 'N/A') ?></dd>
                    
                    <dt class="col-sm-4">Address</dt>
                    <dd class="col-sm-8"><?= nl2br(htmlspecialchars($teacher->address ?? 'N/A')) ?></dd>
                </dl>
            </div>
        </div>
        <hr>
        <strong>Qualifications:</strong>
        <p class="mt-2 text-muted"><?= nl2br(htmlspecialchars($teacher->qualifications ?? 'No qualifications listed.')) ?></p>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Teaching Assignments</h5>
    </div>
    <div class="card-body">
         <?php if (empty($classes) && empty($courses)): ?>
            <p class="text-muted">This teacher is not currently assigned to any classes or courses.</p>
         <?php else: ?>
             <p class="text-muted">Assigned classes and courses information will be displayed here.</p>
         <?php endif; ?>
    </div>
</div>