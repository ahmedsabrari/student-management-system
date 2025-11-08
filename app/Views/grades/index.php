<?php
/**
 * Grade Index View
 *
 * Displays a table listing all recorded grades for students across different courses.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $grades An array of grade objects passed from GradeController::index().
 * (Assumes controller has joined related data for efficiency).
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Manage Grades";

// @TODO: CRITICAL - The GradeController::index() MUST perform JOINs to fetch
// student name, course name, semester, and academic year efficiently.
// Calling relationships inside the loop below will cause severe N+1 query problems.
// (هام جداً: يجب على Controller عمل JOIN لجلب البيانات المرتبطة بكفاءة لتجنب مشاكل N+1)
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Grades List</h2>
    <div>
        <a href="/enrollments" class="btn btn-primary me-2">
            <i class="fas fa-plus"></i> Add/Manage Grade (Select Enrollment)
        </a>
        </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($grades)): ?>
            <div class="alert alert-info">No grades have been recorded yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student Number</th>
                            <th>Full Name</th>
                            <th>Course</th>
                            <th>Grade</th>
                            <th>Semester</th>
                            <th>Academic Year</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grades as $grade): ?>
                            <?php
                                // PLACEHOLDER: Fetching related data - MUST BE OPTIMIZED IN CONTROLLER
                                // مؤقت: جلب البيانات المرتبطة - يجب تحسينه في الـ CONTROLLER
                                $enrollment = $grade->enrollment();
                                $student = $enrollment ? $enrollment->student() : null;
                                $class = $enrollment ? $enrollment->class() : null;
                                $course = $class ? $class->course() : null;
                                $academicYear = $class ? $class->academicYear() : null;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($student->student_number ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($student ? ($student->first_name . ' ' . $student->last_name) : 'N/A') ?></td>
                                <td><?= htmlspecialchars($course->name ?? 'N/A') ?></td>
                                <td><strong><?= htmlspecialchars($grade->grade ?? 'N/A') ?></strong> (<?= htmlspecialchars($grade->grade_points ?? '-') ?> pts)</td>
                                <td><?= htmlspecialchars($class->semester ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($academicYear->name ?? 'N/A') ?></td>
                                <td>
                                    <a href="/enrollments/view/<?= $enrollment->id ?>" class="btn btn-sm btn-info" title="View Enrollment Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="/grades/manage/<?= $enrollment->id ?>" class="btn btn-sm btn-warning" title="Edit Grade">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    
                                    <form action="/grades/delete/<?= $grade->id ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this grade record?');">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Grade">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
    </div>
</div>