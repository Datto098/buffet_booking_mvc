<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token ?? ''; ?>">
    <title>Create User - Admin</title>
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
                        <h1 class="h2">Create New User</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/users">Users</a></li>
                                <li class="breadcrumb-item active">Create New User</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="<?= SITE_URL ?>/admin/users" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>                <!-- Flash Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <!-- Create User Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-user-plus"></i> New User Information
                                </h6>
                            </div>
                            <div class="card-body">                                <form method="POST" action="<?= SITE_URL ?>/admin/users/create" id="createUserForm">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="phone" class="form-label">Phone Number</label>
                                                <input type="tel" class="form-control" id="phone" name="phone">
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
                                                <label for="password_confirm" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                                <select class="form-select" id="role" name="role" required>
                                                    <option value="">Select Role</option>
                                                    <option value="customer">Customer</option>
                                                    <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'super_admin'): ?>
                                                        <option value="manager">Manager</option>
                                                        <option value="super_admin">Super Admin</option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
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
                                        <a href="<?= SITE_URL ?>/admin/users" class="btn btn-secondary me-md-2">Cancel</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Create User
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-info-circle"></i> Role Permissions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="role-permissions">
                                    <p class="text-muted">Select a role to see permissions.</p>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mt-3">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-lightbulb"></i> Tips
                                </h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Use strong passwords</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Verify email addresses</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Set appropriate roles</li>
                                    <li class="mb-0"><i class="fas fa-check text-success me-2"></i> Keep user information up to date</li>
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
        const confirmPasswordField = document.getElementById('password_confirm');

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
                let html = '<h6 class="text-primary">Permissions for ' + selectedRole.replace('_', ' ').toUpperCase() + ':</h6><ul class="list-unstyled">';
                permissions[selectedRole].forEach(function(permission) {
                    html += '<li><i class="fas fa-check text-success me-2"></i>' + permission + '</li>';
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

        // Form validation
        document.getElementById('createUserForm').addEventListener('submit', function(e) {
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;

            if (!firstName || !lastName || !email || !password) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }

            if (!email.includes('@')) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return false;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long.');
                return false;
            }

            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Password confirmation does not match.');
                return false;
            }
        });
    });
    </script>

    <?php require_once 'views/admin/layouts/footer.php'; ?>
</body>
</html>
