<?php
/**
 * Enrollment Index View
 *
 * Displays a table listing all student enrollments in various classes.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $enrollments An array of enrollment objects passed from EnrollmentController::index().
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Manage Enrollments";

// @TODO: Enhance the controller query to join Student, Class, and AcademicYear tables
// to avoid N+1 queries in the loop.
// (لأداء أفضل، يجب تحسين الاستعلام في الـ Controller لربط الجداول وتجنب الاستعلامات المتكررة)
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Enrollment List</h2>
    <a href="/enrollments/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Enrollment
    </a>
</div>

<!-- 
    Enrollment Table
    جدول التسجيلات
-->
<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($enrollments)): ?>
            <div class="alert alert-info">No enrollments found in the system yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#ID</th>
                            <th>Student Name</th>
                            <th>Class Name</th>
                            <th>Academic Year</th>
                            <th>Enrollment Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrollments as $enrollment): ?>
                            <?php
                                // Fetch related data (Ideally JOIN in Controller)
                                // جلب البيانات المرتبطة (يفضل عمل JOIN في الـ Controller)
                                $student = $enrollment->student(); 
                                $class = $enrollment->class();
                                $academicYear = $class ? $class->academicYear() : null;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($enrollment->id) ?></td>
                                <td><?= htmlspecialchars($student->first_name . ' ' . $student->last_name ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($class->name ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($academicYear->name ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($enrollment->enrollment_date ? date('Y-m-d', strtotime($enrollment->enrollment_date)) : 'N/A') ?></td>
                                <td>
                                    <!-- Display status with a Bootstrap badge -->
                                    <span class="badge 
                                        <?= $enrollment->status === 'enrolled' ? 'bg-success' : ($enrollment->status === 'completed' ? 'bg-secondary' : 'bg-danger') ?>">
                                        <?= ucfirst(htmlspecialchars($enrollment->status)) ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Action Buttons -->
                                    <a href="/enrollments/view/<?= $enrollment->id ?>" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    
                                    <!-- Edit might mean managing grades/attendance -->
                                    <a href="/grades/manage/<?= $enrollment->id ?>" class="btn btn-sm btn-outline-primary" title="Manage Grade">
                                        <i class="fas fa-graduation-cap"></i> Grade
                                    </a>
                                    
                                    <!-- Delete Button (using a form for POST request) -->
                                    <form action="/enrollments/delete/<?= $enrollment->id ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to un-enroll this student? This may affect their grades and attendance records.');">
                                        <!-- @TODO: Add CSRF token input -->
                                        <button type="submit" class="btn btn-sm btn-danger" title="Un-enroll Student">
                                            <i class="fas fa-times"></i> Un-enroll
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