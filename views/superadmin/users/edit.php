<?php require_once 'views/layouts/superadmin_header.php'; ?>

<?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

<div class="main-content">
<div class="container-fluid">
    <div class="row">
        <main class="col-12 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 text-danger">Edit User: <?php echo htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: $user['email'] ?? 'Unknown User'); ?></h1>                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="<?= SITE_URL ?>/superadmin/users" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">User Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= SITE_URL ?>/superadmin/users/edit/<?php echo $user['id']; ?>" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="first_name" name="first_name"
                                               value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                                        <?php if (isset($errors['first_name'])): ?>
                                            <div class="text-danger small"><?php echo $errors['first_name']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="last_name" name="last_name"
                                               value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                                        <?php if (isset($errors['last_name'])): ?>
                                            <div class="text-danger small"><?php echo $errors['last_name']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                                        <?php if (isset($errors['email'])): ?>
                                            <div class="text-danger small"><?php echo $errors['email']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                        <?php if (isset($errors['phone'])): ?>
                                            <div class="text-danger small"><?php echo $errors['phone']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                        <select class="form-select" id="role" name="role" required
                                                <?php echo ($user['id'] == $_SESSION['user_id']) ? 'disabled' : ''; ?>>
                                            <option value="">Select Role</option>
                                            <option value="customer" <?php echo ($user['role'] == 'customer') ? 'selected' : ''; ?>>Customer</option>
                                            <option value="manager" <?php echo ($user['role'] == 'manager') ? 'selected' : ''; ?>>Manager</option>
                                            <option value="super_admin" <?php echo ($user['role'] == 'super_admin') ? 'selected' : ''; ?>>Super Admin</option>
                                        </select>
                                        <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                            <div class="form-text">You cannot change your own role</div>
                                            <input type="hidden" name="role" value="<?php echo $user['role']; ?>">
                                        <?php endif; ?>
                                        <?php if (isset($errors['role'])): ?>
                                            <div class="text-danger small"><?php echo $errors['role']; ?></div>
                                        <?php endif; ?>
                                    </div>                                    <div class="col-md-6">
                                        <label for="is_active" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select" id="is_active" name="is_active" required
                                                <?php echo ($user['id'] == $_SESSION['user_id']) ? 'disabled' : ''; ?>>
                                            <option value="1" <?php echo ($user['is_active'] == 1) ? 'selected' : ''; ?>>Active</option>
                                            <option value="0" <?php echo ($user['is_active'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                        <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                            <div class="form-text">You cannot change your own status</div>
                                            <input type="hidden" name="is_active" value="<?php echo $user['is_active']; ?>">
                                        <?php endif; ?>
                                        <?php if (isset($errors['is_active'])): ?>
                                            <div class="text-danger small"><?php echo $errors['is_active']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                    <?php if (isset($errors['address'])): ?>
                                        <div class="text-danger small"><?php echo $errors['address']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Profile Picture</label>
                                    <?php if (!empty($user['avatar'])): ?>
                                        <div class="mb-2">
                                            <img src="<?php echo $user['avatar']; ?>" alt="Current Avatar" class="rounded-circle" width="64" height="64">
                                            <small class="text-muted ms-2">Current picture</small>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                    <div class="form-text">Leave empty to keep current picture. Supported formats: JPG, PNG, GIF (max 2MB)</div>
                                    <?php if (isset($errors['avatar'])): ?>
                                        <div class="text-danger small"><?php echo $errors['avatar']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <hr>

                                <h6 class="text-danger mb-3">Password Change (Optional)</h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                        <div class="form-text">Leave empty to keep current password. Minimum 6 characters if changing.</div>
                                        <?php if (isset($errors['password'])): ?>
                                            <div class="text-danger small"><?php echo $errors['password']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                        <?php if (isset($errors['confirm_password'])): ?>
                                            <div class="text-danger small"><?php echo $errors['confirm_password']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>                                <div class="d-flex justify-content-end">
                                    <a href="<?= SITE_URL ?>/superadmin/users" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-save"></i> Update User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- User Statistics -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">User Statistics</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-success"><?php echo $user_stats['total_orders'] ?? 0; ?></h4>
                                        <p class="small text-muted">Total Orders</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-info"><?php echo $user_stats['total_bookings'] ?? 0; ?></h4>
                                    <p class="small text-muted">Total Bookings</p>
                                </div>
                            </div>
                            <hr>
                            <div class="small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Member Since:</span>
                                    <span><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Last Updated:</span>
                                    <span><?php echo date('M d, Y', strtotime($user['updated_at'] ?? $user['created_at'])); ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Total Spent:</span>
                                    <span class="text-success">$<?php echo number_format($user_stats['total_spent'] ?? 0, 2); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Role Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Current Role Permissions</h6>
                        </div>
                        <div class="card-body">
                            <?php if ($user['role'] === 'customer'): ?>
                                <h6 class="text-success">Customer</h6>
                                <ul class="small">
                                    <li>Can browse menu</li>
                                    <li>Can place orders</li>
                                    <li>Can make bookings</li>
                                    <li>Can view order history</li>
                                    <li>Limited profile access</li>
                                </ul>
                            <?php elseif ($user['role'] === 'manager'): ?>
                                <h6 class="text-warning">Manager</h6>
                                <ul class="small">
                                    <li>Manage menu items</li>
                                    <li>Manage categories</li>
                                    <li>View customer orders</li>
                                    <li>Manage bookings</li>
                                    <li>Access admin dashboard</li>
                                </ul>
                            <?php elseif ($user['role'] === 'super_admin'): ?>
                                <h6 class="text-danger">Super Admin</h6>
                                <ul class="small">
                                    <li>Full system access</li>
                                    <li>User management</li>
                                    <li>Role assignment</li>
                                    <li>System statistics</li>
                                    <li>Restaurant management</li>
                                    <li>Advanced reporting</li>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Security Notice -->
                    <?php if ($user['id'] == $_SESSION['user_id']): ?>
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Security Notice</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info small mb-0">
                                <i class="fas fa-info-circle"></i>
                                <strong>Note:</strong> You are editing your own account. Role and status changes are restricted for security.
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;

    if (password && password !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

// Avatar preview
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Check file size (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            this.value = '';
            return;
        }

        // Check file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Only JPG, PNG, and GIF files are allowed');
            this.value = '';
            return;
        }
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    if (password) {
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match');
            return false;
        }

        if (password.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long');
            return false;
        }
    }
});
</script>

</div>

<?php require_once 'views/layouts/superadmin_footer.php'; ?>
