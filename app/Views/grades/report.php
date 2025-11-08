<?php
/**
 * Grades Report View
 *
 * Displays a filterable report of all student grades.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $reportData The main data for the report, passed from the controller.
 * @var array $students (Assumed to be passed for the filter dropdown).
 * @var array $courses (Assumed to be passed for the filter dropdown).
 * @var array $academic_years (Assumed to be passed for the filter dropdown).
 * @var array $filters An array of the currently active filters.
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Grades Report";
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Grades Report</h2>
    <div>
        <!-- @TODO: Implement print and export functionality -->
        <!-- <button class="btn btn-outline-secondary" onclick="window.print();"><i class="fas fa-print"></i> Print</button> -->
        <!-- <button class="btn btn-outline-success"><i class="fas fa-file-csv"></i> Export CSV</button> -->
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
        <form action="/grades/report" method="GET" class="row g-3 align-items-end">
            <!-- Student Filter -->
            <div class="col-md-3">
                <label for="student_id" class="form-label">Student</label>
                <select id="student_id" name="student_id" class="form-select">
                    <option value="">All Students</option>
                    <!-- @TODO: Populate from $students variable -->
                </select>
            </div>
            <!-- Course Filter -->
            <div class="col-md-3">
                <label for="course_id" class="form-label">Course</label>
                <select id="course_id" name="course_id" class="form-select">
                     <option value="">All Courses</option>
                    <!-- @TODO: Populate from $courses variable -->
                </select>
            </div>
            <!-- Academic Year Filter -->
            <div class="col-md-3">
                <label for="academic_year_id" class="form-label">Academic Year</label>
                <select id="academic_year_id" name="academic_year_id" class="form-select">
                     <option value="">All Years</option>
                    <!-- @TODO: Populate from $academic_years variable -->
                </select>
            </div>
            <!-- Action Buttons -->
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Apply Filter</button>
                <a href="/grades/report" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>


<!-- 
    Grades Report Table
    جدول تقرير الدرجات
-->
<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($reportData)): ?>
            <div class="alert alert-info">No grade records found matching the criteria.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student Number</th>
                            <th>Full Name</th>
                            <th>Course Name</th>
                            <th>Grade</th>
                            <th>Semester</th>
                            <th>Academic Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportData as $row): /* Assuming $reportData is a pre-joined array */ ?>
                            <tr>
                                <td><?= htmlspecialchars($row['student_number'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['full_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['course_name'] ?? 'N/A') ?></td>
                                <td><strong><?= htmlspecialchars($row['grade'] ?? 'N/A') ?></strong></td>
                                <td><?= htmlspecialchars($row['semester'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['academic_year'] ?? 'N/A') ?></td>
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