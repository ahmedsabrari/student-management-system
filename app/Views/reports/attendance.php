<?php
/**
 * Attendance Report View (Detailed)
 *
 * Displays a detailed and filterable report of student attendance across classes,
 * including percentages and a summary chart.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $reportData The main data for the report, passed from ReportController::attendance().
 * Expected format for each row:
 * ['student_number', 'full_name', 'class_name', 'total_days', 'present', 'absent', 'late', 'percentage']
 * @var array $classes (Assumed for filter dropdown).
 * @var array $filters An array of the currently active filters.
 * @var array $chartData (Optional, prepared data for Chart.js)
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Attendance Report";

// Prepare data for Chart.js (Example - Controller should ideally provide this)
// تحضير بيانات الرسم البياني (مثال - يفضل أن يوفرها الـ Controller)
$chartLabels = ['Present', 'Absent', 'Late'];
$chartCounts = [0, 0, 0];
if (!empty($reportData)) {
    foreach ($reportData as $row) {
        $chartCounts[0] += $row['present'] ?? 0;
        $chartCounts[1] += $row['absent'] ?? 0;
        $chartCounts[2] += $row['late'] ?? 0;
    }
}
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Attendance Report</h2>
    <div>
        <!-- @TODO: Implement Export Functionality -->
        <!-- تنفيذ وظيفة التصدير -->
        <a href="/reports/attendance/pdf?<?= http_build_query($filters ?? []) ?>" class="btn btn-outline-danger disabled" target="_blank">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>
</div>

<!-- 
    Filter Form Card
    بطاقة نموذج الفلترة
-->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Report</h5>
    </div>
    <div class="card-body">
        <form action="/reports/attendance" method="GET" class="row g-3 align-items-end">
            <!-- Class Filter -->
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
            <!-- Date Range Filter -->
            <div class="col-md-3">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" id="date_from" name="date_from" class="form-control" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" id="date_to" name="date_to" class="form-control" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
            </div>
            <!-- Action Buttons -->
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="/reports/attendance" class="btn btn-secondary w-100 mt-1">Reset</a>
            </div>
        </form>
    </div>
</div>


<!-- 
    Report Summary & Chart Row
    صف ملخص التقرير والرسم البياني
-->
<div class="row mb-4">
    <!-- Chart Card -->
    <div class="col-md-5">
         <div class="card shadow-sm h-100">
            <div class="card-header">
                <h6 class="mb-0">Overall Attendance Status</h6>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                 <?php if (array_sum($chartCounts) > 0): ?>
                    <canvas id="attendanceChart" style="max-height: 250px;"></canvas>
                 <?php else: ?>
                    <p class="text-muted">No data available for chart.</p>
                 <?php endif; ?>
            </div>
        </div>
    </div>
     <!-- Key Stats Card -->
    <div class="col-md-7">
        <div class="card shadow-sm h-100">
             <div class="card-header">
                <h6 class="mb-0">Report Summary</h6>
            </div>
            <div class="card-body">
                <!-- @TODO: Add more detailed summary stats if needed -->
                <!-- إضافة إحصائيات ملخصة أكثر تفصيلاً إذا لزم الأمر -->
                <p>This report shows attendance records based on the selected filters.</p>
                <span class="badge bg-success me-2">Present: <?= $chartCounts[0] ?></span>
                <span class="badge bg-danger me-2">Absent: <?= $chartCounts[1] ?></span>
                <span class="badge bg-warning text-dark">Late: <?= $chartCounts[2] ?></span>
            </div>
        </div>
    </div>
</div>


<!-- 
    Attendance Report Table
    جدول تقرير الحضور
-->
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list-check me-2"></i>Detailed Records</h5>
    </div>
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
                            <th>Class</th> <!-- اسم الفصل -->
                            <th>Total Days</th> <!-- إجمالي الأيام المسجلة -->
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Late</th>
                            <th>Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportData as $row): ?>
                            <?php 
                                $percentage = round($row['percentage'] ?? 0);
                                $progressBarColor = 'bg-danger'; // Default red
                                if ($percentage >= 85) $progressBarColor = 'bg-success';
                                elseif ($percentage >= 60) $progressBarColor = 'bg-warning';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['student_number'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['full_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['class_name'] ?? 'N/A') ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['total_days'] ?? 0) ?></td>
                                <td class="text-center text-success fw-bold"><?= htmlspecialchars($row['present'] ?? 0) ?></td>
                                <td class="text-center text-danger fw-bold"><?= htmlspecialchars($row['absent'] ?? 0) ?></td>
                                <td class="text-center text-warning fw-bold"><?= htmlspecialchars($row['late'] ?? 0) ?></td>
                                <td>
                                    <!-- Progress Bar -->
                                    <div class="progress" style="height: 20px; font-size: 0.75rem;">
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
            <!-- @TODO: Add Pagination -->
            <!-- إضافة ترقيم الصفحات -->
        <?php endif; ?>
    </div>
</div>

<!-- Chart.js Script -->
<!-- سكريبت الرسم البياني -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('attendanceChart');
        if (ctx && <?= array_sum($chartCounts) ?> > 0) { // Only render if canvas exists and data is available
            const attendanceChart = new Chart(ctx.getContext('2d'), {
                type: 'doughnut', // Doughnut chart is good for percentages
                data: {
                    labels: <?= json_encode($chartLabels) ?>,
                    datasets: [{
                        label: 'Attendance Status',
                        data: <?= json_encode($chartCounts) ?>,
                        backgroundColor: [
                            'rgba(25, 135, 84, 0.7)',  // Success (Present)
                            'rgba(220, 53, 69, 0.7)',  // Danger (Absent)
                            'rgba(255, 193, 7, 0.7)',   // Warning (Late)
                        ],
                        borderColor: [
                            'rgba(25, 135, 84, 1)',
                            'rgba(220, 53, 69, 1)',
                            'rgba(255, 193, 7, 1)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                             callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed;
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

<!-- @TODO: Ensure Font Awesome is included -->
<!-- تأكد من تضمين Font Awesome -->