<?php
/**
 * Assignment Submissions View
 *
 * Displays a list of student submissions for a specific assignment.
 * Allows teachers to view, download (if applicable), and grade submissions.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $assignment The assignment object for which submissions are listed.
 * @var array $submissions An array of AssignmentSubmission objects (joined with student data).
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Submissions for: " . htmlspecialchars($assignment->title);
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-0">Student Submissions</h2>
        <small class="text-muted">Assignment: <?= htmlspecialchars($assignment->title) ?></small>
    </div>
    <div>
        <a href="/assignments/<?= htmlspecialchars($assignment->id) ?>" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left"></i> Back to Assignment
        </a>
        <!-- @TODO: Implement Export Functionality -->
        <!-- تنفيذ وظيفة التصدير -->
        <!-- <button class="btn btn-outline-success me-1"><i class="fas fa-file-csv"></i> Export CSV</button> -->
        <!-- <button class="btn btn-outline-danger"><i class="fas fa-file-pdf"></i> Export PDF</button> -->
    </div>
</div>

<!-- 
    Submissions Table Card
    بطاقة جدول التسليمات
-->
<div class="card shadow-sm">
    <div class="card-header">
        <!-- Search Bar -->
        <!-- شريط البحث -->
        <div class="input-group input-group-sm">
            <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
            <input type="text" id="submissionSearch" class="form-control" placeholder="Search by student name..." aria-label="Search Submissions" aria-describedby="basic-addon1">
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($submissions)): ?>
            <div class="alert alert-info">No submissions received for this assignment yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle" id="submissionsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Student Name</th>
                            <th>Submission Date</th>
                            <th>File</th>
                            <th>Grade</th>
                            <th>Feedback</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): ?>
                            <?php 
                                // Assume student data is pre-joined or fetched via relationship
                                // افتراض أن بيانات الطالب مدمجة مسبقًا أو تم جلبها عبر العلاقة
                                $student = $submission->student(); 
                            ?>
                            <tr class="submission-row" data-student-name="<?= htmlspecialchars(strtolower($student->first_name . ' ' . $student->last_name ?? '')) ?>">
                                <td><?= htmlspecialchars($student->first_name . ' ' . $student->last_name ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($submission->submitted_at ? date('Y-m-d H:i', strtotime($submission->submitted_at)) : 'N/A') ?></td>
                                <td>
                                    <?php if (!empty($submission->file_path)): ?>
                                        <!-- @TODO: Create a secure download route -->
                                        <!-- إنشاء مسار آمن للتحميل -->
                                        <a href="/download/submission/<?= $submission->id ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No file</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($submission->grade ?? 'Not Graded') ?> 
                                    <?= $submission->points_earned ? '/ ' . htmlspecialchars($assignment->max_points ?? '-') : '' ?>
                                </td>
                                <td>
                                    <?php 
                                        // Display truncated feedback
                                        // عرض جزء من التعليق
                                        $feedback = $submission->feedback ?? '';
                                        echo htmlspecialchars(substr($feedback, 0, 50)) . (strlen($feedback) > 50 ? '...' : ''); 
                                    ?>
                                </td>
                                <td>
                                     <span class="badge 
                                        <?= $submission->status === 'graded' ? 'bg-success' : ($submission->status === 'late' ? 'bg-warning' : 'bg-primary') ?>">
                                        <?= ucfirst(htmlspecialchars($submission->status ?? 'N/A')) ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Link to view/grade the specific submission -->
                                    <!-- رابط لعرض/تقييم التسليم المحدد -->
                                     <a href="/assignments/submissions/grade/<?= $submission->id ?>" class="btn btn-sm btn-primary" title="View/Grade Submission">
                                        <i class="fas fa-edit"></i> View / Grade
                                     </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- @TODO: Add Pagination if needed -->
            <!-- إضافة ترقيم الصفحات إذا لزم الأمر -->
        <?php endif; ?>
    </div>
</div>

<!-- Simple client-side search script -->
<!-- سكريبت بسيط للبحث من جانب العميل -->
<script>
    const searchInput = document.getElementById('submissionSearch');
    const tableRows = document.querySelectorAll('#submissionsTable tbody tr.submission-row');

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = searchInput.value.toLowerCase();

            tableRows.forEach(row => {
                const studentName = row.getAttribute('data-student-name');
                if (studentName.includes(searchTerm)) {
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