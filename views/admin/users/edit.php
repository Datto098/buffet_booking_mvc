<?php include '../views/admin/layouts/header.php'; ?>

<div class="admin-wrapper">
    <?php include '../views/admin/layouts/sidebar.php'; ?>

    <div class="admin-content">
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">Edit User</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/admin/users">User Management</a></li>
                            <li class="breadcrumb-item active">Edit: <?php echo htmlspecialchars($user['name']); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="/admin/users" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                    <?php if ($this->hasRole(['super_admin']) && $user['id'] != $_SESSION['user_id']): ?>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">User Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="/admin/users/edit/<?php echo $user['id']; ?>" method="POST" id="editUserForm">
                                <?php echo $this->csrfToken(); ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role</label>
                                            <select class="form-select" id="role" name="role" <?php echo ($user['id'] == $_SESSION['user_id']) ? 'disabled' : ''; ?>>
                                                <option value="customer" <?php echo ($user['role'] == 'customer') ? 'selected' : ''; ?>>Customer</option>
                                                <?php if ($this->hasRole(['super_admin'])): ?>
                                                    <option value="manager" <?php echo ($user['role'] == 'manager') ? 'selected' : ''; ?>>Manager</option>
                                                    <option value="super_admin" <?php echo ($user['role'] == 'super_admin') ? 'selected' : ''; ?>>Super Admin</option>
                                                <?php endif; ?>
                                            </select>
                                            <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                                <div class="form-text">You cannot change your own role.</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Account Status</label>
                                            <select class="form-select" id="status" name="status" <?php echo ($user['id'] == $_SESSION['user_id']) ? 'disabled' : ''; ?>>
                                                <option value="active" <?php echo ($user['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                                <option value="inactive" <?php echo ($user['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                                <option value="suspended" <?php echo ($user['status'] == 'suspended') ? 'selected' : ''; ?>>Suspended</option>
                                            </select>
                                            <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                                <div class="form-text">You cannot change your own status.</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter full address..."><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>

                                <div class="mb-4">
                                    <h6>Password Settings</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password" class="form-label">New Password</label>
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password_confirm" class="form-label">Confirm New Password</label>
                                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Confirm new password">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update User
                                    </button>
                                    <a href="/admin/users" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">User Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="stat-item">
                                        <h4 class="stat-number text-primary"><?php echo $user['total_orders'] ?? 0; ?></h4>
                                        <p class="stat-label">Total Orders</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <h4 class="stat-number text-success">₹<?php echo number_format($user['total_spent'] ?? 0, 2); ?></h4>
                                        <p class="stat-label">Total Spent</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row text-center mt-3">
                                <div class="col-6">
                                    <div class="stat-item">
                                        <h4 class="stat-number text-info"><?php echo $user['total_bookings'] ?? 0; ?></h4>
                                        <p class="stat-label">Bookings</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <h4 class="stat-number text-warning"><?php echo $user['last_login_days'] ?? 'Never'; ?></h4>
                                        <p class="stat-label">Days Since Login</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title">Account Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="small text-muted">
                                <p><strong>User ID:</strong> #<?php echo $user['id']; ?></p>
                                <p><strong>Registered:</strong> <?php echo date('M j, Y', strtotime($user['created_at'])); ?></p>
                                <p><strong>Last Updated:</strong> <?php echo date('M j, Y g:i A', strtotime($user['updated_at'])); ?></p>
                                <?php if (!empty($user['last_login'])): ?>
                                    <p><strong>Last Login:</strong> <?php echo date('M j, Y g:i A', strtotime($user['last_login'])); ?></p>
                                <?php endif; ?>
                                <p><strong>Email Verified:</strong>
                                    <?php if (!empty($user['email_verified_at'])): ?>
                                        <span class="badge bg-success">Yes</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">No</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title">Recent Activity</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($user['recent_activity'])): ?>
                                <div class="activity-list">
                                    <?php foreach ($user['recent_activity'] as $activity): ?>
                                        <div class="activity-item">
                                            <div class="activity-date small text-muted">
                                                <?php echo date('M j, g:i A', strtotime($activity['created_at'])); ?>
                                            </div>
                                            <div class="activity-text">
                                                <?php echo htmlspecialchars($activity['description']); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No recent activity</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<?php if ($this->hasRole(['super_admin']) && $user['id'] != $_SESSION['user_id']): ?>
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete "<strong><?php echo htmlspecialchars($user['name']); ?></strong>"?</p>
                <p class="text-danger small">
                    <i class="fas fa-exclamation-triangle"></i>
                    This action cannot be undone. All user data, orders, and bookings will be permanently removed.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="/admin/users/delete/<?php echo $user['id']; ?>" method="POST" class="d-inline">
                    <?php echo $this->csrfToken(); ?>
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('editUserForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Reset previous validation states
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        let isValid = true;

        // Validate required fields
        const requiredFields = ['name', 'email'];
        requiredFields.forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                field.nextElementSibling.textContent = 'This field is required';
                isValid = false;
            }
        });

        // Validate email format
        const emailField = form.querySelector('[name="email"]');
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailField.value && !emailPattern.test(emailField.value)) {
            emailField.classList.add('is-invalid');
            emailField.nextElementSibling.textContent = 'Please enter a valid email address';
            isValid = false;
        }

        // Validate phone number format
        const phoneField = form.querySelector('[name="phone"]');
        const phonePattern = /^[0-9]{10}$/;
        if (phoneField.value && !phonePattern.test(phoneField.value.replace(/\D/g, ''))) {
            phoneField.classList.add('is-invalid');
            phoneField.nextElementSibling.textContent = 'Please enter a valid 10-digit phone number';
            isValid = false;
        }

        // Validate password confirmation
        const passwordField = form.querySelector('[name="password"]');
        const passwordConfirmField = form.querySelector('[name="password_confirm"]');

        if (passwordField.value || passwordConfirmField.value) {
            if (passwordField.value.length < 6) {
                passwordField.classList.add('is-invalid');
                passwordField.nextElementSibling.textContent = 'Password must be at least 6 characters long';
                isValid = false;
            }

            if (passwordField.value !== passwordConfirmField.value) {
                passwordConfirmField.classList.add('is-invalid');
                passwordConfirmField.nextElementSibling.textContent = 'Password confirmation does not match';
                isValid = false;
            }
        }

        if (isValid) {
            showLoadingButton(form.querySelector('button[type="submit"]'));
            form.submit();
        }
    });

    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 6) {
            value = value.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
        } else if (value.length >= 3) {
            value = value.replace(/(\d{3})(\d{3})/, '$1-$2');
        }
        e.target.value = value;
    });
});
</script>

<?php include '../views/admin/layouts/footer.php'; ?>
