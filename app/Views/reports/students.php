<?php
/**
 * Students Report View
 *
 * Displays a comprehensive and filterable report of all students.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $reportData The main data for the report, passed from ReportController::students().
 * Expected format for each row:
 * ['student_number', 'full_name', 'department_name', 'status', 'total_courses' (calculated), 'gpa' (calculated)]
 * @var array $departments (Assumed for search filter, though search is client-side for now).
 * @var int $totalActiveStudents (Calculated and passed by the controller).
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Students Report";
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Students Report</h2>
    <div>
        <!-- @TODO: Implement Export Functionality -->
        <!-- تنفيذ وظيفة التصدير -->
        <button class="btn btn-outline-danger disabled"><i class="fas fa-file-pdf"></i> Export PDF</button>
        <button class="btn btn-outline-success disabled"><i class="fas fa-file-excel"></i> Export Excel</button>
    </div>
</div>

<!-- 
    Statistics & Search Bar Row
    صف الإحصائيات وشريط البحث
-->
<div class="row mb-3 align-items-center">
    <!-- Student Counter -->
    <div class="col-md-4">
        <div class="card bg-light">
             <div class="card-body text-center py-2">
                 <h6 class="card-title text-muted mb-1">Total Active Students</h6>
                 <p class="card-text fs-4 fw-bold mb-0"><?= htmlspecialchars($totalActiveStudents ?? count($reportData ?? [])) ?></p>
                 <!-- Count only active students if $totalActiveStudents is provided -->
                 <!-- يتم عد الطلاب النشطين فقط إذا تم توفير $totalActiveStudents -->
             </div>
        </div>
    </div>
    <!-- Search Bar -->
    <div class="col-md-8">
         <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" id="studentSearch" class="form-control" placeholder="Search by name, number, or department..." aria-label="Search Students">
        </div>
    </div>
</div>


<!-- 
    Students Report Table
    جدول تقرير الطلاب
-->
<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($reportData)): ?>
            <div class="alert alert-info">No student records found matching the criteria.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle" id="studentsReportTable">
                    <thead class="table-light">
                        <tr>
                            <th>Student Number</th>
                            <th>Full Name</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Total Courses</th> <!-- عدد المقررات -->
                            <th>GPA</th> <!-- المعدل التراكمي -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportData as $student): ?>
                            <tr class="student-row" 
                                data-search-term="<?= htmlspecialchars(strtolower($student['student_number'] . ' ' . $student['full_name'] . ' ' . $student['department_name'])) ?>">
                                <td><?= htmlspecialchars($student['student_number'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($student['full_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($student['department_name'] ?? 'N/A') ?></td>
                                <td>
                                    <!-- Display status with a Bootstrap badge -->
                                    <?php $status = strtolower($student['status'] ?? ''); ?>
                                    <span class="badge 
                                        <?= $status === 'active' ? 'bg-success' : ($status === 'graduated' ? 'bg-secondary' : ($status === 'suspended' ? 'bg-warning text-dark' : ($status === 'dropped' ? 'bg-danger' : 'bg-light text-dark'))) ?>">
                                        <?= ucfirst(htmlspecialchars($student['status'] ?? 'N/A')) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?= htmlspecialchars($student['total_courses'] ?? '-') ?>
                                    <!-- This needs calculation in the Controller -->
                                    <!-- هذا يتطلب حسابًا في الـ Controller -->
                                </td>
                                <td class="text-center fw-bold">
                                    <?= htmlspecialchars(isset($student['gpa']) ? number_format($student['gpa'], 2) : '-') ?>
                                     <!-- This needs calculation in the Controller -->
                                     <!-- هذا يتطلب حسابًا في الـ Controller -->
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

<!-- Simple client-side search script -->
<!-- سكريبت بسيط للبحث من جانب العميل -->
<script>
    const searchInput = document.getElementById('studentSearch');
    const tableRows = document.querySelectorAll('#studentsReportTable tbody tr.student-row');

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

<!-- @TODO: Ensure Font Awesome is included in the main layout for icons -->
<!-- تأكد من تضمين Font Awesome في القالب الرئيسي لعرض الأيقونات -->