<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token ?? ''; ?>">
    <title>Users Management - Admin</title>
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
                        <h1 class="h2">Users Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Users</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportUsers()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                        <a href="<?= SITE_URL ?>/admin/users/create" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add User
                        </a>
                    </div>
                </div><!-- Flash Messages -->
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

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-primary">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Users
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $totalUsers ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-success">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Active Users
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $activeUsers ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-warning">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Admins
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $adminUsers ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-info">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            New Today
                                        </div>                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $newToday ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Actions Bar -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchUsers" placeholder="Search by name, email, or role...">
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleBulkActions()">
                                <i class="fas fa-tasks"></i> Bulk Actions
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="refreshUsers()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions Bar (Hidden by default) -->
                <div id="bulkActionsBar" class="row mb-3" style="display: none;">
                    <div class="col-12">
                        <div class="alert alert-light border">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <strong><span id="selectedCount">0</span> user(s) selected</strong>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm" onclick="bulkUpdateStatus('active')">
                                        <i class="fas fa-check"></i> Activate Selected
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="bulkUpdateStatus('inactive')">
                                        <i class="fas fa-ban"></i> Deactivate Selected
                                    </button>                                    <button type="button" class="btn btn-secondary btn-sm" onclick="clearSelection()">
                                        <i class="fas fa-times"></i> Clear Selection
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Filter Bar -->
                <div class="filter-bar">
                    <form action="<?= SITE_URL ?>/admin/users" method="GET" class="row g-3 align-items-end">                        <div class="col-md-4">
                            <label class="form-label">Search Users</label>
                            <div class="search-box">
                                <input type="text" class="form-control" name="search" placeholder="Name, email, phone..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role">
                                <option value="">All Roles</option>
                                <option value="admin" <?= ($_GET['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="customer" <?= ($_GET['role'] ?? '') == 'customer' ? 'selected' : '' ?>>Customer</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Status</option>
                                <option value="active" <?= ($_GET['status'] ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= ($_GET['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <a href="<?= SITE_URL ?>/admin/users" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>                <!-- Users Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users"></i> Users List
                        </h5>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary">
                                <?php echo count($users ?? []); ?> total
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($users)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="usersTable">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Contact</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input user-checkbox"
                                                       value="<?= $user['id'] ?>">
                                            </td>
                                            <td><strong>#<?= htmlspecialchars($user['id']) ?></strong></td>
                                            <td>
                                                <div class="user-info">
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($user['avatar'])): ?>
                                                            <img src="<?= SITE_URL ?>/uploads/user_avatars/<?= htmlspecialchars($user['avatar']) ?>"
                                                                 class="avatar-sm me-3" alt="Avatar">
                                                        <?php else: ?>
                                                            <div class="avatar-placeholder me-3">
                                                                <i class="fas fa-user"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <strong><?= htmlspecialchars($user['full_name'] ?? $user['username']) ?></strong>
                                                            <small class="text-muted d-block">@<?= htmlspecialchars($user['username']) ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="contact-info">
                                                    <?php if (!empty($user['email'])): ?>
                                                        <div><i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($user['email']) ?></div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($user['phone'])): ?>
                                                        <div><i class="fas fa-phone me-1"></i> <?= htmlspecialchars($user['phone']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="role-badge role-<?= $user['role'] ?>">
                                                    <i class="fas fa-<?= $user['role'] == 'admin' ? 'user-shield' : 'user' ?> me-1"></i>
                                                    <?= ucfirst($user['role']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?= $user['status'] ?>">
                                                    <?= ucfirst($user['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($user['last_login'])): ?>
                                                    <?= date('M d, Y', strtotime($user['last_login'])) ?>
                                                    <small class="text-muted d-block">
                                                        <?= date('g:i A', strtotime($user['last_login'])) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted">Never</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="<?= SITE_URL ?>/admin/users/edit/<?= $user['id'] ?>"
                                                       class="btn btn-sm btn-outline-primary"
                                                       data-bs-toggle="tooltip" title="Edit User">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <?php if ($user['status'] == 'active'): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                                onclick="toggleUserStatus(<?= $user['id'] ?>, 'inactive')"
                                                                data-bs-toggle="tooltip" title="Deactivate User">
                                                            <i class="fas fa-user-slash"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-outline-success"
                                                                onclick="toggleUserStatus(<?= $user['id'] ?>, 'active')"
                                                                data-bs-toggle="tooltip" title="Activate User">
                                                            <i class="fas fa-user-check"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                            onclick="viewUserDetails(<?= $user['id'] ?>)"
                                                            data-bs-toggle="tooltip" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>

                                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                                                onclick="deleteUser(<?= $user['id'] ?>)"
                                                                data-bs-toggle="tooltip" title="Delete User">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No Users Found</h5>
                                                <p class="text-muted">Start by adding your first user.</p>
                                                <a href="<?= SITE_URL ?>/admin/users/create" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>Add User
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>                    <!-- Pagination -->
                    <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
                        <div class="pagination-container">
                            <nav aria-label="Users pagination">
                                <ul class="pagination justify-content-center">
                                    <?php if (($pagination['current_page'] ?? 1) > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= ($pagination['current_page'] ?? 1) - 1 ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= ($pagination['total_pages'] ?? 1); $i++): ?>
                                        <li class="page-item <?= $i == ($pagination['current_page'] ?? 1) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if (($pagination['current_page'] ?? 1) < ($pagination['total_pages'] ?? 1)): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= ($pagination['current_page'] ?? 1) + 1 ?>">Next</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user me-2"></i>User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="userDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                    <p class="text-muted"><small>Note: This will also remove all associated orders and bookings.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="" method="POST" id="deleteUserForm" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'views/admin/layouts/footer.php'; ?>

    <script>
        // View user details
        function viewUserDetails(userId) {
            fetch(`<?= SITE_URL ?>/admin/users/${userId}/details`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('userDetailsContent').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('userDetailsModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading user details');
                });
        }

        // Toggle user status
        function toggleUserStatus(userId, status) {
            if (confirm(`Are you sure you want to ${status} this user?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `<?= SITE_URL ?>/admin/users/${userId}/toggle-status`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = 'csrf_token';
                csrfToken.value = '<?= $_SESSION['csrf_token'] ?? '' ?>';

                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = status;

                form.appendChild(csrfToken);
                form.appendChild(statusInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Delete user
        function deleteUser(userId) {
            document.getElementById('deleteUserForm').action = `<?= SITE_URL ?>/admin/users/${userId}/delete`;
            new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
        }

        // Export users
        function exportUsers() {
            window.location.href = '<?= SITE_URL ?>/admin/users/export';
        }

        // Bulk actions (placeholder)
        function bulkActions() {
            const selectedUsers = document.querySelectorAll('.user-checkbox:checked');
            if (selectedUsers.length === 0) {
                alert('Please select users for bulk actions');
                return;
            }
            alert('Bulk actions feature coming soon!');
        }

        // Select all checkbox functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

</body>
</html>
