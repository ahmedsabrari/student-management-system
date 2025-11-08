<?php $title = "My Assignments"; ?>

<div class="card shadow-sm">
    <div class="card-header">
        <h2 class="h5 mb-0"><?= htmlspecialchars($title) ?></h2>
    </div>
    <div class="card-body">
        <p class="text-muted">Your assignments and submissions will be listed here soon.</p>
        
        <!-- @TODO: Loop through $submissions array -->
        <?php if (empty($submissions)): ?>
            <div class="alert alert-info">No assignments found.</div>
        <?php else: ?>
            <!-- Loop logic here -->
        <?php endif; ?>
    </div>
</div>

