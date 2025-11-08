<?php

declare(strict_types=1); // Enforce strict types

namespace App\Services;

use App\Models\Grade;
use App\Models\Enrollment;
use App\Models\Student; // Needed for student-specific reports
use Exception; // For catching exceptions

/**
 * Grade Service
 *
 * Handles the business logic for managing student grades,
 * including adding, updating, deleting, and retrieving grade information.
 */
class GradeService
{
    /**
     * @var Grade The Grade model instance.
     */
    protected Grade $gradeModel;

    /**
     * @var Enrollment The Enrollment model instance.
     */
    protected Enrollment $enrollmentModel;

    /**
     * GradeService constructor.
     * Initializes the necessary models.
     */
    public function __construct()
    {
        $this->gradeModel = new Grade();
        $this->enrollmentModel = new Enrollment();
    }

    /**
     * Get all grade records, potentially joined with other details.
     * جلب جميع سجلات الدرجات، ربما مع تفاصيل مرتبطة.
     *
     * @param array $filters Optional filters.
     * @return array An array of Grade objects or arrays with joined data.
     */
    public function getAllGrades(array $filters = []): array
    {
        // @TODO: Implement filtering logic.
        // @TODO: Implement joining logic for a comprehensive report.
        // A dedicated method in GradeModel like `getAllGradeDetails()` is preferred.
        // (يفضل وجود دالة مخصصة في GradeModel لجلب التفاصيل المرتبطة)

        // Basic fetch for now
        // جلب أساسي حاليًا
        return $this->gradeModel->all();
    }

    /**
     * Find a specific grade record by its ID.
     * البحث عن سجل درجة محدد بواسطة ID الخاص به.
     *
     * @param int|string $id The grade ID.
     * @return object|false The Grade object or false if not found.
     */
    public function getGradeById($id): object|false
    {
        return $this->gradeModel->find($id);
    }

    /**
     * Find the grade record associated with a specific enrollment.
     * البحث عن سجل الدرجة المرتبط بتسجيل محدد.
     *
     * @param int|string $enrollmentId The enrollment ID.
     * @return object|false The Grade object or false if not found.
     */
    public function getGradeByEnrollment($enrollmentId): object|false
    {
        $sql = "SELECT * FROM {$this->gradeModel->getTable()} WHERE enrollment_id = ? LIMIT 1";
        return $this->gradeModel->query($sql, [$enrollmentId])->fetch();
    }

    /**
     * Add or update a grade for a specific enrollment.
     * إضافة أو تحديث درجة لتسجيل محدد.
     *
     * Performs an "upsert" operation.
     * يقوم بعملية "تحديث أو إدراج".
     *
     * @param int|string $enrollmentId The ID of the enrollment.
     * @param array $gradeData Associative array of grade data (e.g., ['grade' => 'A', 'grade_points' => 4.0]).
     * @return object|false The created or updated Grade object, or false on failure.
     */
    public function addOrUpdateGrade($enrollmentId, array $gradeData): object|false
    {
        // 1. Validate inputs (basic check, Validator helper is better)
        // ١. التحقق من المدخلات (تحقق أساسي، Validator helper أفضل)
        if (empty($enrollmentId) || empty($gradeData)) {
             // error_log("Grade update failed: Enrollment ID or grade data missing.");
            return false;
        }

        // 2. Check if the enrollment exists
        // ٢. التحقق من وجود التسجيل
        $enrollment = $this->enrollmentModel->find($enrollmentId);
        if (!$enrollment) {
             // error_log("Grade update failed: Enrollment with ID {$enrollmentId} not found.");
            return false;
        }

        // 3. Find if a grade already exists for this enrollment
        // ٣. البحث عما إذا كانت هناك درجة موجودة بالفعل لهذا التسجيل
        $existingGrade = $this->getGradeByEnrollment($enrollmentId);

        // Prepare data (ensure enrollment_id is correct, set timestamp)
        // تحضير البيانات (التأكد من صحة enrollment_id، تعيين الطابع الزمني)
        $dataToSave = $gradeData;
        $dataToSave['enrollment_id'] = $enrollmentId; // Ensure it's set
        $dataToSave['recorded_at'] = date('Y-m-d H:i:s'); // Update timestamp

        try {
            if ($existingGrade) {
                // Update existing grade
                // تحديث الدرجة الموجودة
                if ($this->gradeModel->update($existingGrade->id, $dataToSave)) {
                    return $this->gradeModel->find($existingGrade->id); // Return updated
                }
            } else {
                // Create new grade
                // إنشاء درجة جديدة
                $newGradeId = $this->gradeModel->create($dataToSave);
                if ($newGradeId) {
                    return $this->gradeModel->find($newGradeId); // Return new
                }
            }
             // @codeCoverageIgnoreStart
             return false; // Create or update failed
             // @codeCoverageIgnoreEnd
             // @codeCoverageIgnoreStart
        } catch (Exception $e) {
             // error_log("Grade save database error for enrollment {$enrollmentId}: " . $e->getMessage());
             return false;
        }
         // @codeCoverageIgnoreEnd
    }

    /**
     * Delete a specific grade record.
     * حذف سجل درجة محدد.
     *
     * @param int|string $gradeId The ID of the grade record to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteGrade($gradeId): bool
    {
        $grade = $this->gradeModel->find($gradeId);
        if (!$grade) {
            return false; // Not found
        }
        return $this->gradeModel->delete($gradeId);
    }

    /**
     * Get all grades for a specific student.
     * الحصول على جميع درجات طالب محدد.
     *
     * @param int|string $studentId The ID of the student.
     * @return array An array of Grade objects (potentially joined with course info).
     */
    public function getStudentGrades($studentId): array
    {
        // @TODO: Implement a more efficient query joining grades, enrollments, classes, courses.
        // تنفيذ استعلام أكثر كفاءة يربط الجداول اللازمة.
        $studentModel = new Student(); // Assuming Student model exists
        $student = $studentModel->find($studentId);
        if ($student && method_exists($student, 'grades')) {
             return $student->grades(); // Use the relationship defined in Student model
        }
        return []; // Return empty if student not found or relationship doesn't exist
    }

    /**
     * Generate a full grade report (potentially filtered).
     * إنشاء تقرير درجات كامل (ربما مفلتر).
     *
     * @param array $filters Optional filters.
     * @return array Report data (likely requires complex joins and calculations).
     */
    public function getFullGradeReport(array $filters = []): array
    {
        // @TODO: This is complex business logic. Implement robust query here or in GradeModel.
        // This query needs to join grades, enrollments, students, classes, courses
        // and potentially calculate GPA per student or per course.
        // (هذا منطق عمل معقد. نفذ استعلامًا قويًا هنا أو في GradeModel.
        // يحتاج هذا الاستعلام إلى ربط جداول متعددة وربما حساب المعدل التراكمي.)

        // Example placeholder structure:
        // هيكل مثال مؤقت:
        $sql = "SELECT 
                    s.student_number, 
                    s.first_name, 
                    s.last_name, 
                    co.name as course_name, 
                    g.grade, 
                    g.grade_points,
                    cl.semester,
                    ay.name as academic_year
                FROM grades g
                JOIN enrollments e ON g.enrollment_id = e.id
                JOIN students s ON e.student_id = s.id
                JOIN classes cl ON e.class_id = cl.id
                JOIN courses co ON cl.course_id = co.id
                LEFT JOIN academic_years ay ON cl.academic_year_id = ay.id
                WHERE 1=1 "; // Add filters here
                
        // Add filter logic based on $filters array...

        $sql .= " ORDER BY s.last_name, s.first_name, co.name";
        
        return $this->gradeModel->query($sql)->fetchAll(\PDO::FETCH_ASSOC); // Fetch as associative array
    }
}
