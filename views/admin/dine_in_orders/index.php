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
        case 'served':
            return 'bg-success';
        case 'completed':
            return 'bg-success';
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
            return 'Chờ xử lý';
        case 'preparing':
            return 'Đang chuẩn bị';
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

        /* Enhanced table styling */
        #dineInOrdersTable {
            font-size: 0.9rem;
        }

        #dineInOrdersTable th {
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            font-weight: 600;
            color: #5a5c69;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        #dineInOrdersTable td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #e3e6f0;
        }

        #dineInOrdersTable tbody tr:hover {
            background-color: #f8f9fc;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-group .btn {
            margin-right: 2px;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        /* Status indicator animation */
        @keyframes pulse-warning {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .position-absolute.bg-warning {
            animation: pulse-warning 2s infinite;
        }

        /* Responsive table */
        @media (max-width: 1200px) {
            #dineInOrdersTable {
                font-size: 0.8rem;
            }

            #dineInOrdersTable td {
                padding: 0.75rem 0.5rem;
            }
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
                <option value="pending" <?= ($_GET['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                <option value="preparing" <?= ($_GET['status'] ?? '') == 'preparing' ? 'selected' : '' ?>>Đang chuẩn bị</option>
                <option value="served" <?= ($_GET['status'] ?? '') == 'served' ? 'selected' : '' ?>>Đã phục vụ</option>
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
                                <th width="60">ID</th>
                                <th width="80">Bàn</th>
                                <th width="100">Khách hàng</th>
                                <th width="120">Trạng thái</th>
                                <th width="80">Số món</th>
                                <th width="100">Tổng tiền</th>
                                <th width="140">Thời gian tạo</th>
                                <th width="140">Cập nhật lần cuối</th>
                                <th width="150">Ghi chú</th>
                                <th width="120">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><strong>#<?= $order['id'] ?></strong></td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-chair"></i> <?= htmlspecialchars($order['table_number']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($order['customer_name'])): ?>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user text-muted me-1"></i>
                                                <small><?= htmlspecialchars($order['customer_name']) ?></small>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted"><i class="fas fa-user-times"></i> Khách vãng lai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= getStatusClass($order['status']) ?> position-relative">
                                            <?= getStatusText($order['status']) ?>
                                            <?php if ($order['status'] === 'preparing'): ?>
                                                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-warning border border-light rounded-circle">
                                                    <span class="visually-hidden">Đang xử lý</span>
                                                </span>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="fas fa-utensils"></i> <?= $order['item_count'] ?? 0 ?> món
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-success">
                                            <?= number_format($order['total_amount'], 0, ',', '.') ?> ₫
                                        </strong>
                                    </td>
                                    <td>
                                        <div class="text-nowrap">
                                            <i class="fas fa-clock text-muted me-1"></i>
                                            <small><?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($order['updated_at']) && $order['updated_at'] !== $order['created_at']): ?>
                                            <div class="text-nowrap">
                                                <i class="fas fa-sync-alt text-primary me-1"></i>
                                                <small class="text-primary"><?= date('H:i d/m/Y', strtotime($order['updated_at'])) ?></small>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($order['notes'])): ?>
                                            <span class="text-truncate d-inline-block" style="max-width: 120px;" title="<?= htmlspecialchars($order['notes']) ?>">
                                                <i class="fas fa-sticky-note text-warning me-1"></i>
                                                <?= htmlspecialchars($order['notes']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= SITE_URL ?>/admin/dine-in-orders/view?id=<?= $order['id'] ?>"
                                               class="btn btn-sm btn-info"
                                               title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-sm btn-warning"
                                                    onclick="showUpdateStatusModal(<?= $order['id'] ?>, '<?= $order['status'] ?>')"
                                                    title="Cập nhật trạng thái">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($order['status'] === 'completed'): ?>
                                                <a href="<?= SITE_URL ?>/admin/invoice/create?order_id=<?= $order['id'] ?>"
                                                   class="btn btn-sm btn-primary"
                                                   title="Tạo hóa đơn">
                                                    <i class="fas fa-receipt"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($order['status'] !== 'completed' && $order['status'] !== 'cancelled'): ?>
                                                <button class="btn btn-sm btn-success"
                                                        onclick="quickUpdateStatus(<?= $order['id'] ?>, 'completed')"
                                                        title="Hoàn thành">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
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
                        <?php
                        // Lấy trạng thái hiện tại từ JS khi mở modal, nhưng khi render lần đầu thì chưa có
                        // Nên sẽ render tất cả, và dùng JS để disable option nếu cần
                        ?>
                        <select class="form-select" name="status" id="updateOrderStatus" required>
                            <option value="pending">Chờ xử lý</option>
                            <option value="preparing">Đang chuẩn bị</option>
                            <option value="served">Đã phục vụ</option>
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
        var select = document.getElementById('updateOrderStatus');
        select.value = status;
        // Disable option 'pending' nếu trạng thái hiện tại khác 'pending'
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].value === 'pending') {
                if (status !== 'pending') {
                    select.options[i].disabled = true;
                    select.options[i].style.opacity = 0.5;
                } else {
                    select.options[i].disabled = false;
                    select.options[i].style.opacity = 1;
                }
            }
        }
        var modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
        modal.show();
    }

    function submitUpdateStatus() {
        var form = document.getElementById('updateStatusForm');
        var formData = new FormData(form);

        // Thêm CSRF token
        formData.append('csrf_token', '<?= csrf_token() ?>');

        // Disable button để tránh double-click
        var submitBtn = document.querySelector('#updateStatusModal .btn-primary');
        var originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang cập nhật...';

        fetch('<?= SITE_URL ?>/admin/dine-in-orders/update-status', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;

                if (data.success) {
                    // Cập nhật trạng thái trực tiếp trong bảng
                    var orderId = formData.get('id');
                    var newStatus = formData.get('status');
                    updateOrderStatusInTable(orderId, newStatus);

                    // Đóng modal
                    var modal = bootstrap.Modal.getInstance(document.getElementById('updateStatusModal'));
                    modal.hide();

                    // Hiển thị thông báo thành công
                    showSuccessMessage('Cập nhật trạng thái thành công!');
                } else {
                    showErrorMessage(data.message || 'Cập nhật thất bại!');
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                console.error('Error:', error);
                showErrorMessage('Có lỗi xảy ra khi cập nhật trạng thái!');
            });
    }    function updateOrderStatusInTable(orderId, newStatus) {
        // Tìm hàng chứa order này
        var table = document.getElementById('dineInOrdersTable');
        var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var firstCell = row.getElementsByTagName('td')[0];

            if (firstCell && firstCell.textContent.includes('#' + orderId)) {
                // Tìm cột trạng thái (cột thứ 4, index 3)
                var statusCell = row.getElementsByTagName('td')[3];
                if (statusCell) {
                    // Cập nhật nội dung trạng thái
                    var statusText = getStatusTextJS(newStatus);
                    var statusClass = getStatusClassJS(newStatus);
                    var statusHtml = '<span class="badge ' + statusClass + ' position-relative">' + statusText;

                    if (newStatus === 'preparing') {
                        statusHtml += '<span class="position-absolute top-0 start-100 translate-middle p-1 bg-warning border border-light rounded-circle">' +
                                     '<span class="visually-hidden">Đang xử lý</span></span>';
                    }
                    statusHtml += '</span>';

                    statusCell.innerHTML = statusHtml;

                    // Cập nhật thời gian cập nhật (cột thứ 8, index 7)
                    var updateTimeCell = row.getElementsByTagName('td')[7];
                    if (updateTimeCell) {
                        var now = new Date();
                        var timeString = now.getHours().toString().padStart(2, '0') + ':' +
                                       now.getMinutes().toString().padStart(2, '0') + ' ' +
                                       now.getDate().toString().padStart(2, '0') + '/' +
                                       (now.getMonth() + 1).toString().padStart(2, '0') + '/' +
                                       now.getFullYear();

                        updateTimeCell.innerHTML = '<div class="text-nowrap">' +
                                                  '<i class="fas fa-sync-alt text-primary me-1"></i>' +
                                                  '<small class="text-primary">' + timeString + '</small>' +
                                                  '</div>';
                    }

                    // Thêm hiệu ứng highlight
                    row.style.backgroundColor = '#d4edda';
                    setTimeout(function() {
                        row.style.backgroundColor = '';
                    }, 2000);
                }
                break;
            }
        }
    }

    function getStatusTextJS(status) {
        switch (status) {
            case 'pending': return 'Chờ xác nhận';
            case 'processing': return 'Đang xử lý';
            case 'preparing': return 'Đang chế biến';
            case 'ready': return 'Sẵn sàng';
            case 'served': return 'Đã phục vụ';
            case 'completed': return 'Hoàn thành';
            case 'cancelled': return 'Đã hủy';
            default: return status;
        }
    }

    function getStatusClassJS(status) {
        switch (status) {
            case 'pending': return 'bg-warning';
            case 'processing':
            case 'preparing': return 'bg-info';
            case 'ready': return 'bg-success';
            case 'served': return 'bg-primary';
            case 'completed': return 'bg-secondary';
            case 'cancelled': return 'bg-danger';
            default: return 'bg-secondary';
        }
    }

    function showSuccessMessage(message) {
        // Tạo alert thành công
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show';
        alertDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + message +
                           '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';

        // Chèn vào đầu main content
        var main = document.querySelector('main');
        var firstChild = main.children[1]; // Sau breadcrumb
        main.insertBefore(alertDiv, firstChild);

        // Tự động ẩn sau 3 giây
        setTimeout(function() {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 3000);
    }

    function showErrorMessage(message) {
        // Tạo alert lỗi
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message +
                           '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';

        // Chèn vào đầu main content
        var main = document.querySelector('main');
        var firstChild = main.children[1]; // Sau breadcrumb
        main.insertBefore(alertDiv, firstChild);

        // Tự động ẩn sau 5 giây
        setTimeout(function() {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    function exportDineInOrders() {
        alert('Tính năng xuất file sẽ được bổ sung sau!');
    }

    function buildPageUrl(page) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        return url.toString();
    }

    function quickUpdateStatus(orderId, newStatus) {
        if (!confirm('Bạn có chắc chắn muốn cập nhật trạng thái này?')) {
            return;
        }

        var formData = new FormData();
        formData.append('id', orderId);
        formData.append('status', newStatus);
        formData.append('csrf_token', '<?= csrf_token() ?>');

        fetch('<?= SITE_URL ?>/admin/dine-in-orders/update-status', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    updateOrderStatusInTable(orderId, newStatus);
                    showSuccessMessage('Cập nhật trạng thái thành công!');
                } else {
                    showErrorMessage(data.message || 'Cập nhật thất bại!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('Có lỗi xảy ra khi cập nhật trạng thái!');
            });
    }
</script>
<?php require_once 'views/admin/layouts/footer.php'; ?>
