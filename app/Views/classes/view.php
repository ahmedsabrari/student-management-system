<?php
/**
 * Class View/Details Page
 *
 * Displays detailed information about a single class, including its schedule
 * and enrolled students.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $class The class object passed from ClassController::show().
 * @var array $students (Assumed to be passed, representing enrolled students)
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Class Details: " . htmlspecialchars($class->name);

// Fetch related data using relationships (consider optimizing in Controller)
// جلب البيانات المرتبطة باستخدام العلاقات (يفضل تحسينها في الـ Controller)
$course = $class->course();
$teacher = $class->teacher();
$teacherUser = $teacher ? $teacher->user() : null; // Get user data for teacher name
$academicYear = $class->academicYear();

// @TODO: Fetch enrolled students for this class efficiently in the Controller
// جلب الطلاب المسجلين لهذا الفصل بكفاءة في الـ Controller
// $students = $class->students(); // Example relationship call
$students = []; // Placeholder

?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-0"><?= htmlspecialchars($class->name) ?></h2>
        <small class="text-muted">Course: <?= htmlspecialchars($course->name ?? 'N/A') ?></small>
    </div>
    <div>
        <a href="/classes" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Class List
        </a>
        <a href="/classes/<?= htmlspecialchars($class->id) ?>/edit" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Class
        </a>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chalkboard me-2"></i>Class Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Class Name</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($class->name ?? 'N/A') ?></dd>

                    <dt class="col-sm-4">Course</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($course->name ?? 'N/A') ?> (<?= htmlspecialchars($course->code ?? 'N/A') ?>)</dd>

                    <dt class="col-sm-4">Teacher</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($teacherUser->full_name ?? 'Not Assigned') ?></dd>
                </dl>
            </div>
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Academic Year</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($academicYear->name ?? 'N/A') ?></dd>

                    <dt class="col-sm-4">Semester</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($class->semester ?? 'N/A') ?></dd>

                    <dt class="col-sm-4">Max Students</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($class->max_students ?? 'N/A') ?></dd>
                </dl>
            </div>
        </div>
        
        <hr>
        <strong>Schedule Notes:</strong>
        <p class="mt-2 text-muted"><?= nl2br(htmlspecialchars($class->schedule ?? 'No schedule notes provided.')) ?></p>
        
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Enrolled Students</h5>
    </div>
    <div class="card-body">
        <?php if (empty($students)): ?>
            <p class="text-muted">No students are currently enrolled in this class.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Student Number</th>
                            <th>Full Name</th>
                            <th>Department</th> <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): /* Assuming $students is array of Student objects */ ?>
                            <tr>
                                <td><?= htmlspecialchars($student->student_number ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($student->first_name . ' ' . $student->last_name) ?></td>
                                <td>
                                    <?php 
                                        // Placeholder - Student's department name
                                        // نص مؤقت - اسم قسم الطالب
                                        echo 'Department Placeholder'; 
                                    ?>
                                </td>
                                <td>
                                    <span class="badge 
                                        <?= $student->status === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ucfirst(htmlspecialchars($student->status ?? 'N/A')) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
