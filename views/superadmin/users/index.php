<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'views/layouts/superadmin_header.php'; ?>
    <title>User Management - Super Admin</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Page Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2">
                            <i class="fas fa-users me-2 text-primary"></i>User Management
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/superadmin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Users</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-outline-primary me-2" onclick="refreshPage()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                        <a href="<?= SITE_URL ?>/superadmin/users/create" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Add New User
                        </a>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php
                $flash = $_SESSION['flash'] ?? [];
                foreach ($flash as $type => $message):
                ?>
                    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?= $type === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php
                endforeach;
                unset($_SESSION['flash']);
                ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Customers</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['customers'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-user fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Managers</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['managers'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-user-tie fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Super Admins</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['super_admins'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-crown fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Users</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['total'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-users fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                </div>

                <!-- Filter Bar -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-filter me-2 text-primary"></i>Filters
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?= SITE_URL ?>/superadmin/users" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Search Users</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" class="form-control" name="search" placeholder="Name, email, phone..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Role</label>
                                <select class="form-select" name="role">
                                    <option value="">All Roles</option>
                                    <option value="customer" <?php echo (isset($_GET['role']) && $_GET['role'] == 'customer') ? 'selected' : ''; ?>>Customer</option>
                                    <option value="manager" <?php echo (isset($_GET['role']) && $_GET['role'] == 'manager') ? 'selected' : ''; ?>>Manager</option>
                                    <option value="super_admin" <?php echo (isset($_GET['role']) && $_GET['role'] == 'super_admin') ? 'selected' : ''; ?>>Super Admin</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">All Status</option>
                                    <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="<?= SITE_URL ?>/superadmin/users" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-bold">
                                <i class="fas fa-users me-2 text-primary"></i>All Users
                            </h6>
                            <div class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>
                                <?php echo count($users ?? []); ?> users displayed
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 fw-bold">ID</th>
                                        <th class="border-0 fw-bold">User</th>
                                        <th class="border-0 fw-bold">Contact</th>
                                        <th class="border-0 fw-bold">Role</th>
                                        <th class="border-0 fw-bold">Status</th>
                                        <th class="border-0 fw-bold">Created</th>
                                        <th class="border-0 fw-bold text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-2">
                                                    <?php if (!empty($user['avatar'])): ?>
                                                        <img src="<?php echo $user['avatar']; ?>" alt="Avatar" class="rounded-circle" width="32" height="32">
                                                    <?php else: ?>
                                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                            <i class="fas fa-user text-white"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>                                                <div>
                                                    <div class="fw-bold"><?php echo htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'Unknown User'); ?></div>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo getRoleBadgeColor($user['role']); ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?>
                                            </span>
                                        </td>                                        <td>
                                            <span class="badge bg-<?php echo ($user['is_active'] ?? 0) == 1 ? 'success' : 'secondary'; ?>">
                                                <?php echo ($user['is_active'] ?? 0) == 1 ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= SITE_URL ?>/superadmin/users/edit/<?php echo $user['id']; ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteUser(<?php echo $user['id']; ?>)">
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
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No users found</h5>
                                        <p class="text-muted">Try adjusting your filters or add a new user.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                    <nav aria-label="User pagination">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>&<?php echo http_build_query($_GET); ?>">Previous</a>
                            </li>

                            <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                                <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query($_GET); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?php echo $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>&<?php echo http_build_query($_GET); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
        </main>
    </div>
</div>

<script>
    // Get CSRF token from meta tag
    function getCSRFToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        return metaTag ? metaTag.getAttribute('content') : '';
    }

    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            fetch(`<?= SITE_URL ?>/superadmin/users/delete/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'csrf_token=' + encodeURIComponent(getCSRFToken())
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting user: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting user');
                });
        }
    }
</script>

<?php
// Helper functions for user management
function getRoleIcon($role) {
    switch($role) {
        case 'super_admin': return 'crown';
        case 'manager': return 'user-tie';
        case 'customer': return 'user';
        default: return 'user';
    }
}

function getRoleBadgeColor($role) {
    switch($role) {
        case 'super_admin': return 'warning';
        case 'manager': return 'info';
        case 'customer': return 'success';
        default: return 'secondary';
    }
}
?>

</main>
</div>
</div>
</div>

<?php require_once 'views/layouts/superadmin_footer.php'; ?>
