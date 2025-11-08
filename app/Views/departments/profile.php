<?php
/**
 * Department Profile View
 *
 * Displays a comprehensive profile for a single academic department,
 * including statistics like student count.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $department The department object passed from the controller.
 * @var array $teachers An array of teacher objects in this department.
 * @var array $courses An array of course objects in this department.
 * @var array $students An array of student objects in this department.
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Department Profile: " . htmlspecialchars($department->name);

// Calculate student count from the passed array
// حساب عدد الطلاب من المصفوفة التي تم تمريرها
$studentCount = count($students ?? []);
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
    Main Profile Card
    بطاقة الملف الشخصي الرئيسية
-->
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Department Overview</h5>
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
        <div class="row">
            <!-- Left Column: Details -->
            <div class="col-md-8">
                <dl class="row">
                    <dt class="col-sm-3">Department Name</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($department->name ?? 'N/A') ?></dd>

                    <dt class="col-sm-3">Date Created</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($department->created_at ? date('F j, Y', strtotime($department->created_at)) : 'N/A') ?></dd>

                    <!-- 'updated_at' is not in the schema, so it's noted here -->
                    <!-- حقل 'updated_at' غير موجود في تصميم قاعدة البيانات -->
                    <dt class="col-sm-3">Last Modified</dt>
                    <dd class="col-sm-9"><em class="text-muted">N/A</em></dd>
                    
                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9"><?= nl2br(htmlspecialchars($department->description ?? 'No description provided.')) ?></dd>
                </dl>
            </div>
            <!-- Right Column: Statistics -->
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="card-title text-uppercase text-muted">Total Students</h6>
                        <p class="card-text display-4 fw-bold"><?= $studentCount ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 
    Associated Teachers Section
    قسم المعلمين المرتبطين
-->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Teachers</h5>
    </div>
    <div class="card-body">
        <?php if (empty($teachers)): ?>
            <p class="text-muted">No teachers are assigned to this department.</p>
        <?php else: ?>
            <ul class="list-group list-group-flush">
                <?php foreach ($teachers as $teacher): ?>
                    <li class="list-group-item">
                        <a href="/teachers/<?= $teacher->id ?>"><?= htmlspecialchars($teacher->user()->full_name ?? 'N/A') ?></a>
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
        <h5 class="mb-0"><i class="fas fa-book me-2"></i>Courses</h5>
    </div>
    <div class="card-body">
        <?php if (empty($courses)): ?>
            <p class="text-muted">No courses are assigned to this department.</p>
        <?php else: ?>
             <ul class="list-group list-group-flush">
                <?php foreach ($courses as $course): ?>
                    <li class="list-group-item">
                        <a href="/courses/<?= $course->id ?>"><?= htmlspecialchars($course->name ?? 'N/A') ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<!-- @TODO: Ensure Font Awesome is included in the main layout for icons -->
<!-- تأكد من تضمين Font Awesome في القالب الرئيسي لعرض الأيقونات -->