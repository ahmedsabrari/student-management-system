<?php
/**
 * Financial Report View
 *
 * Displays a filterable report of student fee statuses, balances, and overall financial summary.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $reportData The main data for the report, passed from ReportController::financial().
 * Expected format for each row:
 * ['student_number', 'full_name', 'fee_category_name', 'amount_due', 'amount_paid', 'balance', 'status']
 * @var array $academic_years (Assumed for filter dropdown).
 * @var array $feeCategories (Assumed for filter dropdown).
 * @var array $filters An array of the currently active filters.
 * @var float $totalDue (Calculated and passed by controller).
 * @var float $totalPaid (Calculated and passed by controller).
 * @var float $totalBalance (Calculated and passed by controller).
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Financial Report";

// Calculate summary totals if not passed directly (better done in controller)
// حساب الإجماليات إذا لم يتم تمريرها (الأفضل القيام به في الـ Controller)
$totalDue = $totalDue ?? array_sum(array_column($reportData ?? [], 'amount_due'));
$totalPaid = $totalPaid ?? array_sum(array_column($reportData ?? [], 'amount_paid'));
$totalBalance = $totalBalance ?? ($totalDue - $totalPaid);

// Prepare data for Chart.js
// تحضير بيانات الرسم البياني
$chartLabels = ['Total Paid', 'Total Outstanding'];
$chartDataValues = [$totalPaid, max(0, $totalBalance)]; // Ensure balance isn't negative for chart
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Financial Report</h2>
    <div>
        <!-- @TODO: Implement Export Functionality -->
        <!-- تنفيذ وظيفة التصدير -->
        <button class="btn btn-outline-danger disabled"><i class="fas fa-file-pdf"></i> Export PDF</button>
        <button class="btn btn-outline-success disabled"><i class="fas fa-file-excel"></i> Export Excel</button>
    </div>
</div>

<!-- 
    Filter Form Card
    بطاقة نموذج الفلترة
-->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Report</h5>
    </div>
    <div class="card-body">
        <form action="/reports/financial" method="GET" class="row g-3 align-items-end">
            <!-- Academic Year Filter -->
            <div class="col-md-3">
                <label for="academic_year_id" class="form-label">Academic Year</label>
                <select id="academic_year_id" name="academic_year_id" class="form-select">
                     <option value="">All Years</option>
                    <!-- @TODO: Populate from $academic_years variable -->
                     <?php if(!empty($academic_years)): ?>
                        <?php foreach($academic_years as $year): ?>
                            <option value="<?= htmlspecialchars($year->id) ?>" <?= (isset($filters['academic_year_id']) && $filters['academic_year_id'] == $year->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($year->name) ?>
                            </option>
                        <?php endforeach; ?>
                     <?php endif; ?>
                </select>
            </div>
            <!-- Fee Category Filter -->
             <div class="col-md-3">
                <label for="fee_category_id" class="form-label">Fee Category</label>
                <select id="fee_category_id" name="fee_category_id" class="form-select">
                     <option value="">All Categories</option>
                    <!-- @TODO: Populate from $feeCategories variable -->
                     <?php if(!empty($feeCategories)): ?>
                        <?php foreach($feeCategories as $category): ?>
                            <option value="<?= htmlspecialchars($category->id) ?>" <?= (isset($filters['fee_category_id']) && $filters['fee_category_id'] == $category->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category->name) ?>
                            </option>
                        <?php endforeach; ?>
                     <?php endif; ?>
                </select>
            </div>
            <!-- Status Filter -->
             <div class="col-md-3">
                <label for="status" class="form-label">Payment Status</label>
                <select id="status" name="status" class="form-select">
                     <option value="">All Statuses</option>
                     <option value="pending" <?= (isset($filters['status']) && $filters['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                     <option value="paid" <?= (isset($filters['status']) && $filters['status'] == 'paid') ? 'selected' : '' ?>>Paid</option>
                     <option value="overdue" <?= (isset($filters['status']) && $filters['status'] == 'overdue') ? 'selected' : '' ?>>Overdue</option>
                     <option value="cancelled" <?= (isset($filters['status']) && $filters['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <!-- Action Buttons -->
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="/reports/financial" class="btn btn-secondary w-100 mt-1">Reset</a>
            </div>
        </form>
    </div>
</div>


<!-- 
    Report Summary & Chart Row
    صف ملخص التقرير والرسم البياني
-->
<div class="row mb-4">
    <!-- Chart Card -->
    <div class="col-md-5">
         <div class="card shadow-sm h-100">
            <div class="card-header">
                <h6 class="mb-0">Payment Summary</h6>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                 <?php if ($totalDue > 0): ?>
                    <canvas id="financialChart" style="max-height: 250px;"></canvas>
                 <?php else: ?>
                    <p class="text-muted">No financial data available for chart.</p>
                 <?php endif; ?>
            </div>
        </div>
    </div>
     <!-- Key Stats Card -->
    <div class="col-md-7">
        <div class="card shadow-sm h-100">
             <div class="card-header">
                <h6 class="mb-0">Financial Overview (Filtered)</h6>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-5">Total Amount Due:</dt>
                    <dd class="col-sm-7">$<?= htmlspecialchars(number_format($totalDue, 2)) ?></dd>
                    
                    <dt class="col-sm-5">Total Amount Paid:</dt>
                    <dd class="col-sm-7 text-success">$<?= htmlspecialchars(number_format($totalPaid, 2)) ?></dd>

                    <dt class="col-sm-5">Total Outstanding Balance:</dt>
                    <dd class="col-sm-7 fw-bold <?= ($totalBalance > 0) ? 'text-danger' : 'text-success' ?>">
                        $<?= htmlspecialchars(number_format($totalBalance, 2)) ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>


<!-- 
    Financial Report Table
    جدول التقرير المالي
-->
<div class="card shadow-sm">
     <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Detailed Records</h5>
    </div>
    <div class="card-body">
        <?php if (empty($reportData)): ?>
            <div class="alert alert-info">No financial records found matching the criteria.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle" id="financialReportTable">
                    <thead class="table-light">
                        <tr>
                            <th>Student Number</th>
                            <th>Full Name</th>
                            <th>Fee Category</th>
                            <th>Amount Due</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportData as $fee): /* Assumes controller provides pre-joined data */ ?>
                             <?php
                                $balance = ($fee['amount_due'] ?? 0) - ($fee['amount_paid'] ?? 0);
                                $status = strtolower($fee['status'] ?? '');
                                $statusClass = 'bg-secondary'; // Default
                                if ($status === 'paid') $statusClass = 'bg-success';
                                elseif ($status === 'pending') $statusClass = 'bg-warning text-dark';
                                elseif ($status === 'overdue') $statusClass = 'bg-danger';
                                elseif ($status === 'cancelled') $statusClass = 'bg-light text-dark';
                            ?>
                            <tr class="fee-row" data-search-term="<?= htmlspecialchars(strtolower($fee['student_number'] . ' ' . $fee['full_name'] . ' ' . $fee['fee_category_name'])) ?>">
                                <td><?= htmlspecialchars($fee['student_number'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($fee['full_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($fee['fee_category_name'] ?? 'N/A') ?></td>
                                <td>$<?= htmlspecialchars(number_format($fee['amount_due'] ?? 0, 2)) ?></td>
                                <td class="text-success">$<?= htmlspecialchars(number_format($fee['amount_paid'] ?? 0, 2)) ?></td>
                                <td class="fw-bold <?= ($balance > 0) ? 'text-danger' : '' ?>">$<?= htmlspecialchars(number_format($balance, 2)) ?></td>
                                <td>
                                    <span class="badge <?= $statusClass ?>">
                                        <?= ucfirst(htmlspecialchars($fee['status'] ?? 'N/A')) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- @TODO: Add Pagination for large datasets -->
            <!-- إضافة ترقيم الصفحات لمجموعات البيانات الكبيرة -->
        <?php endif; ?>
    </div>
</div>

<!-- Chart.js Script -->
<!-- سكريبت الرسم البياني -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('financialChart');
        if (ctx && <?= $totalDue ?> > 0) { // Only render if canvas exists and data is available
            const financialChart = new Chart(ctx.getContext('2d'), {
                type: 'doughnut', // Doughnut chart suits paid vs outstanding
                data: {
                    labels: <?= json_encode($chartLabels) ?>,
                    datasets: [{
                        label: 'Amount ($)',
                        data: <?= json_encode($chartDataValues) ?>,
                        backgroundColor: [
                            'rgba(25, 135, 84, 0.7)',  // Success (Paid)
                            'rgba(220, 53, 69, 0.7)',  // Danger (Outstanding)
                        ],
                        borderColor: [
                            'rgba(25, 135, 84, 1)',
                            'rgba(220, 53, 69, 1)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                         tooltip: {
                             callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': $';
                                    }
                                    if (context.parsed !== null) {
                                        // Format as currency
                                        label += context.parsed.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

<!-- @TODO: Add client-side search/filter script if needed -->
<!-- إضافة سكريبت بحث/فلترة من جانب العميل إذا لزم الأمر -->
<!-- @TODO: Ensure Font Awesome is included -->
<!-- تأكد من تضمين Font Awesome -->