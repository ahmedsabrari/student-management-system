<?php
/**
 * Record Payment View
 *
 * Renders the form for recording a general payment from a student.
 * This view displays student summary and provides fields for payment details.
 * It's assumed the controller will handle allocation of this payment against
 * outstanding fees if necessary.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var object $student The student for whom the payment is being recorded.
 * @var object $department The student's department.
 * @var float $totalBalance The student's total outstanding balance.
 * @var array $errors (Assumed from Validator)
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Record Payment for " . htmlspecialchars($student->first_name . ' ' . $student->last_name);
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Record Student Payment</h2>
    <!-- Link back to the main fees index or student's fee list -->
    <!-- رابط للعودة إلى قائمة الرسوم الرئيسية أو قائمة رسوم الطالب -->
    <a href="/fees" class="btn btn-secondary"> 
        <i class="fas fa-arrow-left"></i> Back to Fees List
    </a>
</div>

<!-- 
    Student Summary Card
    بطاقة ملخص الطالب
-->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0">Student Details</h5>
    </div>
    <div class="card-body">
         <dl class="row">
            <dt class="col-sm-3">Full Name</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($student->first_name . ' ' . $student->last_name ?? 'N/A') ?></dd>

            <dt class="col-sm-3">Student Number</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($student->student_number ?? 'N/A') ?></dd>

            <dt class="col-sm-3">Department</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($department->name ?? 'N/A') ?></dd>

            <dt class="col-sm-3">Total Outstanding Balance</dt>
            <dd class="col-sm-9"><strong class="<?= ($totalBalance > 0) ? 'text-danger' : 'text-success' ?>">$<?= htmlspecialchars(number_format($totalBalance ?? 0, 2)) ?></strong></dd>
        </dl>
    </div>
</div>


<!-- 
    Record Payment Form Card
    بطاقة نموذج تسجيل دفعة
-->
<div class="card shadow-sm">
    <div class="card-header">
         <h5 class="mb-0"><i class="fas fa-cash-register me-2"></i>Enter Payment Details</h5>
    </div>
    <div class="card-body">
        <!-- @TODO: Form action should point to the correct payment processing route, possibly passing student ID -->
        <!-- مسار النموذج يجب أن يوجه إلى مسار معالجة الدفع الصحيح، ربما مع تمرير رقم الطالب -->
        <form action="/fees/payment/record" method="POST" id="recordGeneralPaymentForm"> 
            
            <!-- 
                CSRF Token 
                @TODO: Replace with dynamic token.
            -->
            <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">
            
            <!-- Hidden input for student ID -->
            <!-- حقل مخفي لرقم الطالب -->
            <input type="hidden" name="student_id" value="<?= htmlspecialchars($student->id) ?>">

            <div class="row">
                <!-- Payment Date -->
                <div class="col-md-4 mb-3">
                    <label for="paid_date" class="form-label">Payment Date</label>
                    <input type="date" class="form-control" id="paid_date" name="paid_date" 
                           value="<?= date('Y-m-d') ?>" required>
                     <!-- @TODO: Display validation error -->
                </div>

                 <!-- Amount Paid -->
                <div class="col-md-4 mb-3">
                    <label for="amount_paid" class="form-label">Amount Paid</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0.01" 
                               class="form-control" id="amount_paid" name="amount_paid" 
                               required placeholder="Enter amount">
                    </div>
                     <!-- @TODO: Display validation error -->
                </div>

                 <!-- Payment Method -->
                <div class="col-md-4 mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                     <select class="form-select" id="payment_method" name="payment_method" required>
                        <option value="">Select Method...</option>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Check">Check</option>
                        <option value="Other">Other</option>
                    </select>
                     <!-- @TODO: Display validation error -->
                </div>
            </div>

            <!-- Reference Number (Optional) -->
            <div class="mb-3">
                <label for="reference_number" class="form-label">Reference Number / Transaction ID (Optional)</label>
                <input type="text" class="form-control" id="reference_number" name="reference_number" placeholder="e.g., Check #123, Bank Ref XYZ">
            </div>

            <!-- Notes (Optional) -->
            <div class="mb-3">
                <label for="notes" class="form-label">Notes (Optional)</label>
                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional notes about this payment..."></textarea>
            </div>

            <hr>

            <!-- 
                Form Action Buttons
                أزرار الإجراءات
            -->
            <div class="d-flex justify-content-end">
                 <!-- Cancel button might go back to the student's fee list or main fees list -->
                 <!-- زر الإلغاء قد يعود إلى قائمة رسوم الطالب أو القائمة الرئيسية -->
                <a href="/fees" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- @TODO: Ensure Font Awesome is included for icons -->
<!-- تأكد من تضمين Font Awesome لعرض الأيقونات -->
