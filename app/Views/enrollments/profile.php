<?php
/**
 * Enrollment Profile View
 *
 * Displays a comprehensive profile for a single enrollment, focusing
 * on the student's details within the context of that enrollment.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $enrollment The enrollment object passed from the controller.
 * @var object $student The student associated with the enrollment.
 * @var object $class The class associated with the enrollment.
 * @var object $course The course associated with the class.
 * @var object $academicYear The academic year associated with the class.
 * @var object $department The department of the student.
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Enrollment Profile for " . htmlspecialchars($student->first_name . ' ' . $student->last_name);
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-0">Enrollment Profile</h2>
        <small class="text-muted">Enrollment ID: <?= htmlspecialchars($enrollment->id) ?></small>
    </div>
    <div>
        <a href="/enrollments" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        <!-- This edit button could link to the enrollment edit page -->
        <!-- زر التعديل يمكن أن يوجه إلى صفحة تعديل التسجيل -->
        <a href="/enrollments/edit/<?= htmlspecialchars($enrollment->id) ?>" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Enrollment
        </a>
    </div>
</div>

<div class="row">
    <!-- 
        Left Column: Student Profile Card
        العمود الأيسر: بطاقة الملف الشخصي للطالب
    -->
    <div class="col-lg-5">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Student Information</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                     <img src="<?= htmlspecialchars($student->avatar ?? '/assets/images/default-avatar.png') ?>" 
                         alt="Student Avatar" 
                         class="img-fluid rounded-circle" 
                         style="width: 90px; height: 90px; object-fit: cover;">
                </div>
                <dl class="row">
                    <dt class="col-sm-5">Full Name</dt>
                    <dd class="col-sm-7">
                        <a href="/students/<?= htmlspecialchars($student->id ?? '') ?>">
                            <?= htmlspecialchars($student->first_name . ' ' . $student->last_name ?? 'N/A') ?>
                        </a>
                    </dd>

                    <dt class="col-sm-5">Student Number</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($student->student_number ?? 'N/A') ?></dd>
                    
                    <dt class="col-sm-5">Email</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($student->email ?? 'N/A') ?></dd>

                    <dt class="col-sm-5">Department</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($department->name ?? 'N/A') ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- 
        Right Column: Enrollment Details Card
        العمود الأيمن: بطاقة تفاصيل التسجيل
    -->
    <div class="col-lg-7">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Enrollment Details</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Class Name</dt>
                    <dd class="col-sm-8">
                         <a href="/classes/<?= htmlspecialchars($class->id ?? '') ?>">
                            <?= htmlspecialchars($class->name ?? 'N/A') ?>
                         </a>
                    </dd>

                    <dt class="col-sm-4">Course</dt>
                    <dd class="col-sm-8">
                        <a href="/courses/<?= htmlspecialchars($course->id ?? '') ?>">
                           <?= htmlspecialchars($course->name ?? 'N/A') ?>
                        </a>
                    </dd>
                    
                    <dt class="col-sm-4">Academic Year</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($academicYear->name ?? 'N/A') ?></dd>

                    <dt class="col-sm-4">Enrollment Date</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($enrollment->enrollment_date ? date('F j, Y', strtotime($enrollment->enrollment_date)) : 'N/A') ?></dd>
                    
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        <span class="badge 
                            <?= $enrollment->status === 'enrolled' ? 'bg-success' : ($enrollment->status === 'completed' ? 'bg-secondary' : 'bg-danger') ?>">
                            <?= ucfirst(htmlspecialchars($enrollment->status)) ?>
                        </span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- @TODO: Ensure Font Awesome is included in the main layout for icons -->
<!-- تأكد من تضمين Font Awesome في القالب الرئيسي لعرض الأيقونات -->
