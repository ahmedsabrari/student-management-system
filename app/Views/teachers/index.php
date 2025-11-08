<?php
/**
 * Teacher Index View
 *
 * Displays a table listing all teachers in the system.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $teachers An array of teacher objects passed from TeacherController::index().
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Manage Teachers";

// @TODO: Enhance the controller query to join User and Department tables
// to avoid N+1 queries in the loop (fetching user->full_name, user->email, department->name).
// (لأداء أفضل، يجب تحسين الاستعلام في الـ Controller لربط الجداول وتجنب الاستعلامات المتكررة)
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Teacher List</h2>
    <a href="/teachers/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Teacher
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($teachers)): ?>
            <div class="alert alert-info">No teachers found in the system yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $teacher): ?>
                            <?php
                                // Fetch related data (Ideally JOIN in Controller)
                                // جلب البيانات المرتبطة (يفضل عمل JOIN في الـ Controller)
                                $user = $teacher->user(); 
                                $department = $teacher->department();
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($teacher->id) ?></td>
                                <td><?= htmlspecialchars($user->full_name ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($user->email ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($department->name ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($teacher->phone ?? 'N/A') ?></td>
                                <td>
                                    <a href="/teachers/<?= $teacher->id ?>" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="/teachers/<?= $teacher->id ?>/edit" class="btn btn-sm btn-warning" title="Edit Teacher">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    
                                    <form action="/teachers/<?= $teacher->id ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this teacher? This will also delete their user account.');">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Teacher">
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