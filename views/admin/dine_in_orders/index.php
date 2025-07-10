<?php require_once 'views/admin/layouts/header.php'; ?>
<?php
function buildPageUrl($page) {
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}

function getStatusClass($status)
{
    switch ($status) {
        case 'pending':
            return 'bg-warning';
        case 'preparing':
            return 'bg-info';
        case 'ready':
            return 'bg-success';
        case 'served':
            return 'bg-primary';
        case 'completed':
            return 'bg-secondary';
        case 'cancelled':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}
function getStatusText($status)
{
    switch ($status) {
        case 'pending':
            return 'Chờ xác nhận';
        case 'preparing':
            return 'Đang chế biến';
        case 'ready':
            return 'Sẵn sàng';
        case 'served':
            return 'Đã phục vụ';
        case 'completed':
            return 'Hoàn thành';
        case 'cancelled':
            return 'Đã hủy';
        default:
            return $status;
    }
}
?>
<?php require_once 'views/admin/layouts/sidebar.php'; ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <!-- Custom CSS to fix search box styling -->
    <style>
        .filter-bar .search-box {
            position: relative;
        }
        .filter-bar .search-box::before {
            content: '\f002';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--admin-text-secondary, #858796);
            z-index: 2;
        }
        .filter-bar .search-box .form-control {
            padding-left: 2.5rem;
        }
    </style>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">Quản lý đơn gọi món tại bàn</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Dine-in Orders</li>
                </ol>
            </nav>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportDineInOrders()">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success'];
                                                unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error'];
                                                        unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <div class="filter-bar mb-4">
        <form action="<?= SITE_URL ?>/admin/dine-in-orders" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tìm kiếm</label>
                <div class="search-box">
                    <input type="text" class="form-control" name="search" placeholder="Bàn, ghi chú..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Bàn</label>
                <select class="form-select" name="table">
                    <option value="">Tất cả</option>
                <?php foreach ($tables as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= ($_GET['table'] ?? '') == $t['id'] ? 'selected' : '' ?>><?= $t['table_number'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Trạng thái</label>
            <select class="form-select" name="status">
                <option value="">Tất cả</option>
                <option value="pending" <?= ($_GET['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                <option value="processing" <?= ($_GET['status'] ?? '') == 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                <option value="completed" <?= ($_GET['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                <option value="cancelled" <?= ($_GET['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-filter me-1"></i>Lọc
            </button>
            <a href="<?= SITE_URL ?>/admin/dine-in-orders" class="btn btn-outline-secondary">
                <i class="fas fa-times me-1"></i>Clear
            </a>
        </div>
    </form>
</div>

    <!-- Table -->
    <div class="card" style="margin-top: 40px;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-utensils"></i> Danh sách đơn gọi món tại bàn
            </h5>
            <div class="d-flex gap-2">
                <span class="badge bg-primary">
                    <?= $totalOrders ?> tổng đơn
                </span>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($orders)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="dineInOrdersTable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Bàn</th>
                                <th>Trạng thái</th>
                                <th>Tổng tiền</th>
                                <th>Thời gian</th>
                                <th>Ghi chú</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['table_number']) ?></td>
                                    <td><span class="badge <?= getStatusClass($order['status']) ?>"><?= getStatusText($order['status']) ?></span></td>
                                    <td><?= number_format($order['total_amount'], 0, ',', '.') ?> ₫</td>
                                    <td><?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></td>
                                    <td><?= htmlspecialchars($order['notes']) ?></td>
                                    <td>
                                        <a href="<?= SITE_URL ?>/admin/dine-in-orders/view?id=<?= $order['id'] ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                        <button class="btn btn-sm btn-warning" onclick="showUpdateStatusModal(<?= $order['id'] ?>, '<?= $order['status'] ?>')"><i class="fas fa-edit"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Chưa có đơn nào.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pagination -->
    <?php
    $totalPages = ceil($totalOrders / 20);
    $currentPage = max(1, intval($_GET['page'] ?? 1));
    if ($totalPages > 1): ?>
        <nav class="mt-3">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="<?= buildPageUrl($i) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</main>

<!-- Modal cập nhật trạng thái -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cập nhật trạng thái đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm">
                    <input type="hidden" name="id" id="updateOrderId">
                    <div class="mb-3">
                        <label class="form-label">Trạng thái mới</label>
                        <select class="form-select" name="status" id="updateOrderStatus">
                            <option value="pending">Chờ xác nhận</option>
                            <option value="processing">Đang xử lý</option>
                            <option value="completed">Hoàn thành</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="submitUpdateStatus()">Cập nhật</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showUpdateStatusModal(id, status) {
        document.getElementById('updateOrderId').value = id;
        document.getElementById('updateOrderStatus').value = status;
        var modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
        modal.show();
    }

    function submitUpdateStatus() {
        var form = document.getElementById('updateStatusForm');
        var formData = new FormData(form);
        fetch('<?= SITE_URL ?>/admin/dine-in-orders/update-status', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
    }

    function exportDineInOrders() {
        alert('Tính năng xuất file sẽ được bổ sung sau!');
    }

    function buildPageUrl(page) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        return url.toString();
    }
</script>
<?php require_once 'views/admin/layouts/footer.php'; ?>
