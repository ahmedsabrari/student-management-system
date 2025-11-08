<?php
/**
 * Assignment Index View
 *
 * Displays a table listing all assignments created in the system.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $assignments An array of assignment objects passed from AssignmentController::index().
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Manage Assignments";

// @TODO: Enhance the controller query to join Class and Course tables
// to avoid N+1 queries in the loop (fetching class->name, class->course()->name).
// (لأداء أفضل، يجب تحسين الاستعلام في الـ Controller لربط الجداول وتجنب الاستعلامات المتكررة)
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Assignment List</h2>
    <a href="/assignments/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create New Assignment
    </a>
</div>

<!-- 
    Assignments Table
    جدول الواجبات
-->
<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($assignments)): ?>
            <div class="alert alert-info">No assignments found in the system yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Assignment Title</th>
                            <th>Course</th>
                            <th>Class</th>
                            <th>Due Date</th>
                            <th>Max Points</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignments as $assignment): ?>
                            <?php
                                // PLACEHOLDER: Fetching related data - MUST BE OPTIMIZED IN CONTROLLER
                                // مؤقت: جلب البيانات المرتبطة - يجب تحسينه في الـ CONTROLLER
                                $class = $assignment->class();
                                $course = $class ? $class->course() : null;
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($assignment->title ?? 'N/A') ?></strong></td>
                                <td><?= htmlspecialchars($course->name ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($class->name ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($assignment->due_date ? date('Y-m-d', strtotime($assignment->due_date)) : 'N/A') ?></td>
                                <td><?= htmlspecialchars($assignment->max_points ?? 'N/A') ?></td>
                                <td>
                                    <!-- Action Buttons -->
                                    <a href="/assignments/<?= $assignment->id ?>" class="btn btn-sm btn-info" title="View Details & Submissions">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="/assignments/<?= $assignment->id ?>/edit" class="btn btn-sm btn-warning" title="Edit Assignment">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    
                                    <!-- Delete Button (using a form for POST request) -->
                                    <form action="/assignments/<?= $assignment->id ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this assignment? This will also delete all student submissions.');">
                                        <!-- @TODO: Add CSRF token input -->
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Assignment">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- @TODO: Add Pagination for large datasets -->
            <!-- إضافة ترقيم الصفحات لمجموعات البيانات الكبيرة -->
        <?php endif; ?>
    </div>
</div>

<!-- @TODO: Ensure Font Awesome is included in the main layout for icons -->
<!-- تأكد من تضمين Font Awesome في القالب الرئيسي لعرض الأيقونات -->