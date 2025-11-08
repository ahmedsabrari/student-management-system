<?php
/**
 * Admin/Teacher Dashboard View
 *
 * This file is injected into the 'admin.php' layout.
 * It displays high-level statistics and quick access links for administrators.
 *
 * @var int $studentCount (Passed from DashboardController)
 * @var int $teacherCount (Passed from DashboardController)
 * @var int $courseCount  (Passed from DashboardController)
 * @var array $recentActivity (Assumed to be passed, e.g., from Log model)
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Admin Dashboard";
?>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Total Students</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?= htmlspecialchars($studentCount ?? 0) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">
                            Total Teachers</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?= htmlspecialchars($teacherCount ?? 0) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">
                            Total Courses</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?= htmlspecialchars($courseCount ?? 0) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                            Pending Fees (Example)</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">$10,500</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">

    <div class="col-lg-7 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">System Overview</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myOverviewChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Quick Access</h6>
            </div>
            <div class="card-body">
                <p>Quickly add new records to the system.</p>
                <a href="/students/create" class="btn btn-primary btn-icon-split mb-2">
                    <span class="text">Add New Student</span>
                </a>
                <a href="/teachers/create" class="btn btn-success btn-icon-split mb-2">
                    <span class="text">Add New Teacher</span>
                </a>
                <a href="/courses/create" class="btn btn-info btn-icon-split mb-2">
                    <span class="text">Add New Course</span>
                </a>
                <hr>
                <p class="mt-2">View system reports:</p>
                <a href="/reports/students" class="btn btn-secondary btn-sm">Student Reports</a>
                <a href="/reports/financial" class="btn btn-secondary btn-sm">Financial Reports</a>
            </div>
        </div>
    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Wait for the DOM to be ready
    document.addEventListener("DOMContentLoaded", function() {
        // Get the canvas element
        const ctx = document.getElementById('myOverviewChart').getContext('2d');
        
        // Create the chart
        const myChart = new Chart(ctx, {
            type: 'bar', // Type of chart (bar, line, pie)
            data: {
                labels: ['Students', 'Teachers', 'Courses'],
                datasets: [{
                    label: '# of Records',
                    // Data is injected directly from PHP variables
                    data: [
                        <?= (int)($studentCount ?? 0) ?>, 
                        <?= (int)($teacherCount ?? 0) ?>, 
                        <?= (int)($courseCount ?? 0) ?>
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(255, 159, 64, 0.5)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>