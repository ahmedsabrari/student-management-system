<?php
/**
 * Student Index View
 *
 * Displays a table listing all students in the system.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $students An array of student objects passed from StudentController::index().
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Manage Students";

// @TODO: Add Department data to the $students array in the Controller for better performance
// بدلاً من استدعاء ->department() هنا، الأفضل جلب اسم القسم في الـ Controller
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Student List</h2>
    <a href="/students/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Student
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($students)): ?>
            <div class="alert alert-info">No students found in the system yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Student Number</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Department</th> <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student->id) ?></td>
                                <td><?= htmlspecialchars($student->student_number ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($student->first_name . ' ' . $student->last_name) ?></td>
                                <td><?= htmlspecialchars($student->email ?? 'N/A') ?></td>
                                <td>
                                    <?php 
                                        // Ideally, fetch department name in Controller query
                                        // $department = $student->department(); // Calling relationship method (less ideal in view)
                                        // echo htmlspecialchars($department->name ?? 'N/A'); 
                                        echo 'Department Name Placeholder'; // Placeholder
                                    ?>
                                </td>
                                <td>
                                    <span class="badge 
                                        <?= $student->status === 'active' ? 'bg-success' : ($student->status === 'graduated' ? 'bg-secondary' : 'bg-danger') ?>">
                                        <?= ucfirst(htmlspecialchars($student->status)) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/students/<?= $student->id ?>" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="/students/<?= $student->id ?>/edit" class="btn btn-sm btn-warning" title="Edit Student">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    
                                    <form action="/students/<?= $student->id ?>/delete" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Student">
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