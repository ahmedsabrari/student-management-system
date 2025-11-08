<?php
/**
 * Teacher Dashboard View
 *
 * This file is injected into the 'admin.php' layout.
 * It displays information relevant to the logged-in teacher,
 * such as their assigned classes, upcoming assignments, and quick actions.
 *
 * @var array $courses (Passed from DashboardController::getTeacherDashboardData())
 * @var array $assignments (Assumed to be passed)
 * @var array $events (Assumed to be passed)
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Teacher Dashboard";
?>

<div class="container-fluid p-4 mb-4 bg-light rounded-3">
    <div class="container-fluid py-3">
        <h1 class="display-5 fw-bold">Welcome, <?= htmlspecialchars(\App\Helpers\Session::get('user_name') ?? 'Teacher') ?>!</h1>
        <p class="col-md-8 fs-4">Here is a summary of your assigned classes and quick actions for today.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <h3>My Active Classes</h3>
        <hr>
        
        <?php if (empty($courses)): // Using $courses var as per DashboardController ?>
            <div class="alert alert-info">You are not currently assigned to any active classes.</div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Course / Class Name</th>
                                <th>Class Count</th>
                                <th>Total Students</th>
                                <th>Quick Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($course['name']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($course['class_count']) ?></td>
                                    <td><?= htmlspecialchars($course['student_count']) ?></td>
                                    <td>
                                        <a href="/attendance/take/1" class="btn btn-sm btn-outline-primary">Take Attendance</a>
                                        <a href="/grades/manage/1" class="btn btn-sm btn-outline-secondary">Manage Grades</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-4">
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <strong>Upcoming Assignments</strong>
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
                    <li class="list-group-item text-muted">No upcoming assignments.</li>
                <?php /* endif; */ ?>
            </ul>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header">
                <strong>Upcoming Events</strong>
            </div>
            <ul class="list-group list-group-flush">
                 <?php /* if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <li class="list-group-item">
                            <h6 class="mb-1"><?= htmlspecialchars($event->title) ?></h6>
                            <small class="text-muted"><?= date('M j, Y', strtotime($event->event_date)) ?></small>
                        </li>
                    <?php endforeach; ?>
                <?php else: */ ?>
                    <li class="list-group-item text-muted">No upcoming events.</li>
                <?php /* endif; */ ?>
            </ul>
        </div>
    </div>
</div>