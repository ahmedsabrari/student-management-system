USE student_management_system;

-- ==========================================
-- 16. PERMISSIONS & ROLE_PERMISSIONS
-- (Assuming roles 1=admin, 2=teacher, 3=staff, 4=student exist)
-- ==========================================
INSERT INTO permissions (name, description) VALUES
('settings.manage', 'Can manage global system settings'),
('users.manage', 'Can create, edit, delete any user'),
('students.manage', 'Can create, edit, delete student records'),
('students.view', 'Can view student records'),
('teachers.manage', 'Can create, edit, delete teacher records'),
('courses.manage', 'Can create, edit, delete courses and classes'),
('enroll.manage', 'Can enroll students in classes'),
('grades.manage', 'Can assign and update student grades'),
('attendance.manage', 'Can take and manage student attendance'),
('reports.view.academic', 'Can view academic reports'),
('reports.view.financial', 'Can view financial reports'),
('fees.manage', 'Can manage fee categories and student payments'),
('assignments.manage', 'Can create and grade assignments');

-- Assign permissions to roles
-- 1: Admin (Gets all key permissions)
INSERT INTO role_permissions (role_id, permission_id) VALUES
(1, (SELECT id FROM permissions WHERE name = 'settings.manage')),
(1, (SELECT id FROM permissions WHERE name = 'users.manage')),
(1, (SELECT id FROM permissions WHERE name = 'students.manage')),
(1, (SELECT id FROM permissions WHERE name = 'students.view')),
(1, (SELECT id FROM permissions WHERE name = 'teachers.manage')),
(1, (SELECT id FROM permissions WHERE name = 'courses.manage')),
(1, (SELECT id FROM permissions WHERE name = 'enroll.manage')),
(1, (SELECT id FROM permissions WHERE name = 'grades.manage')),
(1, (SELECT id FROM permissions WHERE name = 'attendance.manage')),
(1, (SELECT id FROM permissions WHERE name = 'reports.view.academic')),
(1, (SELECT id FROM permissions WHERE name = 'reports.view.financial')),
(1, (SELECT id FROM permissions WHERE name = 'fees.manage')),
(1, (SELECT id FROM permissions WHERE name = 'assignments.manage'));

-- 2: Teacher (Gets academic management permissions)
INSERT INTO role_permissions (role_id, permission_id) VALUES
(2, (SELECT id FROM permissions WHERE name = 'students.view')),
(2, (SELECT id FROM permissions WHERE name = 'grades.manage')),
(2, (SELECT id FROM permissions WHERE name = 'attendance.manage')),
(2, (SELECT id FROM permissions WHERE name = 'assignments.manage')),
(2, (SELECT id FROM permissions WHERE name = 'reports.view.academic'));

-- 3: Staff (Gets financial and student management permissions)
INSERT INTO role_permissions (role_id, permission_id) VALUES
(3, (SELECT id FROM permissions WHERE name = 'students.manage')),
(3, (SELECT id FROM permissions WHERE name = 'students.view')),
(3, (SELECT id FROM permissions WHERE name = 'enroll.manage')),
(3, (SELECT id FROM permissions WHERE name = 'fees.manage')),
(3, (SELECT id FROM permissions WHERE name = 'reports.view.financial'));

-- 4: Student (Gets basic view permissions)
INSERT INTO role_permissions (role_id, permission_id) VALUES
(4, (SELECT id FROM permissions WHERE name = 'students.view'));


-- ==========================================
-- 2. USERS
-- (Default password for all is 'password' hashed)
-- ==========================================
INSERT INTO users (role_id, username, email, password, full_name, status) VALUES
(1, 'admin', 'admin@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'active'),
(2, 'teacher_smith', 'smith@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Emily Smith', 'active'),
(2, 'teacher_brown', 'brown@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Prof. David Brown', 'active'),
(4, 'student_doe', 'john.doe@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'active'),
(4, 'student_jane', 'jane.doe@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Doe', 'active'),
(3, 'staff_jones', 'jones@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah Jones (Staff)', 'active');

-- ==========================================
-- 5. TEACHERS
-- (Uses User IDs 2 and 3; Department IDs 1 'CS', 2 'Math')
-- ==========================================
INSERT INTO teachers (user_id, department_id, hire_date, phone, specialization) VALUES
(2, 1, '2018-09-01', '555-1111', 'Data Structures and Algorithms'),
(3, 2, '2020-08-15', '555-2222', 'Calculus and Linear Algebra');

-- ==========================================
-- 6. STUDENTS
-- (Uses User IDs 4 and 5; Department IDs 1 'CS', 2 'Math')
-- ==========================================
INSERT INTO students (user_id, student_number, first_name, last_name, gender, date_of_birth, email, phone, department_id, status) VALUES
(4, 'S1001', 'John', 'Doe', 'male', '2003-05-15', 'john.doe@school.edu', '555-3333', 1, 'active'),
(5, 'S1002', 'Jane', 'Doe', 'female', '2004-02-20', 'jane.doe@school.edu', '555-4444', 2, 'active'),
(NULL, 'S1003', 'Mike', 'Johnson', 'male', '2003-11-10', 'mike.johnson@school.edu', '555-5555', 1, 'active');

-- ==========================================
-- 7. GUARDIANS
-- (Depends on Student IDs 1 and 2)
-- ==========================================
INSERT INTO guardians (student_id, full_name, relationship, email, phone, is_primary) VALUES
(1, 'Richard Doe', 'parent', 'richard.doe@email.com', '555-6666', TRUE),
(2, 'Sarah Doe', 'parent', 'sarah.doe@email.com', '555-7777', TRUE),
(3, 'Mark Johnson', 'guardian', 'mark.johnson@email.com', '555-8888', TRUE);

-- ==========================================
-- 13. FEE_CATEGORIES
-- (Depends on Academic Year ID 1 '2023-2024' and Dept ID 1 'CS')
-- ==========================================
INSERT INTO fee_categories (name, description, amount, academic_year_id, department_id) VALUES
('Annual Tuition 2023-2024', 'Standard annual tuition fee for all students', 5000.00, 1, NULL),
('CS Lab Fee', 'Lab materials fee for Computer Science students', 250.00, 1, 1),
('Math Resource Fee', 'Resource fee for Mathematics students', 100.00, 1, 2);

-- ==========================================
-- 14. STUDENT_FEES
-- (Depends on Student IDs 1, 2, 3 and Fee Category IDs 1, 2, 3)
-- ==========================================
INSERT INTO student_fees (student_id, fee_category_id, academic_year_id, amount_due, amount_paid, due_date, status, payment_method, transaction_id) VALUES
(1, 1, 1, 5000.00, 5000.00, '2023-09-15', 'paid', 'Bank Transfer', 'TX12345'),
(1, 2, 1, 250.00, 250.00, '2023-09-15', 'paid', 'Credit Card', 'TX12346'),
(2, 1, 1, 5000.00, 2500.00, '2023-09-15', 'pending', NULL, NULL),
(2, 3, 1, 100.00, 0.00, '2023-09-15', 'overdue', NULL, NULL),
(3, 1, 1, 5000.00, 0.00, '2023-09-15', 'pending', NULL, NULL);

-- ==========================================
-- 8. COURSES
-- (Depends on Dept IDs 1, 2 and Teacher IDs 1, 2)
-- ==========================================
INSERT INTO courses (code, name, description, credit_hours, department_id, teacher_id) VALUES
('CS101', 'Introduction to Programming', 'Fundamentals of programming using Python.', 3, 1, 1),
('MATH101', 'Calculus I', 'Limits, derivatives, and introduction to integration.', 4, 2, 2),
('CS201', 'Data Structures', 'Advanced data structures and algorithms.', 3, 1, 1);

-- ==========================================
-- 9. CLASSES
-- (Depends on Course IDs 1, 2, 3, Teacher IDs 1, 2, and Academic Year ID 1)
-- ==========================================
INSERT INTO classes (name, course_id, teacher_id, academic_year_id, schedule, semester, year) VALUES
('CS101-A', 1, 1, 1, 'MWF 09:00-09:50', 'Fall', 2023),
('MATH101-B', 2, 2, 1, 'TTh 11:00-12:15', 'Fall', 2023),
('CS201-A', 3, 1, 1, 'TTh 14:00-15:15', 'Spring', 2024);

-- ==========================================
-- 10. ENROLLMENTS
-- (Depends on Student IDs 1, 2, 3 and Class IDs 1, 2, 3)
-- ==========================================
INSERT INTO enrollments (student_id, class_id, status) VALUES
(1, 1, 'enrolled'), -- John Doe in CS101
(1, 3, 'enrolled'), -- John Doe in CS201
(2, 2, 'enrolled'), -- Jane Doe in MATH101
(3, 1, 'enrolled'); -- Mike Johnson in CS101

-- ==========================================
-- 11. GRADES
-- (Depends on Enrollment IDs 1, 2, 3, 4)
-- ==========================================
INSERT INTO grades (enrollment_id, grade, grade_points, remarks) VALUES
(1, 'A-', 3.70, 'Excellent work on assignments.'),
(3, 'B+', 3.30, 'Good participation in class.'),
(4, 'A', 4.00, 'Perfect score on final exam.');
-- Enrollment 2 (John Doe in CS201) has no grade yet

-- ==========================================
-- 12. ATTENDANCE
-- (Depends on Enrollment IDs 1, 3)
-- ==========================================
INSERT INTO attendance (enrollment_id, date, status, note) VALUES
(1, '2023-09-04', 'present', NULL),
(1, '2023-09-06', 'present', NULL),
(1, '2023-09-08', 'absent', 'Sick leave'),
(3, '2023-09-04', 'present', NULL),
(3, '2023-09-06', 'late', 'Arrived 10 minutes late'),
(3, '2023-09-08', 'present', NULL);

-- ==========================================
-- 19. ASSIGNMENTS
-- (Depends on Class IDs 1, 2)
-- ==========================================
INSERT INTO assignments (class_id, title, description, due_date, max_points) VALUES
(1, 'CS101 - Homework 1', 'Write a "Hello World" program.', '2025-11-15', 50),
(2, 'MATH101 - Worksheet 1', 'Complete the derivative problems.', '2025-11-20', 100);
-- ==========================================
-- 19. ASSIGNMENT_SUBMISSIONS
-- (Depends on Assignment IDs 1, 2 and Student IDs 1, 2, 3)
-- ==========================================
INSERT INTO assignment_submissions (assignment_id, student_id, file_path, status, grade, points_earned, feedback) VALUES
(1, 1, '/uploads/assignments/john_doe_hw1.zip', 'graded', 'A', 50, 'Perfect submission.'),
(1, 3, '/uploads/assignments/mike_johnson_hw1.zip', 'graded', 'B', 40, 'Good work, missed one edge case.'),
(2, 2, '/uploads/assignments/jane_doe_ws1.pdf', 'submitted', NULL, NULL, NULL);

-- ==========================================
-- 21. TIMETABLES
-- (Depends on Class IDs 1, 2, 3)
-- ==========================================
INSERT INTO timetables (class_id, day_of_week, start_time, end_time, room) VALUES
(1, 'Monday', '09:00:00', '09:50:00', 'Room 101'),
(1, 'Wednesday', '09:00:00', '09:50:00', 'Room 101'),
(1, 'Friday', '09:00:00', '09:50:00', 'Room 101'),
(2, 'Tuesday', '11:00:00', '12:15:00', 'Room 205'),
(2, 'Thursday', '11:00:00', '12:15:00', 'Room 205'),
(3, 'Tuesday', '14:00:00', '15:15:00', 'Lab 3B');

-- ==========================================
-- 17. NOTIFICATIONS
-- (Depends on User IDs 1-6)
-- ==========================================
INSERT INTO notifications (user_id, title, message, type) VALUES
(4, 'Welcome!', 'Welcome to the Student Management System, John!', 'success'),
(5, 'Fee Overdue', 'Your payment for Math Resource Fee is overdue.', 'warning');


-- ==========================================
-- 20. LOGS
-- (Depends on User IDs 1-6)
-- ==========================================
INSERT INTO logs (user_id, action, description, ip_address) VALUES
(1, 'LOGIN_SUCCESS', 'User admin logged in successfully.', '127.0.0.1'),
(3, 'STUDENT_CREATE', 'User staff_jones created new student Mike Johnson (S1003).', '127.0.0.1');

-- ==========================================
-- 22. DOCUMENTS
-- (Depends on Student ID 1, Teacher ID 2)
-- ==========================================
INSERT INTO documents (owner_type, owner_id, title, file_path, mime_type) VALUES
('student', 1, 'High School Transcript', '/uploads/documents/john_doe_transcript.pdf', 'application/pdf'),
('teacher', 2, 'Calculus Curriculum', '/uploads/documents/math101_curriculum.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

-- ==========================================================
-- END OF SEED DATA
-- ==========================================================



-- ==========================================
-- 23. ANNOUNCEMENTS
-- (Depends on User ID 1 'admin', Dept ID 1 'CS')
-- ==========================================
INSERT INTO announcements (title, content, author_id, target_audience, is_published, publish_at) VALUES
('Welcome Back Students!', 'Welcome to the 2023-2024 academic year. We wish you success!', 1, 'all', TRUE, '2023-09-01 08:00:00'),
('CS Department Meeting', 'All Computer Science faculty are required to attend.', 1, 'teachers', TRUE, '2023-09-05 10:00:00');

-- ==========================================
-- 24. EVENTS
-- (Depends on User ID 1 'admin')
-- ==========================================
INSERT INTO events (title, description, event_date, start_time, location, organizer_id, event_type) VALUES
('Fall Semester Start', 'First day of classes for the Fall 2023 semester.', '2023-09-04', '08:00:00', 'Campus-wide', 1, 'academic'),
('Midterm Exams', 'Midterm examination period.', '2023-10-23', '09:00:00', 'Campus-wide', 1, 'academic');

-- ==========================================
-- 18. MESSAGES
-- (Depends on User IDs 1-6)
-- ==========================================
INSERT INTO messages (sender_id, receiver_id, subject, body, is_read) VALUES
(2, 4, 'Regarding CS101 Homework 1', 'John, your submission was excellent. Well done.', 0),
(4, 2, 'Re: Regarding CS101 Homework 1', 'Thank you, Dr. Smith!', 0, 1); -- Reply to message 1




-- * **المدير (Admin):**
--     * **البريد الإلكتروني:** `admin@school.edu`
--     * **كلمة المرور:** `password`

-- * **المعلم (Teacher):**
--     * **البريد الإلكتروني:** `smith@school.edu` (Dr. Emily Smith)
--     * **كلمة المرور:** `password`

-- * **المعلم (Teacher):**
--     * **البريد الإلكتروني:** `brown@school.edu` (Prof. David Brown)
--     * **كلمة المرور:** `password`

-- * **الطالب (Student):**
--     * **البريد الإلكتروني:** `john.doe@school.edu` (John Doe)
--     * **كلمة المرور:** `password`

-- * **الطالب (Student):**
--     * **البريد الإلكتروني:** `jane.doe@school.edu` (Jane Doe)
--     * **كلمة المرور:** `password`

-- * **الموظف (Staff):**
--     * **البريد الإلكتروني:** `jones@school.edu` (Sarah Jones)
--     * **كلمة المرور:** `password`