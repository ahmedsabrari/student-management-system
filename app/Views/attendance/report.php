<?php
/**
 * Attendance Report View
 *
 * Displays a detailed and filterable report of student attendance,
 * including percentages and visual progress bars.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $reportData The main data for the report, passed from the controller.
 * Expected format for each row:
 * ['student_number', 'full_name', 'department_name', 'total_days', 'present', 'absent', 'late', 'percentage']
 * @var array $classes (Assumed to be passed for the filter dropdown).
 * @var array $filters An array of the currently active filters.
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Attendance Report";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Attendance Report</h2>
    <div>
        <a href="/reports/attendance/pdf?<?= http_build_query($filters ?? []) ?>" class="btn btn-outline-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Download PDF
        </a>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Report</h5>
    </div>
    <div class="card-body">
        <form action="/reports/attendance" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
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
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" id="date_from" name="date_from" class="form-control" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" id="date_to" name="date_to" class="form-control" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="/reports/attendance" class="btn btn-secondary w-100 mt-1">Reset</a>
            </div>
        </form>
    </div>
</div>


<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($reportData)): ?>
            <div class="alert alert-info">No attendance records found matching the criteria.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student Number</th>
                            <th>Full Name</th>
                            <th>Department</th>
                            <th>Total Days</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Late</th>
                            <th>Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportData as $row): /* Assumes controller provides pre-calculated data */ ?>
                            <?php 
                                $percentage = round($row['percentage'] ?? 0);
                                $progressBarColor = 'bg-danger'; // Default to red
                                if ($percentage >= 85) {
                                    $progressBarColor = 'bg-success'; // Green for high attendance
                                } elseif ($percentage >= 60) {
                                    $progressBarColor = 'bg-warning'; // Yellow for medium
                                }
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['student_number'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['full_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['department_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['total_days'] ?? 0) ?></td>
                                <td class="text-success"><?= htmlspecialchars($row['present'] ?? 0) ?></td>
                                <td class="text-danger"><?= htmlspecialchars($row['absent'] ?? 0) ?></td>
                                <td class="text-warning"><?= htmlspecialchars($row['late'] ?? 0) ?></td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar <?= $progressBarColor ?>" role="progressbar" 
                                             style="width: <?= $percentage ?>%;" 
                                             aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100">
                                             <?= $percentage ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
    </div>
</div>