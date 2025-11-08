<?php
/**
 * Class Index View
 *
 * Displays a table listing all classes in the system.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $classes An array of class objects passed from ClassController::index().
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Manage Classes";

// @TODO: Enhance the controller query to join Course, Teacher, and AcademicYear tables
// to avoid N+1 queries in the loop and get student counts efficiently.
// (لأداء أفضل، يجب تحسين الاستعلام في الـ Controller لربط الجداول وتجنب الاستعلامات المتكررة)
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Class List</h2>
    <a href="/classes/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Class
    </a>
</div>

<!-- 
    Class Table
    جدول الفصول الدراسية
-->
<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($classes)): ?>
            <div class="alert alert-info">No classes found in the system yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Class Name</th>
                            <th>Course</th>
                            <th>Teacher</th>
                            <th>Students</th> <!-- عدد الطلاب -->
                            <th>Academic Year</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $class): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($class->name ?? 'N/A') ?></strong></td>
                                <td>
                                    <?php 
                                        // Placeholder - This data should be joined in the controller.
                                        // نص مؤقت - هذه البيانات يجب جلبها في الـ Controller.
                                        echo 'Course Name Placeholder'; 
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        // Placeholder - This data should be joined in the controller.
                                        // نص مؤقت - هذه البيانات يجب جلبها في الـ Controller.
                                        echo 'Teacher Name Placeholder';
                                    ?>
                                </td>
                                <td>
                                    <!-- Placeholder for student count -->
                                    <span class="badge bg-secondary">0 / <?= htmlspecialchars($class->max_students ?? 'N/A') ?></span>
                                </td>
                                <td>
                                     <?php 
                                        // Placeholder - This data should be joined in the controller.
                                        // نص مؤقت - هذه البيانات يجب جلبها في الـ Controller.
                                        echo htmlspecialchars($class->semester . ' ' . $class->year);
                                    ?>
                                </td>
                                <td>
                                    <!-- Action Buttons -->
                                    <a href="/classes/<?= $class->id ?>" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="/classes/<?= $class->id ?>/edit" class="btn btn-sm btn-warning" title="Edit Class">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    
                                    <!-- Delete Button (using a form for POST request) -->
                                    <form action="/classes/<?= $class->id ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this class? This may affect enrollments and grades.');">
                                        <!-- @TODO: Add CSRF token input -->
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Class">
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