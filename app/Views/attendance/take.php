<?php
/**
 * Attendance Taking View
 *
 * Renders the form for taking attendance for a specific class on a specific date.
 * Displays a list of enrolled students with options to mark their status.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $class The class object for which attendance is being taken.
 * @var array $enrollments An array of enrollment objects (joined with student data).
 * @var string $attendance_date The date for which attendance is being taken (YYYY-MM-DD).
 * @var array $attendance_statuses (Assumed to be passed) Associative array [enrollment_id => status] for the given date.
 * @var array $errors (Assumed from Validator)
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Take Attendance for " . htmlspecialchars($class->name);

// Determine the date to use (passed via query string or default to today)
// تحديد التاريخ المستخدم (من الرابط أو الافتراضي هو اليوم)
$current_date = $_GET['date'] ?? date('Y-m-d'); 
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2>Take Attendance</h2>
        <h5 class="text-muted"><?= htmlspecialchars($class->name) ?></h5>
    </div>
    <a href="/attendance" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Class Selection
    </a>
</div>

<!-- 
    Attendance Form
    نموذج تسجيل الحضور
-->
<div class="card shadow-sm">
    <div class="card-body">
        <!-- Form submits to the store method for this specific class -->
        <!-- النموذج يُرسل إلى دالة store الخاصة بهذا الفصل المحدد -->
        <form action="/attendance/store/<?= htmlspecialchars($class->id) ?>" method="POST" id="takeAttendanceForm">
            
            <!-- 
                CSRF Token (Cross-Site Request Forgery Protection)
                حماية CSRF (حقل مخفي)
                @TODO: Replace with a dynamic CSRF token.
            -->
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">

            <!-- Date Selection -->
            <div class="row mb-3 align-items-end">
                <div class="col-md-4">
                    <label for="attendance_date" class="form-label">Attendance Date</label>
                    <input type="date" class="form-control" id="attendance_date" name="attendance_date" value="<?= htmlspecialchars($current_date) ?>" required>
                </div>
                <div class="col-md-8">
                     <!-- Button to change date - reloads the page with the new date -->
                     <!-- زر لتغيير التاريخ - يعيد تحميل الصفحة بالتاريخ الجديد -->
                    <button type="button" class="btn btn-outline-primary" onclick="window.location.href='/attendance/take/<?= $class->id ?>?date=' + document.getElementById('attendance_date').value">
                        Load Attendance for Date
                    </button>
                    <small class="form-text text-muted ms-2">Select a date and click 'Load' to view/edit previous records.</small>
                </div>
            </div>

            <hr>

            <?php if (empty($enrollments)): ?>
                <div class="alert alert-warning">There are no students enrolled in this class.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Student Number</th>
                                <th>Full Name</th>
                                <th>Status</th>
                                <th>Notes (Optional)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enrollments as $enrollment): ?>
                                <?php
                                    // Determine the current status for this student on this date
                                    // @TODO: The controller should pass an array $attendance_statuses[$enrollment->id] = 'status'
                                    // تحديد الحالة الحالية لهذا الطالب في هذا التاريخ (يجب أن يوفرها الـ Controller)
                                    $current_status = $attendance_statuses[$enrollment->id] ?? 'present'; // Default to 'present'
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($enrollment->student_number ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($enrollment->first_name . ' ' . $enrollment->last_name) ?></td>
                                    <td>
                                        <!-- 
                                            Radio buttons for attendance status.
                                            Name format: attendance[enrollment_id] 
                                            This sends an array like ['attendance' => [15 => 'present', 16 => 'absent']] to the controller.
                                            
                                            أزرار الراديو لحالة الحضور.
                                            صيغة الاسم ترسل مصفوفة إلى الـ Controller.
                                        -->
                                        <div class="btn-group" role="group" aria-label="Attendance Status for <?= htmlspecialchars($enrollment->id) ?>">
                                            <input type="radio" class="btn-check" name="attendance[<?= $enrollment->id ?>]" id="present_<?= $enrollment->id ?>" value="present" <?= ($current_status === 'present') ? 'checked' : '' ?> autocomplete="off">
                                            <label class="btn btn-outline-success btn-sm" for="present_<?= $enrollment->id ?>">Present</label>

                                            <input type="radio" class="btn-check" name="attendance[<?= $enrollment->id ?>]" id="absent_<?= $enrollment->id ?>" value="absent" <?= ($current_status === 'absent') ? 'checked' : '' ?> autocomplete="off">
                                            <label class="btn btn-outline-danger btn-sm" for="absent_<?= $enrollment->id ?>">Absent</label>

                                            <input type="radio" class="btn-check" name="attendance[<?= $enrollment->id ?>]" id="late_<?= $enrollment->id ?>" value="late" <?= ($current_status === 'late') ? 'checked' : '' ?> autocomplete="off">
                                            <label class="btn btn-outline-warning btn-sm" for="late_<?= $enrollment->id ?>">Late</label>
                                            
                                            <!-- Optional: Excused status -->
                                            <!-- <input type="radio" class="btn-check" name="attendance[<?= $enrollment->id ?>]" id="excused_<?= $enrollment->id ?>" value="excused" <?= ($current_status === 'excused') ? 'checked' : '' ?> autocomplete="off"> -->
                                            <!-- <label class="btn btn-outline-secondary btn-sm" for="excused_<?= $enrollment->id ?>">Excused</label> -->
                                        </div>
                                    </td>
                                    <td>
                                        <!-- 
                                            Optional notes field for each student.
                                            Name format: notes[enrollment_id]
                                            حقل ملاحظات اختياري لكل طالب.
                                        -->
                                         <input type="text" class="form-control form-control-sm" name="notes[<?= $enrollment->id ?>]" 
                                                placeholder="Optional note..." 
                                                value="<?= htmlspecialchars($attendance_notes[$enrollment->id] ?? '') ?>"> 
                                         <!-- @TODO: Pass $attendance_notes array from controller -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <hr>

            <!-- 
                Form Action Buttons
                أزرار الإجراءات
            -->
            <div class="d-flex justify-content-end">
                <a href="/attendance" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary" <?= empty($enrollments) ? 'disabled' : '' ?>>
                    <i class="fas fa-save"></i> Save Attendance
                </button>
            </div>
        </form>
    </div>
</div>