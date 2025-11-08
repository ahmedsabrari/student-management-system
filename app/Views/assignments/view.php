<?php
/**
 * Assignment View/Details Page
 *
 * Displays detailed information about a single assignment, including a list
 * of student submissions for grading.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $assignment The assignment object passed from AssignmentController::show().
 * @var array $submissions An array of AssignmentSubmission objects associated with this assignment.
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Assignment Details: " . htmlspecialchars($assignment->title);

// Fetch related data using relationships (consider optimizing in Controller)
// جلب البيانات المرتبطة باستخدام العلاقات (يفضل تحسينها في الـ Controller)
$class = $assignment->class();
$course = $class ? $class->course() : null;

// @TODO: Fetch submissions joined with student data efficiently in the Controller
// جلب التسليمات مع بيانات الطلاب بكفاءة في الـ Controller
// $submissions = $assignment->submissions(); // Example relationship call
$submissions = []; // Placeholder

?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-0"><?= htmlspecialchars($assignment->title) ?></h2>
        <small class="text-muted">For Class: <?= htmlspecialchars($class->name ?? 'N/A') ?> (<?= htmlspecialchars($course->name ?? 'N/A') ?>)</small>
    </div>
    <div>
        <a href="/assignments" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Assignments List
        </a>
        <a href="/assignments/<?= htmlspecialchars($assignment->id) ?>/edit" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Assignment
        </a>
    </div>
</div>

<!-- 
    Assignment Details Card
    بطاقة تفاصيل الواجب
-->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Assignment Information</h5>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Title</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($assignment->title ?? 'N/A') ?></dd>

            <dt class="col-sm-3">Class</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($class->name ?? 'N/A') ?></dd>

            <dt class="col-sm-3">Course</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($course->name ?? 'N/A') ?></dd>

            <dt class="col-sm-3">Due Date</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($assignment->due_date ? date('F j, Y', strtotime($assignment->due_date)) : 'Not set') ?></dd>

            <dt class="col-sm-3">Max Points</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($assignment->max_points ?? 'N/A') ?></dd>

            <dt class="col-sm-3">Description</dt>
            <dd class="col-sm-9"><?= nl2br(htmlspecialchars($assignment->description ?? 'No description provided.')) ?></dd>

            <!-- @TODO: Add logic to display file link if a file was attached -->
            <!-- إضافة منطق لعرض رابط الملف إذا تم إرفاق ملف -->
            <dt class="col-sm-3">Attached File</dt>
            <dd class="col-sm-9"><em class="text-muted">None</em></dd> 
            <!-- Example: <a href="/download/assignment/<?= $assignment->id ?>" target="_blank">Download File</a> -->
        </dl>
    </div>
</div>

<!-- 
    Student Submissions Section
    قسم تسليمات الطلاب
-->
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Student Submissions</h5>
    </div>
    <div class="card-body">
        <?php if (empty($submissions)): ?>
            <p class="text-muted">No students have submitted this assignment yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student Name</th>
                            <th>Submission Date</th>
                            <th>Grade / Mark</th>
                            <th>Status</th>
                            <th>Actions</th> <!-- View / Grade -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): /* Assumes submissions include student info */ ?>
                            <?php $student = $submission->student(); // Fetch student ?>
                            <tr>
                                <td><?= htmlspecialchars($student->first_name . ' ' . $student->last_name ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($submission->submitted_at ? date('Y-m-d H:i', strtotime($submission->submitted_at)) : 'N/A') ?></td>
                                <td>
                                    <?= htmlspecialchars($submission->grade ?? '-') ?> / <?= htmlspecialchars($assignment->max_points ?? '-') ?>
                                    <?= $submission->points_earned ? '(' . htmlspecialchars($submission->points_earned) . ' pts)' : '' ?>
                                </td>
                                <td>
                                     <span class="badge 
                                        <?= $submission->status === 'graded' ? 'bg-success' : ($submission->status === 'late' ? 'bg-warning' : 'bg-primary') ?>">
                                        <?= ucfirst(htmlspecialchars($submission->status ?? 'N/A')) ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Link to view/grade the specific submission -->
                                    <!-- رابط لعرض/تقييم التسليم المحدد -->
                                     <a href="/assignments/submissions/<?= $submission->id ?>" class="btn btn-sm btn-outline-primary" title="View/Grade Submission">
                                        <i class="fas fa-edit"></i> View / Grade
                                     </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- @TODO: Ensure Font Awesome is included in the main layout for icons -->
<!-- تأكد من تضمين Font Awesome في القالب الرئيسي لعرض الأيقونات -->