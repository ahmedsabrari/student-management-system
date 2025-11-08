<?php
/**
 * Courses Report View
 *
 * Displays a comprehensive and filterable report of all courses.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $reportData The main data for the report, passed from ReportController::courses().
 * Expected format for each row:
 * ['code', 'name', 'department_name', 'teacher_name', 'enrollment_count' (calculated), 'average_grade' (calculated)]
 * @var int $totalCourses (Calculated and passed by the controller).
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Courses Report";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Courses Report</h2>
    <div>
        <button class="btn btn-outline-danger disabled"><i class="fas fa-file-pdf"></i> Export PDF</button>
        <button class="btn btn-outline-success disabled"><i class="fas fa-file-excel"></i> Export Excel</button>
    </div>
</div>

<div class="row mb-3 align-items-center">
    <div class="col-md-4">
        <div class="card bg-light">
             <div class="card-body text-center py-2">
                 <h6 class="card-title text-muted mb-1">Total Courses Offered</h6>
                 <p class="card-text fs-4 fw-bold mb-0"><?= htmlspecialchars($totalCourses ?? count($reportData ?? [])) ?></p>
             </div>
        </div>
    </div>
    <div class="col-md-8">
         <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" id="courseSearch" class="form-control" placeholder="Search by course name, code, or department..." aria-label="Search Courses">
        </div>
    </div>
</div>


<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($reportData)): ?>
            <div class="alert alert-info">No course records found matching the criteria.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle" id="coursesReportTable">
                    <thead class="table-light">
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Department</th>
                            <th>Teacher Name</th>
                            <th>Enrolled Students</th> <th>Average Grade</th> </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportData as $course): ?>
                            <tr class="course-row" 
                                data-search-term="<?= htmlspecialchars(strtolower($course['code'] . ' ' . $course['name'] . ' ' . ($course['department_name'] ?? ''))) ?>">
                                <td><strong><?= htmlspecialchars($course['code'] ?? 'N/A') ?></strong></td>
                                <td><?= htmlspecialchars($course['name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($course['department_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($course['teacher_name'] ?? 'N/A') ?></td>
                                <td class="text-center">
                                    <?= htmlspecialchars($course['enrollment_count'] ?? 0) ?>
                                    </td>
                                <td class="text-center fw-bold">
                                    <?= htmlspecialchars(isset($course['average_grade']) ? number_format($course['average_grade'], 2) : '-') ?>
                                     </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
    </div>
</div>

<script>
    const searchInput = document.getElementById('courseSearch');
    const tableRows = document.querySelectorAll('#coursesReportTable tbody tr.course-row');

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = searchInput.value.toLowerCase().trim();

            tableRows.forEach(row => {
                const rowData = row.getAttribute('data-search-term');
                if (rowData.includes(searchTerm)) {
                    row.style.display = ''; // Show row
                } else {
                    row.style.display = 'none'; // Hide row
                }
            });
        });
    }
</script>