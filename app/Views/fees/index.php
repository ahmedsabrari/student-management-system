<?php
/**
 * Fees Index View
 *
 * Displays tables for both Fee Categories and individual Student Fees.
 * Provides a central point for managing financial aspects.
 * This view is rendered within the 'admin.php' layout.
 *
 * @var array $feeCategories An array of FeeCategory objects.
 * @var array $studentFees An array of StudentFee objects (assumes data is pre-joined).
 */

// Set the title for the layout
// (This $title variable will be used by 'layouts/admin.php')
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "Manage Fees";

// @TODO: Enhance controller queries to pre-join necessary data for both tables
// to avoid N+1 issues, especially for the Student Fees table.
// (لأداء أفضل، يجب تحسين الاستعلامات في الـ Controller لربط الجداول المطلوبة مسبقًا)
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Financial Management</h2>
    <div>
        <a href="/fees/categories/create" class="btn btn-primary me-2">
            <i class="fas fa-plus"></i> Add Fee Category
        </a>
        <a href="/fees/manage" class="btn btn-info">
            <i class="fas fa-tasks"></i> Manage Student Fees
        </a>
         <!-- Manage Student Fees might link to a page for assigning fees or viewing payment statuses -->
         <!-- رابط "إدارة رسوم الطلاب" قد يوجه لصفحة تخصيص الرسوم أو عرض حالات الدفع -->
    </div>
</div>

<!-- 
    Fee Categories Table Card
    بطاقة جدول فئات الرسوم
-->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Fee Categories</h5>
    </div>
    <div class="card-body">
        <?php if (empty($feeCategories)): ?>
            <div class="alert alert-info">No fee categories have been created yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feeCategories as $category): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($category->name ?? 'N/A') ?></strong></td>
                                <td><?= htmlspecialchars(substr($category->description ?? '', 0, 50)) . (strlen($category->description ?? '') > 50 ? '...' : '') ?></td>
                                <td>$<?= htmlspecialchars(number_format($category->amount ?? 0, 2)) ?></td>
                                <td>
                                    <span class="badge <?= $category->is_active ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $category->is_active ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Action Buttons for Fee Categories -->
                                    <a href="/fees/categories/<?= $category->id ?>/edit" class="btn btn-sm btn-warning" title="Edit Category">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <!-- @TODO: Add Delete form for Fee Categories -->
                                    <form action="/fees/categories/<?= $category->id ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure? Deleting a category might affect student fee records.');">
                                        <!-- @TODO: Add CSRF token -->
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Category">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 
    Student Fees Table Card
    بطاقة جدول رسوم الطلاب
-->
<div class="card shadow-sm">
     <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Student Fee Records</h5>
        <!-- @TODO: Add filters (by student, category, status, academic year) -->
        <!-- إضافة فلاتر (حسب الطالب، الفئة، الحالة، السنة الدراسية) -->
    </div>
    <div class="card-body">
         <?php if (empty($studentFees)): ?>
            <div class="alert alert-info">No student fee records found. Assign fees via 'Manage Student Fees'.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student Name</th>
                            <th>Department</th> <!-- القسم -->
                            <th>Fee Category</th>
                            <th>Amount Due</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php foreach ($studentFees as $fee): ?>
                             <?php
                                // PLACEHOLDER: Fetching related data - MUST BE OPTIMIZED IN CONTROLLER
                                // مؤقت: جلب البيانات المرتبطة - يجب تحسينه في الـ CONTROLLER
                                $student = $fee->student();
                                $category = $fee->feeCategory();
                                $department = $student ? $student->department() : null; 
                                $balance = ($fee->amount_due ?? 0) - ($fee->amount_paid ?? 0);
                                
                                $statusClass = 'bg-secondary'; // Default
                                if ($fee->status === 'paid') $statusClass = 'bg-success';
                                elseif ($fee->status === 'pending') $statusClass = 'bg-warning text-dark';
                                elseif ($fee->status === 'overdue') $statusClass = 'bg-danger';
                                elseif ($fee->status === 'cancelled') $statusClass = 'bg-light text-dark';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($student->first_name . ' ' . $student->last_name ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($department->name ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($category->name ?? 'N/A') ?></td>
                                <td>$<?= htmlspecialchars(number_format($fee->amount_due ?? 0, 2)) ?></td>
                                <td>$<?= htmlspecialchars(number_format($fee->amount_paid ?? 0, 2)) ?></td>
                                <td><strong>$<?= htmlspecialchars(number_format($balance, 2)) ?></strong></td>
                                <td><?= htmlspecialchars($fee->due_date ? date('Y-m-d', strtotime($fee->due_date)) : 'N/A') ?></td>
                                <td>
                                    <span class="badge <?= $statusClass ?>">
                                        <?= ucfirst(htmlspecialchars($fee->status ?? 'N/A')) ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Action Buttons for Student Fees -->
                                     <a href="/fees/manage/<?= $fee->id ?>" class="btn btn-sm btn-info" title="View/Manage Payment">
                                        <i class="fas fa-dollar-sign"></i> Manage
                                    </a>
                                     <!-- @TODO: Add Delete form for Student Fee records if applicable -->
                                </td>
                            </tr>
                         <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
             <!-- @TODO: Add Pagination -->
             <!-- إضافة ترقيم الصفحات -->
        <?php endif; ?>
    </div>
</div>

<!-- @TODO: Ensure Font Awesome is included in the main layout for icons -->
<!-- تأكد من تضمين Font Awesome في القالب الرئيسي لعرض الأيقونات -->