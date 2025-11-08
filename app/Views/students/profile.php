<?php
/**
 * Student Profile View
 *
 * Displays the detailed profile page for a student, potentially viewed by the student themselves.
 * This view is rendered within a main layout (e.g., 'main.php' or 'admin.php').
 *
 * @var object $student The student object passed from the controller.
 * @var array $grades (Assumed to be passed, containing grade details)
 * @var array $attendanceSummary (Assumed to be passed, containing attendance stats)
 */

// Set the dynamic title for the layout
// (This $title variable will be used by the layout file)
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "My Profile"; // Or dynamically set if viewed by admin

// Assume Font Awesome is included in the main layout
?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-2 text-center mb-3 mb-md-0">
                <img src="<?= htmlspecialchars($student->avatar ?? '/assets/images/default-avatar.png') ?>" 
                     alt="Student Avatar" 
                     class="img-fluid rounded-circle" 
                     style="width: 100px; height: 100px; object-fit: cover;">
            </div>
            <div class="col-md-7">
                <h2 class="mb-0"><?= htmlspecialchars($student->first_name . ' ' . $student->last_name) ?></h2>
                <p class="text-muted mb-1">Student ID: <?= htmlspecialchars($student->student_number ?? 'N/A') ?></p>
                <p class="text-muted mb-0"><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($student->email ?? 'N/A') ?></p>
            </div>
            <div class="col-md-3 text-md-end mt-3 mt-md-0">
                <a href="/students" class="btn btn-outline-secondary btn-sm mb-1 w-100">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
                <a href="/settings/profile" class="btn btn-warning btn-sm mb-1 w-100"> 
                    <i class="fas fa-edit me-1"></i> Edit Profile
                </a>
                <a href="#grades-section" class="btn btn-info btn-sm w-100">
                    <i class="fas fa-graduation-cap me-1"></i> View Grades
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Personal & Academic Details</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Department</dt>
                    <dd class="col-sm-8">
                        <?php 
                            // Placeholder - Department name should ideally be joined in the Controller
                            // نص مؤقت - اسم القسم يفضل جلبه في الـ Controller
                            echo 'Department Placeholder'; 
                        ?>
                    </dd>

                    <dt class="col-sm-4">Date of Birth</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($student->date_of_birth ?? 'N/A') ?></dd>

                    <dt class="col-sm-4">Gender</dt>
                    <dd class="col-sm-8"><?= ucfirst(htmlspecialchars($student->gender ?? 'N/A')) ?></dd>
                </dl>
            </div>
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Phone</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($student->phone ?? 'N/A') ?></dd>
                    
                    <dt class="col-sm-4">Address</dt>
                    <dd class="col-sm-8"><?= nl2br(htmlspecialchars($student->address ?? 'N/A')) ?></dd>
                    
                    <dt class="col-sm-4">Enrollment Date</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($student->enrollment_date ?? 'N/A') ?></dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                         <span class="badge 
                            <?= $student->status === 'active' ? 'bg-success' : ($student->status === 'graduated' ? 'bg-secondary' : 'bg-danger') ?>">
                            <?= ucfirst(htmlspecialchars($student->status)) ?>
                        </span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4" id="grades-section">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-star me-2"></i>Grades Summary</h5>
    </div>
    <div class="card-body">
        <?php if (empty($grades)): ?>
            <p class="text-muted">No grades recorded yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Course</th>
                            <th>Grade</th>
                            <th>Grade Points</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php /* foreach ($grades as $grade): ?>
                            <tr>
                                <td><?= htmlspecialchars($grade->course_name ?? 'N/A') ?></td> 
                                <td><?= htmlspecialchars($grade->grade ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($grade->grade_points ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($grade->remarks ?? '') ?></td>
                            </tr>
                        <?php endforeach; */ ?>
                        <tr><td>Web Development</td><td>A</td><td>4.00</td><td>Excellent work</td></tr>
                        <tr><td>Database Design</td><td>B+</td><td>3.33</td><td>Good effort</td></tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Attendance Summary</h5>
    </div>
    <div class="card-body">
         <?php if (empty($attendanceSummary)): ?>
            <p class="text-muted">No attendance data available yet.</p>
         <?php else: ?>
             <p>Overall Attendance: <strong><?= htmlspecialchars($attendanceSummary['overall_percentage'] ?? 'N/A') ?>%</strong></p>
             <?php endif; ?>
         <p class="text-muted">Detailed attendance records will be available soon.</p>
    </div>
</div>