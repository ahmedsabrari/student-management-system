📚 Student Management System (SMS)

🧩 Project Overview

Student Management System (SMS) is a lightweight PHP MVC web application designed to manage students, teachers, classes, grades, and attendance efficiently. It's built from scratch using object-oriented principles and follows the Model-View-Controller (MVC) pattern for a clear separation of concerns.

⚙️ Tech Stack

PHP: 8.0+ (OOP)

Database: MySQL

Frontend: Bootstrap 5, HTML5, CSS3, Vanilla JavaScript

Dependency Management: Composer (for autoloading)

Web Server: Apache (with .htaccess for routing) or PHP Built-in Server

Architecture: Custom MVC Framework

📁 Project Structure

student-management-system/
│
├── public/               # Web server root, contains index.php and assets
│   ├── index.php         # --- Main application entry point ---
│   ├── .htaccess         # Apache rewrite rules
│   └── assets/           # CSS, JS, Images
│
├── app/                  # Core application code
│   ├── Core/             # Base classes (App, Controller, Model, Router, etc.)
│   ├── Config/           # Configuration files (database, app, routes, etc.)
│   ├── Controllers/      # Handles user requests and responses
│   ├── Models/           # Interacts with the database
│   ├── Views/            # HTML templates and layouts
│   ├── Services/         # Business logic layer
│   ├── Middleware/       # Request filtering (Auth, CSRF, etc.)
│   ├── Helpers/          # Utility functions and classes
│   └── Traits/           # Reusable code blocks for Models (Timestamps, SoftDeletes)
│
├── storage/              # Non-public files (logs, cache, backups)
│   ├── logs/
│   ├── cache/
│   └── backups/
│
├── sql/                  # Database schema and seed data
│   ├── schema.sql
│   └── seed.sql
│
├── tests/                # Unit and Integration tests (PHPUnit)
│
├── vendor/               # Composer dependencies
│
├── .env                  # --- Environment variables (!!! DO NOT COMMIT !!!) ---
├── .env.example          # Example environment file
├── composer.json         # Composer configuration
├── .gitignore            # Files/folders ignored by Git
└── README.md             # This file


🚀 Installation & Setup

Follow these steps to set up the project locally:

Clone the repository:

git clone [https://github.com/username/student-management-system.git](https://github.com/username/student-management-system.git) 
cd student-management-system


(Replace with the actual repository URL)

Install dependencies: (Make sure you have Composer installed)

composer install 


Configure environment:

Copy the example environment file:

cp .env.example .env


Edit the .env file: Update the database credentials (DB_DATABASE, DB_USERNAME, DB_PASSWORD), APP_URL, and mail settings according to your local setup.

Generate application keys (if needed): Update CSRF_SECRET_KEY with a strong random key. You can generate one using:

php -r "echo base64_encode(random_bytes(32));" 


Set up the database:

Ensure your MySQL server is running.

Create the database specified in your .env file (e.g., student_management_system).

Import the database schema:

mysql -u YOUR_DB_USERNAME -p YOUR_DB_NAME < sql/schema.sql 


(Replace YOUR_DB_USERNAME and YOUR_DB_NAME)

(Optional) Import seed data:

mysql -u YOUR_DB_USERNAME -p YOUR_DB_NAME < sql/seed.sql


Set file permissions: Ensure the storage/ directory and its subdirectories (logs, cache, backups) are writable by the web server.

chmod -R 775 storage/
# You might need to adjust ownership depending on your server setup
# chown -R www-data:www-data storage/ 


Run the application:

Using PHP built-in server (for development):

php -S localhost:8000 -t public


Access the application at http://localhost:8000.

Using Apache:

Configure a Virtual Host that points its DocumentRoot to the public/ directory of the project.

Ensure mod_rewrite is enabled.

Access the application via the URL defined in your Virtual Host.

🧱 Database

The complete database schema is located in sql/schema.sql.

You can populate the database with sample data using sql/seed.sql.

🔐 Authentication

User registration and login system.

Role-based access control (RBAC) with predefined roles:

Admin: Full system access.

Teacher: Manages assigned classes, students, grades, attendance.

Student: Accesses own profile, courses, grades, assignments.

Staff: Limited administrative tasks (e.g., fee management).

Guardian: Views child's academic progress.

Session management with security features (e.g., Session Regeneration, HttpOnly cookies).

CSRF protection on all state-changing forms.

📚 Features

User Management: Admins can manage users (students, teachers, staff).

Student Management: CRUD operations for student records.

Teacher Management: CRUD operations for teacher records.

Academic Management:

Department Management

Course Management

Class Management (linking courses, teachers, academic years)

Enrollment: Enrolling students into classes.

Grading: Recording and viewing student grades.

Attendance: Taking and tracking student attendance per class.

Assignments: Creating assignments, managing student submissions.

Fees: Managing fee categories and student payments (basic).

Reporting: Generating reports for students, courses, attendance, and financials.

Settings: System-wide configuration and user profile management.

API: Basic RESTful API endpoints for students and courses (requires API token authentication).

🧩 Testing

(This section assumes PHPUnit is set up)

To run the automated tests:

./vendor/bin/phpunit --testdox


🧰 Contribution

Contributions are welcome! Please follow these guidelines:

Branching: Create a new branch for each feature or bug fix (e.g., feature/add-course-search or fix/login-validation).

Pull Requests: Submit Pull Requests (PRs) to the main or develop branch for review. Ensure your code follows the project's coding standards.

Coding Standards: Adhere to PSR-12 coding standards. Use clear variable names and add comments where necessary.

Testing: Add relevant unit or integration tests for new features or bug fixes.

🪪 License

This project is open-source software licensed under the MIT License.