<?php

declare(strict_types=1); // Enforce strict types

namespace App\Services;

use App\Models\Student;
use App\Models\Guardian;
use App\Models\User; // Needed if creating associated user accounts
use App\Core\Database; // Needed for transactions
use Exception; // For catching exceptions during transaction

/**
 * Student Service
 *
 * Handles the business logic for managing student records,
 * including related entities like guardians and potentially user accounts.
 */
class StudentService
{
    /**
     * @var Student The Student model instance.
     */
    protected Student $studentModel;

    /**
     * @var Guardian The Guardian model instance.
     */
    protected Guardian $guardianModel;
    
    /**
     * @var User The User model instance (optional, if linking students to users).
     */
    protected ?User $userModel;

    /**
     * @var \PDO The PDO database connection.
     */
    protected \PDO $db;


    /**
     * StudentService constructor.
     * Initializes the necessary models and database connection.
     */
    public function __construct()
    {
        $this->studentModel = new Student();
        $this->guardianModel = new Guardian();
        $this->userModel = new User(); // Instantiate if needed
        // Get PDO connection for transactions
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all students (excluding soft-deleted if applicable).
     * جلب جميع الطلاب (باستثناء المحذوفين منطقيًا إن وجد).
     *
     * @param array $filters Optional filters (e.g., ['department_id' => 1]).
     * @return array An array of Student objects or arrays.
     */
    public function getAllStudents(array $filters = []): array
    {
        // @TODO: Implement filtering logic based on $filters array
        // تنفيذ منطق الفلترة بناءً على مصفوفة $filters
        if (!empty($filters)) {
             // Example basic filtering - needs more robust implementation
             // مثال فلترة بسيط - يحتاج لتنفيذ أكثر قوة
             $sql = "SELECT * FROM {$this->studentModel->getTable()} WHERE deleted_at IS NULL"; // Assuming soft deletes
             $params = [];
             if(isset($filters['department_id'])) {
                 $sql .= " AND department_id = ?";
                 $params[] = $filters['department_id'];
             }
              if(isset($filters['status'])) {
                 $sql .= " AND status = ?";
                 $params[] = $filters['status'];
             }
             return $this->studentModel->query($sql, $params)->fetchAll();
        }
        
        // If the model uses SoftDeletes trait, ->all() already filters
        // إذا كان النموذج يستخدم SoftDeletes trait، فإن ->all() تقوم بالفلترة بالفعل
        return $this->studentModel->all(); 
    }

    /**
     * Find a specific student by their ID.
     * البحث عن طالب محدد بواسطة ID الخاص به.
     *
     * @param int|string $id The student ID.
     * @return object|false The Student object or false if not found (or soft-deleted).
     */
    public function findStudentById($id): object|false
    {
        // If the model uses SoftDeletes trait, ->find() already filters
        // إذا كان النموذج يستخدم SoftDeletes trait، فإن ->find() تقوم بالفلترة بالفعل
        return $this->studentModel->find($id);
    }

    /**
     * Create a new student record, optionally with a primary guardian.
     * إنشاء سجل طالب جديد، اختياريًا مع ولي أمر أساسي.
     * Uses a database transaction for atomicity.
     * يستخدم تعامل قاعدة البيانات للذرية.
     *
     * @param array $studentData Associative array of student data.
     * @param array|null $guardianData Optional associative array of guardian data.
     * @return object|false The newly created Student object or false on failure.
     */
    public function createStudent(array $studentData, ?array $guardianData = null): object|false
    {
        // Basic validation placeholder (use Validator helper in Controller before calling this)
        // تحقق أساسي مؤقت (استخدم Validator helper في الـ Controller قبل استدعاء هذه الدالة)
        if (empty($studentData['first_name']) || empty($studentData['last_name']) || empty($studentData['student_number'])) {
            // error_log("Student creation failed: Missing required fields.");
            return false;
        }
        
        // @TODO: Add logic to create an associated User account if user_id is intended

        try {
            $this->db->beginTransaction();

            // 1. Create the student record
            // ١. إنشاء سجل الطالب
            $newStudentId = $this->studentModel->create($studentData);

            if (!$newStudentId) {
                // @codeCoverageIgnoreStart
                throw new Exception("Failed to create student record in database.");
                // @codeCoverageIgnoreEnd
            }

            // 2. If guardian data is provided, create the guardian record
            // ٢. إذا تم توفير بيانات ولي الأمر، قم بإنشاء سجل ولي الأمر
            if (!empty($guardianData)) {
                // Ensure required guardian fields are present
                // التأكد من وجود حقول ولي الأمر المطلوبة
                if(empty($guardianData['full_name']) || empty($guardianData['relationship'])){
                     // @codeCoverageIgnoreStart
                     throw new Exception("Guardian creation failed: Missing required fields.");
                     // @codeCoverageIgnoreEnd
                }
                $guardianData['student_id'] = $newStudentId;
                // Set as primary guardian if not specified
                // تعيينه كولي أمر أساسي إذا لم يتم تحديده
                $guardianData['is_primary'] = $guardianData['is_primary'] ?? true; 
                
                $newGuardianId = $this->guardianModel->create($guardianData);
                if (!$newGuardianId) {
                     // @codeCoverageIgnoreStart
                    throw new Exception("Failed to create guardian record after student creation.");
                     // @codeCoverageIgnoreEnd
                }
            }

            $this->db->commit();
            
            // Return the newly created student object
            // إرجاع كائن الطالب الذي تم إنشاؤه حديثًا
            return $this->studentModel->find($newStudentId);

        } catch (Exception $e) {
            $this->db->rollBack();
            // Log the detailed error
            // تسجيل الخطأ المفصل
            // error_log("StudentService::createStudent failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing student record.
     * تحديث سجل طالب موجود.
     *
     * @param int|string $id The ID of the student to update.
     * @param array $studentData Associative array of data to update.
     * @return object|false The updated Student object or false on failure.
     */
    public function updateStudent($id, array $studentData): object|false
    {
         // Basic validation placeholder
         // تحقق أساسي مؤقت
        if (empty($studentData)) {
            return false;
        }

        $student = $this->studentModel->find($id);
        if (!$student) {
            return false; // Student not found or soft-deleted
        }
        
        // @TODO: Add logic to update associated User account if applicable

        if ($this->studentModel->update($id, $studentData)) {
            return $this->studentModel->find($id); // Re-fetch the updated record
        } else {
            // Update might return false if no rows were affected (no changes made)
            // قد تُرجع Update القيمة false إذا لم تتأثر أي صفوف (لم يتم إجراء أي تغييرات)
            // Re-fetch to check if it was just no changes vs an error
            // إعادة الجلب للتحقق مما إذا كان السبب هو عدم وجود تغييرات أم خطأ
            return $this->studentModel->find($id); // Return current state
        }
    }

    /**
     * Soft delete a student record (if using SoftDeletes trait).
     * حذف منطقي لسجل طالب (إذا كان يستخدم SoftDeletes trait).
     *
     * @param int|string $id The ID of the student to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteStudent($id): bool
    {
        $student = $this->studentModel->find($id);
        if (!$student) {
            return false; // Already deleted or not found
        }

        // Assumes the Student model uses the SoftDeletes trait which overrides delete()
        // يفترض أن Student model يستخدم SoftDeletes trait الذي يتجاوز delete()
        return $student->delete(); 
    }

    /**
     * Restore a soft-deleted student record (if using SoftDeletes trait).
     * استعادة سجل طالب محذوف منطقيًا (إذا كان يستخدم SoftDeletes trait).
     *
     * @param int|string $id The ID of the student to restore.
     * @return bool True on success, false otherwise.
     */
    public function restoreStudent($id): bool
    {
        // Find the student including trashed ones
        // البحث عن الطالب بما في ذلك المحذوفين منطقيًا
        // @TODO: Ensure Student model has findWithTrashed (or similar) if using SoftDeletes
        // التأكد من أن Student model لديه دالة findWithTrashed (أو ما شابه) إذا كان يستخدم SoftDeletes
        $student = Student::findWithTrashed($id); 
        
        if ($student && method_exists($student, 'restore')) {
            return $student->restore();
        }
        return false;
    }
    
     /**
     * Permanently delete a student record. Use with caution.
     * حذف سجل طالب بشكل دائم. استخدم بحذر.
     *
     * @param int|string $id The ID of the student to delete permanently.
     * @return bool True on success, false otherwise.
     */
    public function forceDeleteStudent($id): bool
    {
         // Find the student including trashed ones
         // البحث عن الطالب بما في ذلك المحذوفين منطقيًا
         $student = Student::findWithTrashed($id); 

        if ($student && method_exists($student, 'forceDelete')) {
            // Assumes Student model uses SoftDeletes trait with forceDelete() method
            // يفترض أن Student model يستخدم SoftDeletes trait مع دالة forceDelete()
            return $student->forceDelete();
        } elseif($student) {
             // If not using SoftDeletes or forceDelete not available, use base model delete
             // إذا لم يكن يستخدم SoftDeletes أو forceDelete غير متاحة، استخدم دالة الحذف الأساسية
             // @codeCoverageIgnoreStart
             return $this->studentModel->delete($id); // This is permanent delete from base model
             // @codeCoverageIgnoreEnd
        }
        return false; // Not found
    }
}
