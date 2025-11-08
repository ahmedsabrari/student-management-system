<?php

// Note: Standalone function files cannot use declare(strict_types=1); at the top.
// Type hinting is used on function parameters/return types where applicable.

namespace App\Helpers;

use App\Helpers\Session;

/**
 * Checks if a user is currently logged in.
 * التحقق مما إذا كان المستخدم مسجل دخوله حاليًا.
 *
 * @return bool True if the user is logged in, false otherwise.
 */
function isLoggedIn(): bool
{
    return Session::has('user_id');
}

/**
 * Retrieves basic information about the currently logged-in user.
 * استرجاع معلومات أساسية عن المستخدم المسجل دخوله حاليًا.
 *
 * Returns null if the user is not logged in.
 * Excludes sensitive information like passwords.
 * يُرجع null إذا لم يكن المستخدم مسجلًا دخوله. يستثني المعلومات الحساسة.
 *
 * @return array{id: int|string, name: string, role: string}|null An associative array with user data or null.
 */
function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    // Retrieve only necessary and safe data from the session
    // استرجاع البيانات الضرورية والآمنة فقط من الجلسة
    return [
        'id'   => Session::get('user_id'),
        'name' => Session::get('user_name'),
        'role' => Session::get('user_role'),
        // Add other safe-to-expose attributes if needed
    ];
}

/**
 * Gets the role name of the currently logged-in user.
 * الحصول على اسم دور المستخدم المسجل دخوله حاليًا.
 *
 * @return string|null The role name (e.g., 'admin', 'teacher') or null if not logged in.
 */
function currentUserRole(): ?string
{
    return Session::get('user_role');
}

/**
 * Gets the ID of the currently logged-in user.
 * الحصول على ID المستخدم المسجل دخوله حاليًا.
 *
 * @return int|string|null The user ID or null if not logged in.
 */
function currentUserId()
{
    return Session::get('user_id');
}


/**
 * Checks if the currently logged-in user is an Administrator.
 * التحقق مما إذا كان المستخدم الحالي هو مدير.
 *
 * @return bool True if the user is logged in and has the 'admin' role, false otherwise.
 */
function isAdmin(): bool
{
    return isLoggedIn() && strtolower(currentUserRole() ?? '') === 'admin';
}

/**
 * Checks if the currently logged-in user is a Teacher.
 * التحقق مما إذا كان المستخدم الحالي هو معلم.
 *
 * @return bool True if the user is logged in and has the 'teacher' role, false otherwise.
 */
function isTeacher(): bool
{
    return isLoggedIn() && strtolower(currentUserRole() ?? '') === 'teacher';
}

/**
 * Checks if the currently logged-in user is a Student.
 * التحقق مما إذا كان المستخدم الحالي هو طالب.
 *
 * @return bool True if the user is logged in and has the 'student' role, false otherwise.
 */
function isStudent(): bool
{
    return isLoggedIn() && strtolower(currentUserRole() ?? '') === 'student';
}

/**
 * Checks if the currently logged-in user is a Staff member.
 * التحقق مما إذا كان المستخدم الحالي هو موظف.
 *
 * @return bool True if the user is logged in and has the 'staff' role, false otherwise.
 */
function isStaff(): bool
{
    return isLoggedIn() && strtolower(currentUserRole() ?? '') === 'staff';
}

/**
 * Checks if the currently logged-in user is a Guardian.
 * التحقق مما إذا كان المستخدم الحالي هو ولي أمر.
 *
 * @return bool True if the user is logged in and has the 'guardian' role, false otherwise.
 */
function isGuardian(): bool
{
    return isLoggedIn() && strtolower(currentUserRole() ?? '') === 'guardian';
}