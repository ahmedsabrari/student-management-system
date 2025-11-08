<?php
/**
 * Department View/Details Page
 *
 * Displays detailed information about a single academic department,
 * including its associated teachers and courses.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $department The department object passed from DepartmentController::show().
 * @var array $teachers An array of teacher objects associated with this department.
 * @var array $courses An array of course objects associated with this department.
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Department Details: " . htmlspecialchars($department->name);
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-0"><?= htmlspecialchars($department->name) ?></h2>
        <small class="text-muted">Department ID: <?= htmlspecialchars($department->id) ?></small>
    </div>
    <a href="/departments" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Department List
    </a>
</div>

<!-- 
    Department Details Card
    بطاقة تفاصيل القسم
-->
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Department Information</h5>
        <div>
            <a href="/departments/<?= htmlspecialchars($department->id) ?>/edit" class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <!-- Delete Button Form -->
            <form action="/departments/<?= htmlspecialchars($department->id) ?>/delete" method="POST" class="d-inline ms-1" onsubmit="return confirm('Are you sure you want to delete this department?');">
                <!-- @TODO: Add CSRF token input -->
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Department ID</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($department->id) ?></dd>

            <dt class="col-sm-3">Department Name</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($department->name ?? 'N/A') ?></dd>

            <dt class="col-sm-3">Date Created</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($department->created_at ? date('F j, Y, g:i a', strtotime($department->created_at)) : 'N/A') ?></dd>
            
            <!-- Note: 'updated_at' is not in the schema, so it's omitted -->
            <!-- ملاحظة: حقل 'updated_at' غير موجود في تصميم قاعدة البيانات، لذا تم حذفه -->

            <dt class="col-sm-3">Description</dt>
            <dd class="col-sm-9"><?= nl2br(htmlspecialchars($department->description ?? 'No description provided.')) ?></dd>
        </dl>
    </div>
</div>

<!-- 
    Associated Teachers Section
    قسم المعلمين المرتبطين
-->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Teachers in this Department</h5>
    </div>
    <div class="card-body">
        <?php if (empty($teachers)): ?>
            <p class="text-muted">There are no teachers assigned to this department yet.</p>
        <?php else: ?>
            <ul class="list-group list-group-flush">
                <?php foreach ($teachers as $teacher): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php $user = $teacher->user(); ?>
                        <?= htmlspecialchars($user->full_name ?? 'N/A') ?>
                        <a href="/teachers/<?= $teacher->id ?>" class="btn btn-sm btn-outline-secondary">View Profile</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<!-- 
    Associated Courses Section
    قسم المقررات المرتبطة
-->
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-book me-2"></i>Courses in this Department</h5>
    </div>
    <div class="card-body">
        <?php if (empty($courses)): ?>
            <p class="text-muted">There are no courses assigned to this department yet.</p>
        <?php else: ?>
             <ul class="list-group list-group-flush">
                <?php foreach ($courses as $course): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($course->name ?? 'N/A') ?> (<?= htmlspecialchars($course->code ?? 'N/A') ?>)
                        <a href="/courses/<?= $course->id ?>" class="btn btn-sm btn-outline-secondary">View Details</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<!-- @TODO: Ensure Font Awesome is included in the main layout for icons -->
<!-- تأكد من تضمين Font Awesome في القالب الرئيسي لعرض الأيقونات -->