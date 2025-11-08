<?php

declare(strict_types=1); // Enforce strict types

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\ClassModel;
use Exception; // For catching exceptions

/**
 * Enrollment Service
 *
 * Handles the business logic for enrolling students into classes,
 * managing enrollment statuses, and deleting enrollments.
 */
class EnrollmentService
{
    /**
     * @var Enrollment The Enrollment model instance.
     */
    protected Enrollment $enrollmentModel;

    /**
     * @var Student The Student model instance.
     */
    protected Student $studentModel;

    /**
     * @var ClassModel The Class model instance.
     */
    protected ClassModel $classModel;

    /**
     * EnrollmentService constructor.
     * Initializes the necessary models.
     */
    public function __construct()
    {
        $this->enrollmentModel = new Enrollment();
        $this->studentModel = new Student();
        $this->classModel = new ClassModel();
    }

    /**
     * Get all enrollments, potentially with joined data.
     * جلب جميع التسجيلات، ربما مع بيانات مرتبطة.
     *
     * @param array $filters Optional filters.
     * @param bool $withDetails If true, attempts to join related data.
     * @return array An array of Enrollment objects or arrays.
     */
    public function getAllEnrollments(array $filters = [], bool $withDetails = false): array
    {
        // @TODO: Implement filtering logic.
        // @TODO: Implement joining logic if $withDetails is true.
        // A dedicated method in EnrollmentModel like `getAllEnrollmentDetails()` is preferred.
        // (يفضل وجود دالة مخصصة في EnrollmentModel لجلب التفاصيل المرتبطة)
        
        // Basic fetch for now
        // جلب أساسي حاليًا
        return $this->enrollmentModel->all(); 
    }

    /**
     * Find a specific enrollment by its ID.
     * البحث عن تسجيل محدد بواسطة ID الخاص به.
     *
     * @param int|string $id The enrollment ID.
     * @return object|false The Enrollment object or false if not found.
     */
    public function getEnrollmentById($id): object|false
    {
        return $this->enrollmentModel->find($id);
    }

    /**
     * Enroll a student into a class.
     * تسجيل طالب في فصل دراسي.
     *
     * Performs checks for existence of student/class and prevents duplicates.
     * يقوم بالتحقق من وجود الطالب/الفصل ويمنع التكرار.
     *
     * @param int|string $studentId The ID of the student to enroll.
     * @param int|string $classId The ID of the class to enroll into.
     * @return object|false The newly created Enrollment object or false on failure.
     */
    public function enrollStudent($studentId, $classId): object|false
    {
        // 1. Validate inputs (basic check, Validator helper is better)
        // ١. التحقق من المدخلات (تحقق أساسي، Validator helper أفضل)
        if (empty($studentId) || empty($classId)) {
             // error_log("Enrollment failed: Student ID or Class ID missing.");
            return false;
        }

        // 2. Check if student and class exist
        // ٢. التحقق من وجود الطالب والفصل
        $student = $this->studentModel->find($studentId);
        $class = $this->classModel->find($classId);

        if (!$student) {
             // error_log("Enrollment failed: Student with ID {$studentId} not found.");
             return false;
        }
        if (!$class) {
             // error_log("Enrollment failed: Class with ID {$classId} not found.");
            return false;
        }
        
        // @TODO: Check if class has reached max_students limit

        // 3. Check for duplicate enrollment
        // ٣. التحقق من التسجيل المكرر
        $existing = $this->enrollmentModel->query(
            "SELECT id FROM {$this->enrollmentModel->getTable()} WHERE student_id = ? AND class_id = ?",
            [$studentId, $classId]
        )->fetch();

        if ($existing) {
             // error_log("Enrollment failed: Student {$studentId} already enrolled in class {$classId}.");
             // Optionally return the existing enrollment object if needed
             // return $this->getEnrollmentById($existing->id);
            return false; // Indicate failure due to duplicate
        }

        // 4. Create the enrollment record
        // ٤. إنشاء سجل التسجيل
        $enrollmentData = [
            'student_id' => $studentId,
            'class_id' => $classId,
            'enrollment_date' => date('Y-m-d H:i:s'), // Current date/time
            'status' => 'enrolled', // Default status
        ];

        try {
            $newEnrollmentId = $this->enrollmentModel->create($enrollmentData);
            if ($newEnrollmentId) {
                return $this->enrollmentModel->find($newEnrollmentId);
            }
             // @codeCoverageIgnoreStart
             return false;
             // @codeCoverageIgnoreEnd
             // @codeCoverageIgnoreStart
        } catch (Exception $e) {
             // error_log("Enrollment creation database error: " . $e->getMessage());
             return false;
        }
         // @codeCoverageIgnoreEnd
    }

    /**
     * Update the status or other details of an existing enrollment.
     * تحديث حالة أو تفاصيل أخرى لتسجيل موجود.
     *
     * @param int|string $id The ID of the enrollment to update.
     * @param array $data Associative array of data to update (e.g., ['status' => 'completed']).
     * @return object|false The updated Enrollment object or false on failure.
     */
    public function updateEnrollment($id, array $data): object|false
    {
        // Basic validation
        // تحقق أساسي
        if (empty($data)) {
            return false;
        }
        
        $enrollment = $this->enrollmentModel->find($id);
        if (!$enrollment) {
            return false; // Enrollment not found
        }
        
        // Prevent changing student_id or class_id? (Optional business rule)
        // منع تغيير student_id أو class_id؟ (قاعدة عمل اختيارية)
        // unset($data['student_id'], $data['class_id']); 

        if ($this->enrollmentModel->update($id, $data)) {
            return $this->enrollmentModel->find($id); // Re-fetch updated record
        } else {
             // Return current state if no changes or error
             // إرجاع الحالة الحالية إذا لم يكن هناك تغييرات أو حدث خطأ
            return $this->enrollmentModel->find($id);
        }
    }


    /**
     * Unenroll a student from a class (delete the enrollment record).
     * إلغاء تسجيل طالب من فصل (حذف سجل التسجيل).
     *
     * @param int|string $id The ID of the enrollment to delete.
     * @return bool True on success, false otherwise.
     */
    public function unenrollStudent($id): bool
    {
        $enrollment = $this->enrollmentModel->find($id);
        if (!$enrollment) {
            return false; // Not found
        }

        // @TODO: Add checks? Should grades/attendance be deleted or archived first?
        // إضافة فحوصات؟ هل يجب حذف الدرجات/الحضور أو أرشفتها أولاً؟

        // Uses the base model's delete method
        // يستخدم دالة الحذف الخاصة بالنموذج الأساسي
        return $this->enrollmentModel->delete($id); 
    }

    // @TODO: Add restoreEnrollment() if using SoftDeletes
    // إضافة restoreEnrollment() إذا كنت تستخدم SoftDeletes
}
