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
                        <h1 class="h2">Edit User</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/users">Users</a></li>
                                <li class="breadcrumb-item active">Edit User</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="<?= SITE_URL ?>/admin/users" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>

                <!-- Flash Messages -->
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

                <!-- Edit User Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-user-edit"></i> User Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="<?= SITE_URL ?>/admin/users/edit/<?= $user['id'] ?>" id="editUserForm">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="phone" class="form-label">Phone Number</label>
                                                <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password" class="form-label">New Password</label>
                                                <input type="password" class="form-control" id="password" name="password" minlength="6">
                                                <div class="form-text">Leave blank to keep current password. Must be at least 6 characters if changed.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password_confirm" class="form-label">Confirm New Password</label>
                                                <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'super_admin' && $user['id'] != $_SESSION['user']['id']): ?>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="role" name="role" required>
                                                        <option value="customer" <?= ($user['role'] === 'customer') ? 'selected' : '' ?>>Customer</option>
                                                        <option value="manager" <?= ($user['role'] === 'manager') ? 'selected' : '' ?>>Manager</option>
                                                        <option value="super_admin" <?= ($user['role'] === 'super_admin') ? 'selected' : '' ?>>Super Admin</option>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="role_display" class="form-label">Role</label>
                                                    <input type="text" class="form-control" id="role_display" value="<?= ucfirst(str_replace('_', ' ', $user['role'])) ?>" readonly>
                                                    <input type="hidden" name="role" value="<?= $user['role'] ?>">
                                                    <div class="form-text">Role can only be changed by Super Admin.</div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= $user['date_of_birth'] ?? '' ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                                    </div>

                                    <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'super_admin' && $user['id'] != $_SESSION['user']['id']): ?>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?= ($user['is_active'] == 1) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="is_active">
                                                    Active User
                                                </label>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="<?= SITE_URL ?>/admin/users" class="btn btn-secondary me-md-2">Cancel</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update User
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- User Statistics -->
                        <div class="card shadow-sm border-0">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-chart-bar"></i> User Statistics
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-12 mb-3">
                                        <div class="p-3 bg-light rounded">
                                            <h5 class="text-primary mb-1"><?= $user['total_orders'] ?? 0 ?></h5>
                                            <small class="text-muted">Total Orders</small>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <div class="p-3 bg-light rounded">
                                            <h5 class="text-success mb-1">$<?= number_format($user['total_spent'] ?? 0, 2) ?></h5>
                                            <small class="text-muted">Total Spent</small>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <div class="p-3 bg-light rounded">
                                            <h5 class="text-info mb-1"><?= $user['total_bookings'] ?? 0 ?></h5>
                                            <small class="text-muted">Total Bookings</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Details -->
                        <div class="card shadow-sm border-0 mt-3">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-info-circle"></i> Account Details
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>User ID:</strong> #<?= $user['id'] ?>
                                </div>
                                <div class="mb-2">
                                    <strong>Status:</strong>
                                    <span class="badge <?= ($user['is_active'] == 1) ? 'bg-success' : 'bg-danger' ?>">
                                        <?= ($user['is_active'] == 1) ? 'Active' : 'Inactive' ?>
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <strong>Created:</strong> <?= date('M j, Y', strtotime($user['created_at'])) ?>
                                </div>
                                <div class="mb-2">
                                    <strong>Last Updated:</strong>
                                    <?php if (isset($user['updated_at']) && $user['updated_at']): ?>
                                        <?= date('M j, Y', strtotime($user['updated_at'])) ?>
                                    <?php else: ?>
                                        Never
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Role Permissions -->
                        <div class="card shadow-sm border-0 mt-3">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-key"></i> Role Permissions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="role-permissions">
                                    <!-- Permissions will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>

                        <!-- Tips -->
                        <div class="card shadow-sm border-0 mt-3">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-lightbulb"></i> Tips
                                </h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Leave password blank to keep current</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Use strong passwords when changing</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Verify email changes carefully</li>
                                    <li class="mb-0"><i class="fas fa-check text-success me-2"></i> Check user activity before role changes</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeUserEditForm();
        });
    </script>

    <?php require_once 'views/admin/layouts/footer.php'; ?>
</body>
</html>
