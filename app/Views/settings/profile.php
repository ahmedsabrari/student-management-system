<?php
/**
 * User Profile Edit View
 *
 * Renders the form for the currently logged-in user to update their profile details
 * and change their password.
 * This view is rendered within the 'admin.php' or 'main.php' layout.
 *
 * @var object $user The currently logged-in user object passed from SettingsController::profile().
 * @var array $errors (Assumed from Validator)
 */

// Set the title for the layout
// (This $title variable will be used by the layout file)
// (هذا المتغير سيتم استخدامه في ملف القالب)
$title = "My Profile Settings";

// Assume Font Awesome is included in the main layout
?>

<!-- 
    Page Header Section
    قسم ترويسة الصفحة
-->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Edit My Profile</h2>
    <!-- Optional Back button if needed -->
    <!-- <a href="/dashboard" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a> -->
</div>

<!-- Row for Profile Details and Password Change -->
<!-- صف لتفاصيل الملف الشخصي وتغيير كلمة المرور -->
<div class="row">

    <!-- Profile Details Form Column -->
    <!-- عمود نموذج تفاصيل الملف الشخصي -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Update Profile Information</h5>
            </div>
            <div class="card-body">
                <!-- Form submits to the profile update method -->
                <!-- النموذج يُرسل إلى دالة تحديث الملف الشخصي -->
                <form action="/settings/profile" method="POST" id="profileUpdateForm" enctype="multipart/form-data">
                    
                    <!-- 
                        CSRF Token 
                        @TODO: Replace with dynamic token.
                    -->
                    <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder">

                    <!-- Profile Picture Display and Upload -->
                    <div class="mb-3 text-center">
                        <img src="<?= htmlspecialchars($user->avatar ?? '/assets/images/default-avatar.png') ?>" 
                             alt="Current Avatar" 
                             class="img-thumbnail rounded-circle mb-2" 
                             style="width: 120px; height: 120px; object-fit: cover;">
                        <div>
                            <label for="avatar" class="form-label">Change Profile Picture</label>
                            <input class="form-control form-control-sm" type="file" id="avatar" name="avatar" accept="image/png, image/jpeg, image/gif">
                            <small class="form-text text-muted">Max size: 2MB. Allowed types: JPG, PNG, GIF.</small>
                             <!-- @TODO: Display validation error for 'avatar' -->
                        </div>
                    </div>

                    <!-- Full Name -->
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required 
                               value="<?= htmlspecialchars($user->full_name ?? '') ?>">
                         <!-- @TODO: Display validation error for 'full_name' -->
                    </div>

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required
                               value="<?= htmlspecialchars($user->email ?? '') ?>">
                         <!-- @TODO: Display validation error for 'email' -->
                    </div>

                     <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required
                               value="<?= htmlspecialchars($user->username ?? '') ?>">
                         <!-- @TODO: Display validation error for 'username' -->
                    </div>
                    
                    <!-- @TODO: Add Phone and Address fields if applicable for the User model -->
                    <!-- إضافة حقول الهاتف والعنوان إذا كانت متاحة في User model -->

                    <hr>

                    <!-- Form Action Buttons -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Change Form Column -->
    <!-- عمود نموذج تغيير كلمة المرور -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm">
             <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-key me-2"></i>Change Password</h5>
            </div>
            <div class="card-body">
                 <!-- Form submits to the change password method -->
                 <!-- النموذج يُرسل إلى دالة تغيير كلمة المرور -->
                <form action="/settings/change-password" method="POST" id="passwordChangeForm">
                    <!-- 
                        CSRF Token 
                        @TODO: Replace with dynamic token.
                    -->
                    <input type="hidden" name="_csrf_token" value="dummy_csrf_token_placeholder_pwd">

                    <!-- Current Password -->
                    <div class="mb-3">
                        <label for="old_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                         <!-- @TODO: Display validation error for 'old_password' -->
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                         <!-- @TODO: Display validation error for 'new_password' -->
                    </div>

                    <!-- Confirm New Password -->
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                         <!-- @TODO: Display validation error for 'confirm_password' -->
                    </div>

                    <hr>

                    <!-- Form Action Buttons -->
                    <div class="d-flex justify-content-end">
                         <button type="submit" class="btn btn-warning">
                            <i class="fas fa-lock me-1"></i> Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- @TODO: Ensure Font Awesome is included -->
<!-- تأكد من تضمين Font Awesome -->