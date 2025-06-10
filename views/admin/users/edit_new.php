<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token ?? ''; ?>">
    <title>Edit User - Admin</title>
    <?php require_once 'views/admin/layouts/header.php'; ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php require_once 'views/admin/layouts/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Page Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2">
                            <i class="fas fa-user-edit"></i> Edit User
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/users">Users</a></li>
                                <li class="breadcrumb-item active">Edit User</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="<?= SITE_URL ?>/admin/users" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Users
                            </a>
                        </div>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Delete User
                        </button>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?= $_SESSION['flash_type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                        <?= htmlspecialchars($_SESSION['flash_message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php
                    unset($_SESSION['flash_message']);
                    unset($_SESSION['flash_type']);
                    ?>
                <?php endif; ?>

                <!-- User Edit Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-user"></i> User Information
                                </h5>
                                <span class="badge bg-<?= ($user['is_active'] ?? 1) ? 'success' : 'danger' ?>">
                                    <?= ($user['is_active'] ?? 1) ? 'Active' : 'Inactive' ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <form action="<?= SITE_URL ?>/admin/users/edit/<?= $user['id'] ?>" method="POST" id="editUserForm" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" class="form-control" id="first_name" name="first_name"
                                                       value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" class="form-control" id="last_name" name="last_name"
                                                       value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                <input type="email" class="form-control" id="email" name="email"
                                                       value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                <input type="tel" class="form-control" id="phone" name="phone"
                                                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                                       value="<?= $user['date_of_birth'] ?? '' ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                                <select class="form-control" id="role" name="role" required>
                                                    <option value="customer" <?= ($user['role'] ?? '') === 'customer' ? 'selected' : '' ?>>Customer</option>
                                                    <option value="manager" <?= ($user['role'] ?? '') === 'manager' ? 'selected' : '' ?>>Manager</option>
                                                    <option value="super_admin" <?= ($user['role'] ?? '') === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">New Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="password" name="password"
                                                   placeholder="Leave blank to keep current password">
                                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                                <i class="fas fa-eye" id="passwordToggleIcon"></i>
                                            </button>
                                        </div>
                                        <div class="form-text">Minimum 6 characters required</div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                                   <?= ($user['is_active'] ?? 1) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="is_active">
                                                Account Active
                                            </label>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="button" class="btn btn-secondary me-md-2" onclick="window.history.back()">Cancel</button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update User
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right sidebar with user stats and actions -->
                    <div class="col-lg-4">
                        <!-- User Avatar -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-image"></i> Profile Picture
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="avatar-container mb-3">
                                    <img src="<?= !empty($user['avatar']) ? SITE_URL . '/uploads/user_avatars/' . $user['avatar'] : SITE_URL . '/assets/images/default-avatar.png' ?>"
                                         alt="User Avatar" class="img-thumbnail rounded-circle"
                                         style="width: 120px; height: 120px; object-fit: cover;" id="avatarPreview">
                                </div>
                                <div class="mb-3">
                                    <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" form="editUserForm" onchange="previewAvatar(this)">
                                    <div class="form-text">JPG, PNG, GIF up to 5MB</div>
                                </div>
                                <?php if (!empty($user['avatar'])): ?>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeAvatar()">
                                        <i class="fas fa-trash"></i> Remove Picture
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- User Statistics -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-chart-line"></i> User Statistics
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="stat-item">
                                            <h4 class="text-primary mb-1">
                                                <?= $user['total_orders'] ?? 0 ?>
                                            </h4>
                                            <small class="text-muted">Total Orders</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-item">
                                            <h4 class="text-success mb-1">
                                                $<?= number_format($user['total_spent'] ?? 0, 2) ?>
                                            </h4>
                                            <small class="text-muted">Total Spent</small>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Member Since:</small>
                                    <small><?= date('M d, Y', strtotime($user['created_at'] ?? 'now')) ?></small>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Last Login:</small>
                                    <small><?= !empty($user['last_login']) ? date('M d, Y', strtotime($user['last_login'])) : 'Never' ?></small>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-bolt"></i> Quick Actions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewUserOrders(<?= $user['id'] ?>)">
                                        <i class="fas fa-shopping-cart"></i> View Orders
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="sendNotification(<?= $user['id'] ?>)">
                                        <i class="fas fa-bell"></i> Send Notification
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="resetPassword(<?= $user['id'] ?>)">
                                        <i class="fas fa-key"></i> Reset Password
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="duplicateUser(<?= $user['id'] ?>)">
                                        <i class="fas fa-copy"></i> Duplicate User
                                    </button>
                                    <hr>
                                    <button type="button" class="btn btn-outline-<?= ($user['is_active'] ?? 1) ? 'warning' : 'success' ?> btn-sm"
                                            onclick="toggleUserStatus(<?= $user['id'] ?>)">
                                        <i class="fas fa-<?= ($user['is_active'] ?? 1) ? 'ban' : 'check' ?>"></i>
                                        <?= ($user['is_active'] ?? 1) ? 'Suspend' : 'Activate' ?> Account
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete User Modal -->
                <div class="modal fade" id="deleteUserModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-exclamation-triangle"></i> Confirm Delete
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this user account?</p>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Warning:</strong> This action cannot be undone. All user data including orders and booking history will be permanently removed.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form action="<?= SITE_URL ?>/admin/users/delete/<?= $user['id'] ?>" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Delete User
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Avatar preview functionality
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Remove avatar
        function removeAvatar() {
            if (confirm('Are you sure you want to remove the profile picture?')) {
                // Add hidden input to form to indicate avatar removal
                const form = document.getElementById('editUserForm');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_avatar';
                input.value = '1';
                form.appendChild(input);

                // Update preview
                document.getElementById('avatarPreview').src = '<?= SITE_URL ?>/assets/images/default-avatar.png';

                // Submit form
                form.submit();
            }
        }

        // Toggle password visibility
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggleIcon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Delete confirmation
        function confirmDelete() {
            const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
            modal.show();
        }

        // Quick action functions
        function viewUserOrders(userId) {
            window.location.href = `<?= SITE_URL ?>/admin/orders?user_id=${userId}`;
        }

        function sendNotification(userId) {
            // Implement notification functionality
            alert('Notification feature coming soon!');
        }

        function resetPassword(userId) {
            if (confirm('Are you sure you want to reset this user\'s password?')) {
                fetch(`<?= SITE_URL ?>/admin/users/reset-password/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Password reset email sent successfully!');
                    } else {
                        alert('Error: ' + (data.message || 'Failed to reset password'));
                    }
                })
                .catch(error => {
                    alert('Error: Failed to reset password');
                });
            }
        }

        function duplicateUser(userId) {
            if (confirm('Create a new user based on this user\'s information?')) {
                window.location.href = `<?= SITE_URL ?>/admin/users/duplicate/${userId}`;
            }
        }

        function toggleUserStatus(userId) {
            const isActive = <?= ($user['is_active'] ?? 1) ? 'true' : 'false' ?>;
            const action = isActive ? 'suspend' : 'activate';

            if (confirm(`Are you sure you want to ${action} this user account?`)) {
                fetch(`<?= SITE_URL ?>/admin/users/toggle-status/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to update user status'));
                    }
                })
                .catch(error => {
                    alert('Error: Failed to update user status');
                });
            }
        }

        // Form validation
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            if (password && password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long.');
                return false;
            }
        });

        // Auto-save draft functionality
        let autoSaveTimeout;
        function autoSaveDraft() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                const formData = new FormData(document.getElementById('editUserForm'));
                const draftData = {};
                for (let [key, value] of formData.entries()) {
                    if (key !== 'csrf_token' && key !== 'password') {
                        draftData[key] = value;
                    }
                }
                localStorage.setItem('user_edit_draft_<?= $user['id'] ?>', JSON.stringify(draftData));
            }, 2000);
        }

        // Load saved draft
        document.addEventListener('DOMContentLoaded', function() {
            const savedDraft = localStorage.getItem('user_edit_draft_<?= $user['id'] ?>');
            if (savedDraft) {
                const draftData = JSON.parse(savedDraft);
                Object.keys(draftData).forEach(key => {
                    const field = document.querySelector(`[name="${key}"]`);
                    if (field && field.value === '') {
                        field.value = draftData[key];
                    }
                });
            }

            // Add auto-save listeners
            document.querySelectorAll('#editUserForm input, #editUserForm textarea, #editUserForm select').forEach(field => {
                field.addEventListener('input', autoSaveDraft);
            });
        });

        // Clear draft on successful submit
        document.getElementById('editUserForm').addEventListener('submit', function() {
            localStorage.removeItem('user_edit_draft_<?= $user['id'] ?>');
        });
    </script>
</body>
</html>
