<?php
/**
 * Course View/Details Page
 *
 * Displays detailed information about a single course, including enrolled students.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $course The course object passed from CourseController::show().
 * @var array $students (Assumed to be passed, representing enrolled students)
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Course Details: " . htmlspecialchars($course->name);

// Fetch related data using relationships (consider optimizing in Controller)
// جلب البيانات المرتبطة باستخدام العلاقات (يفضل تحسينها في الـ Controller)
$department = $course->department();
$teacher = $course->teacher();
$teacherUser = $teacher ? $teacher->user() : null; // Get user data for teacher name

// @TODO: Fetch enrolled students for this course (might involve joining classes and enrollments)
// $students = $course->getEnrolledStudents(); // Example hypothetical method
$students = []; // Placeholder

?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-0"><?= htmlspecialchars($course->name) ?></h2>
        <small class="text-muted">Course Code: <?= htmlspecialchars($course->code ?? 'N/A') ?></small>
    </div>
    <a href="/courses" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Courses List
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-book-open me-2"></i>Course Information</h5>
        <div>
            <a href="/courses/<?= htmlspecialchars($course->id) ?>/edit" class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i> Edit Course
            </a>
            <form action="/courses/<?= htmlspecialchars($course->id) ?>/delete" method="POST" class="d-inline ms-1" onsubmit="return confirm('Are you sure you want to delete this course? This might affect existing classes and enrollments.');">
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i> Delete Course
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Course Code</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($course->code ?? 'N/A') ?></dd>

                    <dt class="col-sm-4">Course Name</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($course->name ?? 'N/A') ?></dd>

                    <dt class="col-sm-4">Department</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($department->name ?? 'N/A') ?></dd>
                </dl>
            </div>
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Teacher</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($teacherUser->full_name ?? 'Not Assigned') ?></dd>

                    <dt class="col-sm-4">Credits</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($course->credit_hours ?? 'N/A') ?></dd>
                    
                    <dt class="col-sm-4">Date Created</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($course->created_at ? date('Y-m-d H:i', strtotime($course->created_at)) : 'N/A') ?></dd>
                </dl>
            </div>
        </div>
        
        <hr>
        <strong>Description:</strong>
        <p class="mt-2"><?= nl2br(htmlspecialchars($course->description ?? 'No description provided.')) ?></p>
        
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Enrolled Students</h5>
    </div>
    <div class="card-body">
        <?php if (empty($students)): ?>
            <p class="text-muted">No students are currently enrolled in this course.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Enrollment Status</th> </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $enrollment): /* Assuming $students is actually enrollment data */ ?>
                            <tr>
                                <td><?= htmlspecialchars($enrollment->student->first_name . ' ' . $enrollment->student->last_name ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($enrollment->student->email ?? 'N/A') ?></td>
                                <td>
                                    <span class="badge <?= $enrollment->status === 'enrolled' ? 'bg-info' : 'bg-secondary' ?>">
                                        <?= ucfirst(htmlspecialchars($enrollment->status ?? 'N/A')) ?>
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