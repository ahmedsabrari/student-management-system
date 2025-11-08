<?php $title = "My Courses"; ?>

<div class="card shadow-sm">
    <div class="card-header">
        <h2 class="h5 mb-0"><?= htmlspecialchars($title) ?></h2>
    </div>
    <div class="card-body">
        <p class="text-muted">Your enrolled courses will be listed here soon.</p>
        
        <!-- @TODO: Loop through $enrollments array to display course info -->
        <!-- مثال: -->
        <?php if (empty($enrollments)): ?>
            <div class="alert alert-info">You are not currently enrolled in any courses.</div>
        <?php else: ?>
            <!-- Loop logic here -->
        <?php endif; ?>
    </div>
</div>

