<?php
/**
 * File: app/Views/partials/_breadcrumb.php
 *
 * Renders the page breadcrumb navigation.
 * It is included by layouts/main.php.
 *
 * (يعرض هذا الملف مسار التنقل (breadcrumb) الخاص بالصفحة)
 *
 * Expected variables:
 * @var array $breadcrumbs (Optional) Array of breadcrumb links.
 * e.g., [['label'=>'Dashboard','url'=>'/dashboard'], ['label'=>'Students','url'=>null]]
 */
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <?php
        // Default to Dashboard if no breadcrumbs are provided
        // (العودة إلى لوحة التحكم كافتراضي إذا لم يتم توفير مسار)
        $items = $breadcrumbs ?? [['label'=>'Dashboard','url'=>'/dashboard']];
        
        foreach ($items as $index => $bc):
            // Check if it's the last item
            // (التحقق مما إذا كان العنصر الأخير)
            $isLast = $index === array_key_last($items);
        ?>
            <li class="breadcrumb-item <?= $isLast ? 'active' : '' ?>" <?= $isLast ? 'aria-current="page"' : '' ?>>
                <?php if (!empty($bc['url']) && !$isLast): ?>
                    <a href="<?= htmlspecialchars($bc['url']) ?>"><?= htmlspecialchars($bc['label']) ?></a>
                <?php else: ?>
                    <?= htmlspecialchars($bc['label']) ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
