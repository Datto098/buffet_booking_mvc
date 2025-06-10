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

                <!-- User Edit Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-user"></i> User Information
                                </h5>
                                <span class="badge bg-<?= ($user['status'] ?? 'inactive') == 'active' ? 'success' : 'danger' ?>">
                                    <?= ucfirst($user['status'] ?? 'inactive') ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <form action="<?= SITE_URL ?>/admin/users/update/<?= $user['id'] ?>" method="POST" enctype="multipart/form-data" id="editUserForm">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" class="form-control" id="username" name="username"
                                                       value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                <input type="email" class="form-control" id="email" name="email"
                                                       value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="first_name" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name"
                                                   value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="last_name" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name"
                                                   value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                <input type="tel" class="form-control" id="phone" name="phone"
                                                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                            <select class="form-select" id="role" name="role" required>
                                                <option value="">Select Role</option>
                                                <option value="admin" <?= ($user['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Administrator</option>
                                                <option value="customer" <?= ($user['role'] ?? '') == 'customer' ? 'selected' : '' ?>>Customer</option>
                                                <option value="manager" <?= ($user['role'] ?? '') == 'manager' ? 'selected' : '' ?>>Manager</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="active" <?= ($user['status'] ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
                                                <option value="inactive" <?= ($user['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="avatar" class="form-label">Profile Picture</label>
                                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="bio" class="form-label">Bio</label>
                                        <textarea class="form-control" id="bio" name="bio" rows="4"
                                                  placeholder="User biography or notes..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">New Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="password" name="password"
                                                   placeholder="Leave blank to keep current password">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="form-text">Password must be at least 8 characters long</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                                   placeholder="Confirm new password">
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update User
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- User Avatar -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-image"></i> Profile Picture
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="avatar-preview mb-3">
                                    <?php if (!empty($user['avatar'])): ?>
                                        <img src="<?= SITE_URL ?>/uploads/user_avatars/<?= htmlspecialchars($user['avatar']) ?>"
                                             class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" alt="User Avatar">
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <i class="fas fa-user fa-5x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <p class="text-muted small">Click "Choose File" above to upload a new profile picture</p>
                            </div>
                        </div>

                        <!-- User Statistics -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-chart-bar"></i> User Statistics
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h4 class="text-primary mb-1"><?= $user['total_bookings'] ?? 0 ?></h4>
                                            <small class="text-muted">Total Bookings</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success mb-1"><?= $user['total_orders'] ?? 0 ?></h4>
                                        <small class="text-muted">Total Orders</small>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Member Since:</small>
                                    <small><?= date('M d, Y', strtotime($user['created_at'] ?? 'now')) ?></small>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Last Login:</small>
                                    <small><?= !empty($user['last_login']) ? date('M d, Y g:i A', strtotime($user['last_login'])) : 'Never' ?></small>
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
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="viewUserBookings(<?= $user['id'] ?>)">
                                        <i class="fas fa-calendar"></i> View Bookings
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="viewUserOrders(<?= $user['id'] ?>)">
                                        <i class="fas fa-shopping-cart"></i> View Orders
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="sendNotification(<?= $user['id'] ?>)">
                                        <i class="fas fa-bell"></i> Send Notification
                                    </button>
                                    <?php if (($user['status'] ?? '') == 'active'): ?>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleUserStatus(<?= $user['id'] ?>, 'inactive')">
                                            <i class="fas fa-user-slash"></i> Deactivate User
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="toggleUserStatus(<?= $user['id'] ?>, 'active')">
                                            <i class="fas fa-user-check"></i> Activate User
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user?</p>
                    <p class="text-danger"><strong>Warning:</strong> This action cannot be undone and will also remove all associated bookings and orders.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="<?= SITE_URL ?>/admin/users/delete/<?= $user['id'] ?>" method="POST" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'views/admin/layouts/footer.php'; ?>    <script>
        window.SITE_URL = '<?= SITE_URL ?>';
        document.addEventListener('DOMContentLoaded', function() {
            initializeUserEditForm();

            // Set up password toggle
            document.getElementById('togglePassword')?.addEventListener('click', function() {
                togglePasswordVisibility('togglePassword', 'password');
            });
        });
    </script>

    <style>
        .avatar-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f8f9fc 0%, #e8ecf1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            border: 3px dashed #dee2e6;
        }

        .avatar-preview img {
            border: 3px solid #dee2e6;
        }

        .form-control:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .input-group-text {
            background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
            border-color: #e3e6f0;
        }
    </style>
</body>
</html>
