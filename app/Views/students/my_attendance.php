<?php $title = "My Attendance"; ?>

<div class="card shadow-sm">
    <div class="card-header">
        <h2 class="h5 mb-0"><?= htmlspecialchars($title) ?></h2>
    </div>
    <div class="card-body">
        <p class="text-muted">Your attendance records will be listed here soon.</P>
        
        <!-- @TODO: Loop through $attendance array -->
        <?php if (empty($attendance)): ?>
            <div class="alert alert-info">No attendance records found.</div>
        <?php else: ?>
            <!-- Loop logic here -->
        <?php endif; ?>
    </div>
</div>

