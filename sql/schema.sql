-- ==========================================================
-- DATABASE: student_management_system
-- Author: ChatGPT (Architectural Edition)
-- ==========================================================
CREATE DATABASE IF NOT EXISTS student_management_system
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE student_management_system;

-- ==========================================
-- 1. ROLES
-- ==========================================
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO roles (name, description) VALUES
('admin', 'Full access to the system'),
('teacher', 'Can manage assigned classes and students'),
('staff', 'Limited administrative privileges'),
('student', 'Student access to courses and grades');

-- ==========================================
-- 2. USERS
-- ==========================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    avatar VARCHAR(255),
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ==========================================
-- 3. ACADEMIC_YEARS
-- ==========================================
CREATE TABLE academic_years (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_current BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==========================================
-- 4. DEPARTMENTS
-- ==========================================
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==========================================
-- 5. TEACHERS
-- ==========================================
CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    department_id INT NULL,
    hire_date DATE NULL,
    phone VARCHAR(20) NULL,
    address VARCHAR(255) NULL,
    qualifications TEXT NULL,
    specialization VARCHAR(100) NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- 6. STUDENTS
-- ==========================================
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    student_number VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    gender ENUM('male','female','other') NOT NULL,
    date_of_birth DATE NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20) NULL,
    address VARCHAR(255) NULL,
    enrollment_date DATE DEFAULT (CURRENT_DATE),
    department_id INT NULL,
    status ENUM('active','graduated','suspended','dropped') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- 7. GUARDIANS
-- ==========================================
CREATE TABLE guardians (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    relationship ENUM('parent','guardian','other') NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================
-- 8. COURSES
-- ==========================================
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    credit_hours INT DEFAULT 3,
    department_id INT NULL,
    teacher_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id)
        ON DELETE SET NULL,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- 9. CLASSES
-- ==========================================
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    course_id INT NOT NULL,
    teacher_id INT NULL,
    academic_year_id INT NULL,
    schedule VARCHAR(100) NULL,
    semester ENUM('Spring','Summer','Fall','Winter') DEFAULT 'Fall',
    year YEAR DEFAULT YEAR(CURRENT_DATE),
    max_students INT DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id)
        ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
        ON DELETE SET NULL,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- 10. ENROLLMENTS
-- ==========================================
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    enrollment_date DATE DEFAULT (CURRENT_DATE),
    status ENUM('enrolled','completed','dropped') DEFAULT 'enrolled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id)
        ON DELETE CASCADE,
    UNIQUE (student_id, class_id)
) ENGINE=InnoDB;

-- ==========================================
-- 11. GRADES
-- ==========================================
CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_id INT NOT NULL,
    grade VARCHAR(5) NULL,
    grade_points DECIMAL(3,2) NULL,
    remarks VARCHAR(255) NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================
-- 12. ATTENDANCE
-- ==========================================
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present','absent','late','excused') DEFAULT 'present',
    note VARCHAR(255) NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================
-- 13. FEE_CATEGORIES
-- ==========================================
CREATE TABLE fee_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    amount DECIMAL(10,2) NOT NULL,
    academic_year_id INT NULL,
    department_id INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id)
        ON DELETE SET NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- 14. STUDENT_FEES
-- ==========================================
CREATE TABLE student_fees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    fee_category_id INT NOT NULL,
    academic_year_id INT NULL,
    amount_due DECIMAL(10,2),
    amount_paid DECIMAL(10,2) DEFAULT 0,
    due_date DATE,
    paid_date DATE NULL,
    status ENUM('pending','paid','overdue','cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50) NULL,
    transaction_id VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE CASCADE,
    FOREIGN KEY (fee_category_id) REFERENCES fee_categories(id)
        ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- 15. SETTINGS
-- ==========================================
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(100) UNIQUE NOT NULL,
    value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO settings (key_name, value) VALUES
('site_name', 'Student Management System'),
('site_email', 'admin@school.edu'),
('default_language', 'en'),
('grade_scale', '4.0'),
('max_attendance_days', '180');

-- ==========================================================
-- 16. PERMISSIONS
-- ==========================================================
CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================================
-- 17. NOTIFICATIONS
-- ==========================================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info','warning','success','error') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================================
-- 18. MESSAGES
-- ==========================================================
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    subject VARCHAR(150),
    body TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    parent_message_id INT NULL,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_message_id) REFERENCES messages(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================================
-- 19. ASSIGNMENTS
-- ==========================================================
CREATE TABLE assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    due_date DATE,
    max_points DECIMAL(5,2) DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    CONSTRAINT chk_due_date CHECK (due_date > created_at)
) ENGINE=InnoDB;

CREATE TABLE assignment_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    grade VARCHAR(5),
    points_earned DECIMAL(5,2),
    file_path VARCHAR(255),
    submission_text TEXT,
    feedback TEXT,
    status ENUM('submitted','graded','late') DEFAULT 'submitted',
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_assignment_student (assignment_id, student_id)
) ENGINE=InnoDB;

-- ==========================================================
-- 20. LOGS
-- ==========================================================
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100),
    description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================================
-- 21. TIMETABLES
-- ==========================================================
CREATE TABLE timetables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    day_of_week ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
    start_time TIME,
    end_time TIME,
    room VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================================
-- 22. DOCUMENTS
-- ==========================================================
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_type ENUM('student','teacher','admin'),
    owner_id INT NOT NULL,
    title VARCHAR(150),
    file_path VARCHAR(255) NOT NULL,
    file_size INT,
    mime_type VARCHAR(100),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
) ENGINE=InnoDB;

-- ==========================================================
-- 23. ANNOUNCEMENTS
-- ==========================================================
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    author_id INT NOT NULL,
    target_audience ENUM('all','students','teachers','staff') DEFAULT 'all',
    department_id INT NULL,
    is_published BOOLEAN DEFAULT FALSE,
    publish_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================================
-- 24. EVENTS
-- ==========================================================
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    location VARCHAR(255),
    organizer_id INT NOT NULL,
    event_type ENUM('academic','social','sports','holiday') DEFAULT 'academic',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================================
-- INDEXES FOR PERFORMANCE
-- ==========================================================
CREATE INDEX idx_students_department ON students(department_id);
CREATE INDEX idx_students_status ON students(status);
CREATE INDEX idx_enrollments_student_class ON enrollments(student_id, class_id);
CREATE INDEX idx_enrollments_status ON enrollments(status);
CREATE INDEX idx_attendance_date ON attendance(date);
CREATE INDEX idx_attendance_enrollment ON attendance(enrollment_id);
CREATE INDEX idx_grades_enrollment ON grades(enrollment_id);
CREATE INDEX idx_classes_academic_year ON classes(academic_year_id);
CREATE INDEX idx_student_fees_status ON student_fees(status);
CREATE INDEX idx_student_fees_due_date ON student_fees(due_date);
CREATE INDEX idx_assignments_class ON assignments(class_id);
CREATE INDEX idx_assignment_submissions_assignment ON assignment_submissions(assignment_id);
CREATE INDEX idx_assignment_submissions_student ON assignment_submissions(student_id);
CREATE INDEX idx_messages_sender ON messages(sender_id);
CREATE INDEX idx_messages_receiver ON messages(receiver_id);
CREATE INDEX idx_notifications_user ON notifications(user_id);
CREATE INDEX idx_notifications_read ON notifications(is_read);
CREATE INDEX idx_logs_user ON logs(user_id);
CREATE INDEX idx_logs_created_at ON logs(created_at);

-- ==========================================================
-- SAMPLE DATA (Optional - for testing)
-- ==========================================================
INSERT INTO academic_years (name, start_date, end_date, is_current) VALUES
('2023-2024', '2023-09-01', '2024-06-30', TRUE),
('2024-2025', '2024-09-01', '2025-06-30', FALSE);

INSERT INTO departments (name, description) VALUES
('Computer Science', 'Department of Computer Science and Engineering'),
('Mathematics', 'Department of Mathematics and Statistics'),
('Physics', 'Department of Physics and Astronomy'),
('English', 'Department of English Literature');

-- ==========================================================
-- END OF COMPLETE DATABASE SCHEMA
-- ==========================================================
