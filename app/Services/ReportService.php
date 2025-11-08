<?php

declare(strict_types=1); // Enforce strict types

namespace App\Services;

// Import necessary models
use App\Models\Student;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\StudentFee;
use App\Models\Department;
use App\Core\Model; // Access base query method if needed

/**
 * Report Service
 *
 * Handles the business logic for generating various complex reports
 * by querying and aggregating data from multiple models.
 */
class ReportService
{
    // No constructor needed for now as methods will instantiate models directly
    // or use static methods if available.

    /**
     * Generates the data for the Students Report.
     * إنشاء بيانات تقرير الطلاب.
     *
     * @param array $filters Filters like ['department_id' => 1, 'status' => 'active'].
     * @return array An array containing report data and total active students.
     * Report data includes student details and department name.
     * GPA and Total Courses require more complex calculation logic (@TODO).
     */
    public function getStudentReport(array $filters = []): array
    {
        $studentModel = new Student();
        $baseSql = "SELECT s.*, d.name as department_name 
                    FROM {$studentModel->getTable()} s 
                    LEFT JOIN departments d ON s.department_id = d.id 
                    WHERE 1=1"; // Start WHERE clause
        
        $params = [];

        // Apply filters
        // تطبيق الفلاتر
        if (!empty($filters['department_id'])) {
            $baseSql .= " AND s.department_id = ?";
            $params[] = $filters['department_id'];
        }
        if (!empty($filters['status'])) {
            $baseSql .= " AND s.status = ?";
            $params[] = $filters['status'];
        }
        
        // Exclude soft-deleted students if applicable
        // استبعاد الطلاب المحذوفين منطقيًا إذا كان منطبقًا
        if (property_exists($studentModel, 'usesSoftDeletes') && $studentModel->usesSoftDeletes === true && defined(get_class($studentModel).'::DELETED_AT')) {
             $deletedAtColumn = $studentModel::DELETED_AT;
             $baseSql .= " AND s.{$deletedAtColumn} IS NULL";
        }

        $orderBy = " ORDER BY s.last_name, s.first_name";
        
        $reportData = $studentModel->query($baseSql . $orderBy, $params)->fetchAll(\PDO::FETCH_ASSOC);

        // @TODO: Calculate 'total_courses' and 'gpa' for each student.
        // This requires additional complex queries or logic iterating through enrollments/grades.
        // حساب 'total_courses' و 'gpa' لكل طالب (يتطلب منطقًا إضافيًا).
        foreach ($reportData as &$student) {
            $student['total_courses'] = '-'; // Placeholder
            $student['gpa'] = '-'; // Placeholder
        }
        unset($student); // Unset reference

        // Calculate total active students (example)
        // حساب إجمالي الطلاب النشطين (مثال)
        $activeFilters = $filters; // Copy filters
        $activeFilters['status'] = 'active'; // Force status to active
        $activeCountData = $this->getStudentReport($activeFilters); // Recursively call (or better: create a dedicated count method)
        $totalActiveStudents = count($activeCountData['reportData'] ?? []);


        return [
            'reportData' => $reportData,
            'totalActiveStudents' => $totalActiveStudents,
             // Pass back other needed data like departments for filters
             'departments' => (new Department())->all()
        ];
    }

    /**
     * Generates the data for the Courses Report.
     * إنشاء بيانات تقرير المقررات.
     *
     * @param array $filters Optional filters.
     * @return array An array containing report data and total courses count.
     * Report data includes course details, department, teacher, enrollment count.
     * Average grade requires more complex calculation logic (@TODO).
     */
    public function getCourseReport(array $filters = []): array
    {
        $courseModel = new Course();
        // This query joins courses with departments, teachers (via users), and counts enrollments.
        // يقوم هذا الاستعلام بربط المقررات مع الأقسام، المعلمين (عبر المستخدمين)، ويحسب عدد التسجيلات.
        $sql = "SELECT 
                    c.id, c.code, c.name, 
                    d.name as department_name, 
                    u.full_name as teacher_name, 
                    COUNT(DISTINCT e.id) as enrollment_count
                FROM {$courseModel->getTable()} c
                LEFT JOIN departments d ON c.department_id = d.id
                LEFT JOIN teachers t ON c.teacher_id = t.id
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN classes cl ON c.id = cl.course_id
                LEFT JOIN enrollments e ON cl.id = e.class_id
                WHERE 1=1"; // Start WHERE clause
        
        // Add soft delete check for courses if applicable
        // إضافة شرط الحذف المنطقي للمقررات إذا كان منطبقًا
        if (property_exists($courseModel, 'usesSoftDeletes') && $courseModel->usesSoftDeletes === true && defined(get_class($courseModel).'::DELETED_AT')) {
             $deletedAtColumn = $courseModel::DELETED_AT;
             $sql .= " AND c.{$deletedAtColumn} IS NULL";
        }
        
        $params = [];
        // @TODO: Add filtering logic based on $filters (e.g., by department_id)
        // إضافة منطق الفلترة بناءً على $filters
        
        $sql .= " GROUP BY c.id, c.code, c.name, d.name, u.full_name
                  ORDER BY c.name";
                  
        $reportData = $courseModel->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
        
        // @TODO: Calculate 'average_grade' for each course.
        // Requires querying grades related to enrollments for each course.
        // حساب 'average_grade' لكل مقرر (يتطلب استعلام الدرجات المرتبطة).
        foreach ($reportData as &$course) {
             $course['average_grade'] = '-'; // Placeholder
        }
        unset($course);

        $totalCourses = count($reportData);

        return [
            'reportData' => $reportData,
            'totalCourses' => $totalCourses,
             // Pass back filter data if needed
        ];
    }

    /**
     * Generates the data for the Attendance Report.
     * إنشاء بيانات تقرير الحضور.
     *
     * @param array $filters Filters like ['class_id' => 1, 'date_from' => '...', 'date_to' => '...'].
     * @return array An array containing detailed attendance records with calculated percentages.
     */
    public function getAttendanceReport(array $filters = []): array
    {
        $attendanceModel = new Attendance();
        // This query needs to join attendance, enrollments, students, classes
        // يحتاج هذا الاستعلام إلى ربط جداول attendance, enrollments, students, classes
        $sql = "SELECT 
                    a.date, a.status, 
                    s.student_number, s.first_name, s.last_name,
                    cl.id as class_id, cl.name as class_name
                    -- Possibly department name if needed: d.name as department_name
                FROM {$attendanceModel->getTable()} a
                JOIN enrollments e ON a.enrollment_id = e.id
                JOIN students s ON e.student_id = s.id
                JOIN classes cl ON e.class_id = cl.id
                -- LEFT JOIN departments d ON s.department_id = d.id 
                WHERE 1=1 ";
                
        $params = [];

        // Apply filters
        // تطبيق الفلاتر
        if (!empty($filters['class_id'])) {
            $sql .= " AND e.class_id = ?";
            $params[] = $filters['class_id'];
        }
        if (!empty($filters['date_from'])) {
             $sql .= " AND a.date >= ?";
             $params[] = $filters['date_from'];
        }
         if (!empty($filters['date_to'])) {
             $sql .= " AND a.date <= ?";
             $params[] = $filters['date_to'];
        }
        // @TODO: Add student filter if needed

        $sql .= " ORDER BY a.date, s.last_name, s.first_name";

        $detailedRecords = $attendanceModel->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
        
        // --- Calculate Summary Statistics (Example Logic) ---
        // --- حساب الإحصائيات الملخصة (منطق مثال) ---
        $summary = [];
        foreach($detailedRecords as $record) {
            $studentId = $record['student_number']; // Use student_number as key for simplicity
            if(!isset($summary[$studentId])) {
                $summary[$studentId] = [
                    'student_number' => $record['student_number'],
                    'full_name' => $record['full_name'],
                    'class_name' => $record['class_name'], // Assumes filter by class or handles multiple classes
                    // 'department_name' => $record['department_name'],
                    'total_days' => 0, 'present' => 0, 'absent' => 0, 'late' => 0
                ];
            }
            $summary[$studentId]['total_days']++;
            if ($record['status'] === 'present') $summary[$studentId]['present']++;
            elseif ($record['status'] === 'absent') $summary[$studentId]['absent']++;
            elseif ($record['status'] === 'late') $summary[$studentId]['late']++;
        }
        
        // Calculate percentages
        // حساب النسب المئوية
         foreach ($summary as &$row) {
             $presentOrLate = $row['present'] + $row['late'];
             $row['percentage'] = ($row['total_days'] > 0) ? round(($presentOrLate / $row['total_days']) * 100) : 0;
         }
         unset($row);

        return [
            'reportData' => array_values($summary), // Return indexed array for view
             // Pass back filter data
             'filters' => $filters,
             'classes' => (new \App\Models\ClassModel())->all() // For filter dropdown
        ];
    }

    /**
     * Generates the data for the Financial Report.
     * إنشاء بيانات التقرير المالي.
     *
     * @param array $filters Filters like ['academic_year_id' => 1, 'status' => 'pending'].
     * @return array An array containing detailed student fee records with calculated balances.
     */
    public function getFinancialReport(array $filters = []): array
    {
        $studentFeeModel = new StudentFee();
        // Query joins student_fees with students, fee_categories, and optionally academic_years/departments
        // الاستعلام يربط student_fees مع students, fee_categories, واختياريًا academic_years/departments
        $sql = "SELECT 
                    sf.id as student_fee_id, sf.amount_due, sf.amount_paid, sf.due_date, sf.status,
                    s.id as student_id, s.student_number, s.first_name, s.last_name,
                    fc.name as fee_category_name,
                    ay.name as academic_year_name
                    -- d.name as department_name 
                FROM {$studentFeeModel->getTable()} sf
                JOIN students s ON sf.student_id = s.id
                JOIN fee_categories fc ON sf.fee_category_id = fc.id
                LEFT JOIN academic_years ay ON sf.academic_year_id = ay.id
                -- LEFT JOIN departments d ON s.department_id = d.id 
                WHERE 1=1 ";

        $params = [];

        // Apply filters
        // تطبيق الفلاتر
        if (!empty($filters['academic_year_id'])) {
            $sql .= " AND sf.academic_year_id = ?";
            $params[] = $filters['academic_year_id'];
        }
        if (!empty($filters['fee_category_id'])) {
            $sql .= " AND sf.fee_category_id = ?";
            $params[] = $filters['fee_category_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND sf.status = ?";
            $params[] = $filters['status'];
        }
         // @TODO: Add student filter if needed

        $sql .= " ORDER BY s.last_name, s.first_name, fc.name";

        $reportData = $studentFeeModel->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);

        // Calculate balance for each row (can also be done in SQL)
        // حساب الرصيد لكل صف (يمكن القيام به في SQL أيضًا)
        foreach ($reportData as &$row) {
            $row['balance'] = ($row['amount_due'] ?? 0) - ($row['amount_paid'] ?? 0);
        }
        unset($row);
        
        // Calculate totals (better done via separate aggregate SQL query for performance)
        // حساب الإجماليات (الأفضل عبر استعلام SQL تجميعي منفصل للأداء)
        $totalDue = array_sum(array_column($reportData, 'amount_due'));
        $totalPaid = array_sum(array_column($reportData, 'amount_paid'));
        $totalBalance = $totalDue - $totalPaid;


        return [
            'reportData' => $reportData,
            'totalDue' => $totalDue,
            'totalPaid' => $totalPaid,
            'totalBalance' => $totalBalance,
            // Pass back filter data
            'filters' => $filters,
            'academic_years' => (new \App\Models\AcademicYear())->getAll(),
            'feeCategories' => (new \App\Models\FeeCategory())->getAllActive()
        ];
    }
}
