<?php
/**
 * Course Index View
 *
 * Displays a table listing all courses in the system.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $courses An array of course objects passed from CourseController::index().
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Manage Courses";

// @TODO: Add Department and Teacher data to the $courses array in the Controller 
// for better performance, avoiding N+1 queries in the loop.
// (لأداء أفضل، يجب جلب أسماء الأقسام والمدرسين في الـ Controller بدلاً من استدعائهم هنا)
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Course List</h2>
    <a href="/courses/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Course
    </a>
</div>

<!-- 
    Course Table
    جدول المقررات
-->
<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($courses)): ?>
            <div class="alert alert-info">No courses found in the system yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Department</th>
                            <th>Assigned Teacher</th>
                            <th>Credits</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($course->code ?? 'N/A') ?></strong></td>
                                <td><?= htmlspecialchars($course->name ?? 'N/A') ?></td>
                                <td>
                                    <?php 
                                        // Placeholder - This should be joined in the controller.
                                        // نص مؤقت - يجب جلب اسم القسم في الـ Controller.
                                        echo 'Department Name Placeholder'; 
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        // Placeholder - This should be joined in the controller.
                                        // نص مؤقت - يجب جلب اسم المدرس في الـ Controller.
                                        echo 'Teacher Name Placeholder';
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($course->credit_hours ?? 'N/A') ?></td>
                                <td>
                                    <!-- Action Buttons -->
                                    <a href="/courses/<?= $course->id ?>" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="/courses/<?= $course->id ?>/edit" class="btn btn-sm btn-warning" title="Edit Course">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    
                                    <!-- Delete Button (using a form for POST request) -->
                                    <form action="/courses/<?= $course->id ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course? This might affect existing classes and enrollments.');">
                                        <!-- @TODO: Add CSRF token input -->
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Course">
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