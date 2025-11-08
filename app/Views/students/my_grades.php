<?php $title = "My Grades"; ?>

<div class="card shadow-sm">
    <div class="card-header">
        <h2 class="h5 mb-0"><?= htmlspecialchars($title) ?></h2>
    </div>
    <div class="card-body">
        <p class="text-muted">Your grades will be listed here soon.</p>
        
        <!-- @TODO: Loop through $grades array -->
        <?php if (empty($grades)): ?>
            <div class="alert alert-info">No grades have been recorded for you yet.</div>
        <?php else: ?>
            <!-- Loop logic here -->
        <?php endif; ?>
    </div>
</div>

