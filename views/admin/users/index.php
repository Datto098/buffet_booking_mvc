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
                <h1 class="h2">User Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="/admin/users/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New User
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

            <!-- Stats -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="border-left-primary py-2">
                                        <div class="text-primary font-weight-bold">Total Users</div>
                                        <div class="h4"><?= number_format($data['totalUsers']) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border-left-success py-2">
                                        <div class="text-success font-weight-bold">Active Users</div>
                                        <div class="h4"><?= count(array_filter($data['users'], fn($u) => $u['is_active'])) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border-left-info py-2">
                                        <div class="text-info font-weight-bold">Customers</div>
                                        <div class="h4"><?= count(array_filter($data['users'], fn($u) => $u['role'] === 'customer')) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border-left-warning py-2">
                                        <div class="text-warning font-weight-bold">Staff</div>
                                        <div class="h4"><?= count(array_filter($data['users'], fn($u) => in_array($u['role'], ['manager', 'super_admin']))) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">All Users</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($data['users'])): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No users found.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($data['users'] as $user): ?>
                                        <tr>
                                            <td><?= $user['id'] ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <div class="avatar-title bg-primary rounded-circle">
                                                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                                        </div>
                                                    </div>
                                                    <?= htmlspecialchars($user['name']) ?>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php
                                                $roleColors = [
                                                    'customer' => 'info',
                                                    'manager' => 'warning',
                                                    'super_admin' => 'danger'
                                                ];
                                                $color = $roleColors[$user['role']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $color ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $user['role'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($user['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="/admin/users/edit/<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                                                        <form method="POST" action="/admin/users/delete/<?= $user['id'] ?>" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-delete" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($data['totalPages'] > 1): ?>
                        <nav aria-label="Users pagination">
                            <ul class="pagination justify-content-center">
                                <?php if ($data['currentPage'] > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="/admin/users?page=<?= $data['currentPage'] - 1 ?>">Previous</a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
                                    <li class="page-item <?= $i === $data['currentPage'] ? 'active' : '' ?>">
                                        <a class="page-link" href="/admin/users?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($data['currentPage'] < $data['totalPages']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="/admin/users?page=<?= $data['currentPage'] + 1 ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}
.avatar-title {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
}
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>

<?php require_once 'views/admin/layouts/footer.php'; ?>
