<?php
/**
 * Department Index View
 *
 * Displays a table listing all academic departments in the system.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $departments An array of department objects passed from DepartmentController::index().
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Manage Departments";
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Department List</h2>
    <a href="/departments/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Department
    </a>
</div>

<!-- 
    Department Table
    جدول الأقسام
-->
<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($departments)): ?>
            <div class="alert alert-info">No departments found in the system yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#ID</th>
                            <th>Department Name</th>
                            <th>Description</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($departments as $department): ?>
                            <tr>
                                <td><?= htmlspecialchars($department->id) ?></td>
                                <td><strong><?= htmlspecialchars($department->name ?? 'N/A') ?></strong></td>
                                <td>
                                    <?php 
                                        // Truncate long descriptions for display
                                        // قص الوصف الطويل لغرض العرض
                                        $description = $department->description ?? 'No description.';
                                        echo htmlspecialchars(substr($description, 0, 70)) . (strlen($description) > 70 ? '...' : '');
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($department->created_at ? date('Y-m-d', strtotime($department->created_at)) : 'N/A') ?></td>
                                <td>
                                    <!-- Action Buttons -->
                                    <a href="/departments/<?= $department->id ?>" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="/departments/<?= $department->id ?>/edit" class="btn btn-sm btn-warning" title="Edit Department">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    
                                    <!-- Delete Button (using a form for POST request) -->
                                    <form action="/departments/<?= $department->id ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department? Associated teachers and courses will have their department set to NULL.');">
                                        <!-- @TODO: Add CSRF token input -->
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Department">
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