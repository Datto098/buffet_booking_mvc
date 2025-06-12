<?php require_once 'views/layouts/superadmin_header.php'; ?>
<?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

<div class="main-content fade-in">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>
                    <i class="fas fa-user-plus"></i>
                    Create New User
                </h1>
                <div class="btn-toolbar">
                    <a href="<?= SITE_URL ?>/superadmin/users" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>

        <!-- Form Content -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-circle me-2"></i>
                            User Information
                        </h5>
                    </div>
                    <div class="card-body">                        <form method="POST" action="<?= SITE_URL ?>/superadmin/users/create" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                           value="<?php echo htmlspecialchars($old['first_name'] ?? ''); ?>" required>
                                    <?php if (isset($errors['first_name'])): ?>
                                        <div class="text-danger small"><?php echo $errors['first_name']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                           value="<?php echo htmlspecialchars($old['last_name'] ?? ''); ?>" required>
                                    <?php if (isset($errors['last_name'])): ?>
                                        <div class="text-danger small"><?php echo $errors['last_name']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>" required>
                                    <?php if (isset($errors['email'])): ?>
                                        <div class="text-danger small"><?php echo $errors['email']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                           value="<?php echo htmlspecialchars($old['phone'] ?? ''); ?>">
                                    <?php if (isset($errors['phone'])): ?>
                                        <div class="text-danger small"><?php echo $errors['phone']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <div class="form-text">Minimum 6 characters</div>
                                        <?php if (isset($errors['password'])): ?>
                                            <div class="text-danger small"><?php echo $errors['password']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <?php if (isset($errors['confirm_password'])): ?>
                                            <div class="text-danger small"><?php echo $errors['confirm_password']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                        <select class="form-select" id="role" name="role" required>
                                            <option value="">Select Role</option>
                                            <option value="customer" <?php echo (isset($old['role']) && $old['role'] == 'customer') ? 'selected' : ''; ?>>Customer</option>
                                            <option value="manager" <?php echo (isset($old['role']) && $old['role'] == 'manager') ? 'selected' : ''; ?>>Manager</option>
                                            <option value="super_admin" <?php echo (isset($old['role']) && $old['role'] == 'super_admin') ? 'selected' : ''; ?>>Super Admin</option>
                                        </select>
                                        <?php if (isset($errors['role'])): ?>
                                            <div class="text-danger small"><?php echo $errors['role']; ?></div>
                                        <?php endif; ?>
                                    </div>                                    <div class="col-md-6">
                                        <label for="is_active" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select" id="is_active" name="is_active" required>
                                            <option value="1" <?php echo (!isset($old['is_active']) || $old['is_active'] == '1') ? 'selected' : ''; ?>>Active</option>
                                            <option value="0" <?php echo (isset($old['is_active']) && $old['is_active'] == '0') ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                        <?php if (isset($errors['is_active'])): ?>
                                            <div class="text-danger small"><?php echo $errors['is_active']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($old['address'] ?? ''); ?></textarea>
                                    <?php if (isset($errors['address'])): ?>
                                        <div class="text-danger small"><?php echo $errors['address']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Profile Picture</label>
                                    <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                    <div class="form-text">Optional. Supported formats: JPG, PNG, GIF (max 2MB)</div>
                                    <?php if (isset($errors['avatar'])): ?>
                                        <div class="text-danger small"><?php echo $errors['avatar']; ?></div>
                                    <?php endif; ?>
                                </div>                                <div class="d-flex justify-content-end">
                                    <a href="<?= SITE_URL ?>/superadmin/users" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-save"></i> Create User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Role Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Role Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="role-info" id="customer-info" style="display: none;">
                                <h6 class="text-success">Customer</h6>
                                <ul class="small">
                                    <li>Can browse menu</li>
                                    <li>Can place orders</li>
                                    <li>Can make bookings</li>
                                    <li>Can view order history</li>
                                    <li>Limited profile access</li>
                                </ul>
                            </div>
                            <div class="role-info" id="manager-info" style="display: none;">
                                <h6 class="text-warning">Manager</h6>
                                <ul class="small">
                                    <li>Manage menu items</li>
                                    <li>Manage categories</li>
                                    <li>View customer orders</li>
                                    <li>Manage bookings</li>
                                    <li>Access admin dashboard</li>
                                </ul>
                            </div>
                            <div class="role-info" id="super_admin-info" style="display: none;">
                                <h6 class="text-danger">Super Admin</h6>
                                <ul class="small">
                                    <li>Full system access</li>
                                    <li>User management</li>
                                    <li>Role assignment</li>
                                    <li>System statistics</li>
                                    <li>Restaurant management</li>
                                    <li>Advanced reporting</li>
                                </ul>
                            </div>
                            <div class="text-muted small" id="no-role-info">
                                Select a role to see permissions
                            </div>
                        </div>
                    </div>

                    <!-- Security Notice -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Security Notice</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning small mb-0">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Important:</strong> Super Admin accounts have full system access. Only assign this role to trusted personnel.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    const selectedRole = this.value;
    const roleInfos = document.querySelectorAll('.role-info');
    const noRoleInfo = document.getElementById('no-role-info');

    // Hide all role info
    roleInfos.forEach(info => info.style.display = 'none');

    if (selectedRole) {
        noRoleInfo.style.display = 'none';
        const targetInfo = document.getElementById(selectedRole + '-info');
        if (targetInfo) {
            targetInfo.style.display = 'block';
        }
    } else {
        noRoleInfo.style.display = 'block';
    }
});

// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;

    if (password !== confirmPassword) {
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
});
</script>

</div>

<?php require_once 'views/layouts/superadmin_footer.php'; ?>
