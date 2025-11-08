<?php

/**
 * Role and Permission Configuration
 *
 * This file defines the roles available in the system and maps specific
 * permissions to those roles. This centralized configuration allows for easy
 * management of user access control throughout the application.
 */
return [

    /**
     * Roles
     * A list of all user roles in the system.
     * The key is the role name (used internally), and the value is a description.
     */
    'roles' => [
        'admin'   => 'Administrator: Full access to all system features.',
        'teacher' => 'Teacher: Can manage assigned classes, students, grades, and attendance.',
        'staff'   => 'Staff: Limited administrative privileges, such as fee management.',
        'student' => 'Student: Access to their own courses, grades, assignments, and profile.',
        'guardian'=> 'Guardian: Access to their child\'s academic information.',
    ],

    /**
     * Permissions
     * Maps permissions to one or more roles.
     * The key is the permission name, and the value is an array of roles
     * that are granted this permission.
     */
    'permissions' => [
        // Student Management
        'students.view'   => ['admin', 'teacher', 'staff'],
        'students.create' => ['admin', 'staff'],
        'students.edit'   => ['admin', 'staff'],
        'students.delete' => ['admin'],

        // Teacher Management
        'teachers.view'   => ['admin', 'staff'],
        'teachers.create' => ['admin'],
        'teachers.edit'   => ['admin'],
        'teachers.delete' => ['admin'],

        // Course & Class Management
        'courses.view'   => ['admin', 'teacher', 'staff', 'student'],
        'courses.manage' => ['admin'], // create, edit, delete
        'classes.view'   => ['admin', 'teacher', 'staff', 'student'],
        'classes.manage' => ['admin', 'teacher'],

        // Academic Management
        'enrollments.manage' => ['admin', 'staff'],
        'grades.manage'      => ['admin', 'teacher'],
        'attendance.manage'  => ['admin', 'teacher'],

        // Assignment Management
        'assignments.view'      => ['admin', 'teacher', 'student'],
        'assignments.manage'    => ['admin', 'teacher'], // create, edit, delete
        'submissions.grade'     => ['admin', 'teacher'],

        // Financial Management
        'fees.manage' => ['admin', 'staff'],
        
        // Reporting
        'reports.view.academic'  => ['admin', 'teacher', 'staff'],
        'reports.view.financial' => ['admin', 'staff'],
        
        // System & Department Management
        'departments.manage' => ['admin'],
        'settings.manage'    => ['admin'],
    ],
];