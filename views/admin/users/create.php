<?php
$pageTitle = $data['title'];
require_once 'views/admin/layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php require_once 'views/admin/layouts/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Create New User</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="/admin/users" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash'])): ?>
                <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <!-- Create User Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="/admin/users/create">
                                <input type="hidden" name="csrf_token" value="<?= $data['csrf_token'] ?>">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                            <select class="form-select" id="role" name="role" required>
                                                <option value="">Select Role</option>
                                                <option value="customer">Customer</option>
                                                <option value="manager">Manager</option>
                                                <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                                                    <option value="super_admin">Super Admin</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                            <div class="form-text">Password must be at least 6 characters long.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                        <label class="form-check-label" for="is_active">
                                            Active User
                                        </label>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="/admin/users" class="btn btn-secondary me-md-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Role Permissions</h6>
                        </div>
                        <div class="card-body">
                            <div id="role-permissions">
                                <p class="text-muted">Select a role to see permissions.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mt-3">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Tips</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Use strong passwords</li>
                                <li><i class="fas fa-check text-success"></i> Verify email addresses</li>
                                <li><i class="fas fa-check text-success"></i> Set appropriate roles</li>
                                <li><i class="fas fa-check text-success"></i> Keep user information up to date</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const rolePermissions = document.getElementById('role-permissions');
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');

    const permissions = {
        customer: [
            'View menu and food items',
            'Place orders',
            'Make table reservations',
            'View order history',
            'Manage profile'
        ],
        manager: [
            'All customer permissions',
            'Manage food items',
            'Manage categories',
            'View and manage orders',
            'View and manage bookings',
            'Access admin dashboard'
        ],
        super_admin: [
            'All manager permissions',
            'Manage users',
            'System configuration',
            'View reports',
            'Database management',
            'Full system access'
        ]
    };

    roleSelect.addEventListener('change', function() {
        const selectedRole = this.value;
        if (selectedRole && permissions[selectedRole]) {
            let html = '<h6>Permissions for ' + selectedRole.replace('_', ' ').toUpperCase() + ':</h6><ul class="list-unstyled">';
            permissions[selectedRole].forEach(function(permission) {
                html += '<li><i class="fas fa-check text-success"></i> ' + permission + '</li>';
            });
            html += '</ul>';
            rolePermissions.innerHTML = html;
        } else {
            rolePermissions.innerHTML = '<p class="text-muted">Select a role to see permissions.</p>';
        }
    });

    // Password confirmation validation
    function validatePasswords() {
        if (passwordField.value !== confirmPasswordField.value) {
            confirmPasswordField.setCustomValidity('Passwords do not match');
        } else {
            confirmPasswordField.setCustomValidity('');
        }
    }

    passwordField.addEventListener('input', validatePasswords);
    confirmPasswordField.addEventListener('input', validatePasswords);
});
</script>

<?php require_once 'views/admin/layouts/footer.php'; ?>
