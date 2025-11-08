<?php
/**
 * Student View/Profile Page
 *
 * Displays detailed information about a single student.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $student The student object passed from StudentController::show().
 * @var array $guardians (Assumed to be passed in the future)
 * @var array $enrollments (Assumed to be passed in the future)
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Student Profile: " . htmlspecialchars($student->first_name . ' ' . $student->last_name);
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-0"><?= htmlspecialchars($student->first_name . ' ' . $student->last_name) ?></h2>
        <small class="text-muted">Student ID: <?= htmlspecialchars($student->student_number ?? 'N/A') ?></small>
    </div>
    <div>
        <a href="/students" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        <a href="/students/<?= htmlspecialchars($student->id) ?>/edit" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Profile
        </a>
    </div>
</div>

<!-- 
    Main Content Grid
    شبكة المحتوى الرئيسية
-->
<div class="row">

    <!-- Left Column: Main Profile Information -->
    <!-- العمود الأيسر: معلومات الملف الشخصي الرئيسية -->
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Student Details</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <!-- Full Name -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Full Name</strong>
                        <span><?= htmlspecialchars($student->first_name . ' ' . $student->last_name) ?></span>
                    </div>

                    <!-- Email -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Email Address</strong>
                        <span><?= htmlspecialchars($student->email ?? 'N/A') ?></span>
                    </div>

                    <!-- Phone -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Phone Number</strong>
                        <span><?= htmlspecialchars($student->phone ?? 'N/A') ?></span>
                    </div>

                    <!-- Address -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Address</strong>
                        <span><?= htmlspecialchars($student->address ?? 'N/A') ?></span>
                    </div>

                    <!-- Gender -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Gender</strong>
                        <span><?= ucfirst(htmlspecialchars($student->gender ?? 'N/A')) ?></span>
                    </div>

                    <!-- Date of Birth -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Date of Birth</strong>
                        <span><?= htmlspecialchars($student->date_of_birth ?? 'N/A') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Academic & Status Information -->
    <!-- العمود الأيمن: المعلومات الأكاديمية والحالة -->
    <div class="col-lg-4">
        <!-- Academic Info Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Academic Information</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <!-- Student Number -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Student Number</strong>
                        <span><?= htmlspecialchars($student->student_number ?? 'N/A') ?></span>
                    </div>

                    <!-- Department -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Department</strong>
                        <span>
                            <?php
                                // Placeholder for department name
                                // نص مؤقت لاسم القسم
                                echo 'Department Name'; 
                            ?>
                        </span>
                    </div>
                    
                    <!-- Enrollment Date -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Enrollment Date</strong>
                        <span><?= htmlspecialchars($student->enrollment_date ?? 'N/A') ?></span>
                    </div>

                    <!-- Status -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Status</strong>
                        <span class="badge 
                            <?= $student->status === 'active' ? 'bg-success' : ($student->status === 'graduated' ? 'bg-secondary' : 'bg-danger') ?>">
                            <?= ucfirst(htmlspecialchars($student->status)) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 
            Placeholder for future sections (e.g., Guardians)
            مكان مخصص لأقسام مستقبلية (مثل أولياء الأمور)
        -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Guardians</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Guardian information will be displayed here.</p>
                <!-- @TODO: Loop through $guardians array when available -->
            </div>
        </div>

    </div>
</div>

<!-- 
    Placeholder for future tabs (Grades, Attendance, etc.)
    مكان مخصص لعلامات تبويب مستقبلية (الدرجات، الحضور، إلخ)
-->
<div class="card shadow-sm mt-4">
    <div class="card-header">
        <!-- Nav tabs can be added here -->
        <h5 class="mb-0">Student Academic Record</h5>
    </div>
    <div class="card-body">
        <p class="text-muted">Enrolled courses, grades, and attendance records will be displayed here.</p>
        <!-- @TODO: Add tabs and tables for enrollments, grades, etc. -->
    </div>
</div>