<?php
/**
 * Student Dashboard View
 *
 * This file is injected into the 'main.php' layout.
 * It displays information relevant to the logged-in student,
 * such as their enrolled courses, recent grades, and announcements.
 *
 * @var array $enrollments Data passed from DashboardController::getStudentDashboardData()
 * @var array $announcements (Assumed to be passed from controller)
 * @var array $assignments (Assumed to be passed from controller)
 */

// Set the title for the layout
$title = "My Dashboard";
?>

<div class="container-fluid p-4 mb-4 bg-light rounded-3">
    <div class="container-fluid py-3">
        <h1 class="display-5 fw-bold">Welcome, <?= htmlspecialchars(\App\Helpers\Session::get('user_name') ?? 'Student') ?>!</h1>
        <p class="col-md-8 fs-4">Here's a summary of your academic progress, upcoming deadlines, and recent announcements.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <h3>My Enrolled Courses</h3>
        <hr>
        
        <?php if (empty($enrollments)): ?>
            <div class="alert alert-info">You are not currently enrolled in any courses.</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($enrollments as $enrollment): ?>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><?= htmlspecialchars($enrollment['course_name']) ?></h5>
                            <small>Attendance: <?= htmlspecialchars($enrollment['attendance']) ?></small>
                        </div>
                        <span class="badge bg-primary rounded-pill fs-6">
                            Grade: <?= htmlspecialchars($enrollment['grade']) ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <strong>Upcoming Deadlines</strong>
            </div>
            <ul class="list-group list-group-flush">
                <?php /* if (!empty($assignments)): ?>
                    <?php foreach ($assignments as $assignment): ?>
                        <li class="list-group-item">
                            <a href="/assignments/view/<?= $assignment->id ?>">
                                <?= htmlspecialchars($assignment->title) ?>
                            </a>
                            <small class="d-block text-muted">Due: <?= htmlspecialchars($assignment->due_date) ?></small>
                        </li>
                    <?php endforeach; ?>
                <?php else: */ ?>
                    <li class="list-group-item text-muted">No upcoming deadlines.</li>
                <?php /* endif; */ ?>
            </ul>
        </div>
        
        <div class="card">
            <div class="card-header">
                <strong>Recent Announcements</strong>
            </div>
            <ul class="list-group list-group-flush">
                 <?php /* if (!empty($announcements)): ?>
                    <?php foreach ($announcements as $announcement): ?>
                        <li class="list-group-item">
                            <h6 class="mb-1"><?= htmlspecialchars($announcement->title) ?></h6>
                            <small class="text-muted"><?= date('M j, Y', strtotime($announcement->publish_at)) ?></small>
                        </li>
                    <?php endforeach; ?>
                <?php else: */ ?>
                    <li class="list-group-item text-muted">No recent announcements.</li>
                <?php /* endif; */ ?>
            </ul>
        </div>
    </div>
</div>