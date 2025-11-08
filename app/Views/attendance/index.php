<?php
/**
 * Attendance Index/List View
 *
 * Displays a filterable list of attendance records or provides links
 * to take attendance for specific classes.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $attendanceRecords An array of attendance objects (assumes data is pre-joined in controller).
 * @var array $classes (Assumed to be passed for the filter dropdown).
 * @var array $academic_years (Assumed to be passed for the filter dropdown).
 * @var array $filters An array of the currently active filters.
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Manage Attendance";

// @TODO: CRITICAL - The AttendanceController::index() should perform JOINs to fetch
// student name, class name, etc., efficiently, especially if displaying a full list.
// Alternatively, this page could just list classes, and clicking a class goes to its attendance records.
// (هام جداً: يجب على Controller عمل JOIN لجلب البيانات المرتبطة بكفاءة أو تغيير تصميم الصفحة لعرض الفصول أولاً)
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Attendance Records</h2>
    <a href="/attendance" class="btn btn-primary"> 
        <i class="fas fa-edit"></i> Select Class to Take Attendance
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Records</h5>
    </div>
    <div class="card-body">
        <form action="/attendance" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="class_id" class="form-label">Class</label>
                <select id="class_id" name="class_id" class="form-select">
                    <option value="">All Classes</option>
                    <?php if (!empty($classes)): ?>
                        <?php foreach($classes as $class): ?>
                            <option value="<?= htmlspecialchars($class->id) ?>" <?= (isset($filters['class_id']) && $filters['class_id'] == $class->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($class->name) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" id="date" name="date" class="form-control" value="<?= htmlspecialchars($filters['date'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="academic_year_id" class="form-label">Academic Year</label>
                <select id="academic_year_id" name="academic_year_id" class="form-select">
                     <option value="">All Years</option>
                    </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Apply Filter</button>
                <a href="/attendance" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($attendanceRecords)): ?>
            <div class="alert alert-info">No attendance records found matching the criteria. Please select a class and date to view records or take attendance.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student Number</th>
                            <th>Full Name</th>
                            <th>Class Name</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th> </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendanceRecords as $record): /* Assumes pre-joined data */ ?>
                            <tr>
                                <td><?= htmlspecialchars($record['student_number'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($record['full_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($record['class_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($record['date'] ?? 'N/A') ?></td>
                                <td>
                                    <?php $status = strtolower($record['status'] ?? ''); ?>
                                    <span class="badge 
                                        <?= $status === 'present' ? 'bg-success' : ($status === 'absent' ? 'bg-danger' : ($status === 'late' ? 'bg-warning' : 'bg-secondary')) ?>">
                                        <?= ucfirst(htmlspecialchars($record['status'] ?? 'N/A')) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/attendance/take/<?= htmlspecialchars($record['class_id'] ?? '') ?>?date=<?= htmlspecialchars($record['date'] ?? '') ?>" 
                                       class="btn btn-sm btn-warning" title="Edit Attendance for this Date">
                                        <i class="fas fa-edit"></i> Edit
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