<?php
/**
 * Manage Student Fee View
 *
 * Renders the form for managing payments for a specific student fee record.
 * Displays fee details and allows recording new payments.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $studentFee The specific StudentFee object being managed.
 * @var object $student The student associated with the fee.
 * @var object $feeCategory The fee category associated with the fee.
 * @var array $errors (Assumed from Validator)
 * @var array $paymentHistory (Optional: past payments for this fee record)
 */

// Set the dynamic title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Manage Payment: " . htmlspecialchars($feeCategory->name ?? 'Fee') . " for " . htmlspecialchars($student->first_name . ' ' . $student->last_name);

// Calculate current balance
// حساب الرصيد الحالي
$balance = ($studentFee->amount_due ?? 0) - ($studentFee->amount_paid ?? 0);

// Determine status color
// تحديد لون الحالة
$statusClass = 'bg-secondary'; // Default
if ($studentFee->status === 'paid') $statusClass = 'bg-success';
elseif ($studentFee->status === 'pending') $statusClass = 'bg-warning text-dark';
elseif ($studentFee->status === 'overdue') $statusClass = 'bg-danger';
elseif ($studentFee->status === 'cancelled') $statusClass = 'bg-light text-dark';

?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-0">Manage Fee Payment</h2>
        <small class="text-muted">Record ID: <?= htmlspecialchars($studentFee->id) ?></small>
    </div>
    <a href="/fees" class="btn btn-secondary"> <!-- Link back to the main fees index -->
        <i class="fas fa-arrow-left"></i> Back to Fees List
    </a>
</div>

<!-- 
    Fee Details Card
    بطاقة تفاصيل الرسوم
-->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0">Fee Details</h5>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Student</dt>
            <dd class="col-sm-9">
                <a href="/students/<?= htmlspecialchars($student->id ?? '') ?>">
                    <?= htmlspecialchars($student->first_name . ' ' . $student->last_name ?? 'N/A') ?>
                </a>
            </dd>

            <dt class="col-sm-3">Fee Category</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($feeCategory->name ?? 'N/A') ?></dd>

            <dt class="col-sm-3">Amount Due</dt>
            <dd class="col-sm-9">$<?= htmlspecialchars(number_format($studentFee->amount_due ?? 0, 2)) ?></dd>

            <dt class="col-sm-3">Amount Paid</dt>
            <dd class="col-sm-9">$<?= htmlspecialchars(number_format($studentFee->amount_paid ?? 0, 2)) ?></dd>

            <dt class="col-sm-3">Balance</dt>
            <dd class="col-sm-9"><strong class="<?= $balance > 0 ? 'text-danger' : 'text-success' ?>">$<?= htmlspecialchars(number_format($balance, 2)) ?></strong></dd>

            <dt class="col-sm-3">Due Date</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($studentFee->due_date ? date('Y-m-d', strtotime($studentFee->due_date)) : 'N/A') ?></dd>
            
            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9">
                <span class="badge <?= $statusClass ?>">
                    <?= ucfirst(htmlspecialchars($studentFee->status ?? 'N/A')) ?>
                </span>
            </dd>
        </dl>
    </div>
</div>

<!-- 
    Record Payment Form Card
    بطاقة نموذج تسجيل دفعة
-->
<div class="card shadow-sm mb-4">
    <div class="card-header">
         <h5 class="mb-0"><i class="fas fa-cash-register me-2"></i>Record New Payment</h5>
    </div>
    <div class="card-body">
        <!-- @TODO: Form action should point to a specific payment processing route -->
        <!-- مسار النموذج يجب أن يوجه إلى مسار معالجة الدفع المحدد -->
        <form action="/fees/payment/<?= htmlspecialchars($studentFee->id) ?>" method="POST" id="recordPaymentForm">
            
            <!-- 
                CSRF Token 
                @TODO: Replace with dynamic token.
            -->
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">
            
            <!-- Input to identify which fee record this payment is for -->
            <!-- حقل لتحديد سجل الرسوم الذي تخصه هذه الدفعة -->
            <input type="hidden" name="student_fee_id" value="<?= htmlspecialchars($studentFee->id) ?>">

            <div class="row">
                <!-- Amount Paid -->
                <div class="col-md-4 mb-3">
                    <label for="amount_paid_now" class="form-label">Amount Paid</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0.01" 
                               max="<?= htmlspecialchars($balance > 0 ? $balance : 0.01) ?>" 
                               class="form-control" id="amount_paid_now" name="amount_paid_now" 
                               required <?= $balance <= 0 ? 'disabled' : '' ?>>
                    </div>
                     <!-- @TODO: Display validation error -->
                </div>

                <!-- Payment Date -->
                <div class="col-md-4 mb-3">
                    <label for="paid_date" class="form-label">Payment Date</label>
                    <input type="date" class="form-control" id="paid_date" name="paid_date" 
                           value="<?= date('Y-m-d') ?>" required <?= $balance <= 0 ? 'disabled' : '' ?>>
                     <!-- @TODO: Display validation error -->
                </div>

                 <!-- Payment Method -->
                <div class="col-md-4 mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                     <select class="form-select" id="payment_method" name="payment_method" <?= $balance <= 0 ? 'disabled' : '' ?>>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Check">Check</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <!-- Transaction ID / Notes -->
            <div class="mb-3">
                <label for="transaction_id" class="form-label">Transaction ID / Notes (Optional)</label>
                <input type="text" class="form-control" id="transaction_id" name="transaction_id" <?= $balance <= 0 ? 'disabled' : '' ?>>
            </div>

            <hr>

            <!-- 
                Form Action Buttons
                أزرار الإجراءات
            -->
            <div class="d-flex justify-content-end">
                <a href="/fees" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success" <?= $balance <= 0 ? 'disabled' : '' ?>>
                    <i class="fas fa-save me-1"></i> Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 
    Payment History Card (Optional)
    بطاقة سجل الدفعات (اختياري)
-->
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Payment History for this Fee</h5>
    </div>
    <div class="card-body">
         <?php if (empty($paymentHistory)): ?>
            <p class="text-muted">No payments have been recorded for this specific fee item yet.</p>
         <?php else: ?>
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Date Paid</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Transaction/Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- @TODO: Loop through $paymentHistory array -->
                    <!-- الدوران عبر مصفوفة $paymentHistory -->
                    <tr><td>2024-10-20</td><td>$500.00</td><td>Bank Transfer</td><td>Ref#12345</td></tr>
                     <tr><td>2024-09-15</td><td>$500.00</td><td>Cash</td><td></td></tr>
                </tbody>
            </table>
         <?php endif; ?>
    </div>
</div>

<!-- @TODO: Ensure Font Awesome is included for icons -->
<!-- تأكد من تضمين Font Awesome لعرض الأيقونات -->