<?php
/**
 * Admin Payment Management View
 */

$pageTitle = 'Quản Lý Thanh Toán';
$currentPage = 'payments';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-credit-card me-2"></i><?= $pageTitle ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportPayments()">
                <i class="fas fa-download"></i> Xuất Excel
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshPayments()">
                <i class="fas fa-sync-alt"></i> Làm mới
            </button>
        </div>
    </div>
</div>

<!-- Payment Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Tổng Giao Dịch
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($stats['total_payments'] ?? 0) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Thành Công
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($stats['completed_payments'] ?? 0) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Đang Chờ
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($stats['pending_payments'] ?? 0) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Doanh Thu Hôm Nay
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($stats['today_completed_amount'] ?? 0, 0, ',', '.') ?>đ
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter me-2"></i>Bộ Lọc
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= SITE_URL ?>/admin/payments" id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <label for="status" class="form-label">Trạng Thái</label>
                    <select class="form-select" name="status" id="status">
                        <option value="">Tất cả</option>
                        <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Đang chờ</option>
                        <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Thành công</option>
                        <option value="failed" <?= ($_GET['status'] ?? '') === 'failed' ? 'selected' : '' ?>>Thất bại</option>
                        <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="payment_method" class="form-label">Phương Thức</label>
                    <select class="form-select" name="payment_method" id="payment_method">
                        <option value="">Tất cả</option>
                        <option value="vnpay" <?= ($_GET['payment_method'] ?? '') === 'vnpay' ? 'selected' : '' ?>>VNPay</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Từ Ngày</label>
                    <input type="date" class="form-control" name="date_from" id="date_from" value="<?= $_GET['date_from'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Đến Ngày</label>
                    <input type="date" class="form-control" name="date_to" id="date_to" value="<?= $_GET['date_to'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label for="search" class="form-label">Tìm Kiếm</label>
                    <input type="text" class="form-control" name="search" id="search" placeholder="Mã đơn hàng, email..." value="<?= $_GET['search'] ?? '' ?>">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Lọc
                    </button>
                    <a href="<?= SITE_URL ?>/admin/payments" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Xóa Bộ Lọc
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Payments Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list me-2"></i>Danh Sách Thanh Toán
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="paymentsTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã Đơn Hàng</th>
                        <th>Khách Hàng</th>
                        <th>Mã GD VNPay</th>
                        <th>Số Tiền</th>
                        <th>Phương Thức</th>
                        <th>Trạng Thái</th>
                        <th>Ngân Hàng</th>
                        <th>Ngày Tạo</th>
                        <th>Hoàn Thành</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?= $payment['id'] ?></td>
                                <td>
                                    <a href="<?= SITE_URL ?>/admin/orders/details/<?= $payment['order_id'] ?>"
                                       class="text-decoration-none">
                                        #<?= $payment['order_number'] ?? 'N/A' ?>
                                    </a>
                                </td>
                                <td>
                                    <div>
                                        <strong><?= htmlspecialchars($payment['customer_name'] ?? 'N/A') ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($payment['customer_email'] ?? 'N/A') ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($payment['vnp_txn_ref'] ?? 'N/A') ?></span>
                                </td>
                                <td>
                                    <strong><?= number_format(($payment['vnp_amount'] ?? 0) / 100, 0, ',', '.') ?>đ</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= strtoupper($payment['payment_method'] ?? 'N/A') ?></span>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'pending' => 'warning',
                                        'completed' => 'success',
                                        'failed' => 'danger',
                                        'cancelled' => 'secondary'
                                    ];
                                    $statusText = [
                                        'pending' => 'Đang chờ',
                                        'completed' => 'Thành công',
                                        'failed' => 'Thất bại',
                                        'cancelled' => 'Đã hủy'
                                    ];
                                    $status = $payment['payment_status'] ?? 'pending';
                                    ?>
                                    <span class="badge bg-<?= $statusClass[$status] ?? 'secondary' ?>">
                                        <?= $statusText[$status] ?? 'Không xác định' ?>
                                    </span>
                                </td>
                                <td>
                                    <?= htmlspecialchars($payment['vnp_bank_code'] ?? 'N/A') ?>
                                </td>
                                <td>
                                    <?= date('d/m/Y H:i', strtotime($payment['created_at'])) ?>
                                </td>
                                <td>
                                    <?= $payment['completed_at'] ? date('d/m/Y H:i', strtotime($payment['completed_at'])) : '-' ?>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="viewPaymentDetails(<?= $payment['id'] ?>)">
                                                    <i class="fas fa-eye"></i> Xem Chi Tiết
                                                </a>
                                            </li>
                                            <?php if ($payment['payment_status'] === 'completed'): ?>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="printPaymentReceipt(<?= $payment['id'] ?>)">
                                                    <i class="fas fa-print"></i> In Hóa Đơn
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            <?php if (in_array($payment['payment_status'], ['pending', 'failed'])): ?>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="cancelPayment(<?= $payment['id'] ?>)">
                                                    <i class="fas fa-times"></i> Hủy Giao Dịch
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Không có dữ liệu thanh toán</h5>
                                <p class="text-muted">Chưa có giao dịch thanh toán nào được thực hiện.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <nav aria-label="Payment pagination">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == ($currentPage ?? 1) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?><?= http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) ? '&' . http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)) : '' ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentDetailsModalLabel">
                    <i class="fas fa-credit-card me-2"></i>Chi Tiết Thanh Toán
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="paymentDetailsContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewPaymentDetails(paymentId) {
    $('#paymentDetailsModal').modal('show');

    fetch(`<?= SITE_URL ?>/admin/payments/details/${paymentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('paymentDetailsContent').innerHTML = data.html;
            } else {
                document.getElementById('paymentDetailsContent').innerHTML =
                    '<div class="alert alert-danger">Không thể tải thông tin chi tiết.</div>';
            }
        })
        .catch(error => {
            document.getElementById('paymentDetailsContent').innerHTML =
                '<div class="alert alert-danger">Có lỗi xảy ra khi tải dữ liệu.</div>';
        });
}

function cancelPayment(paymentId) {
    if (confirm('Bạn có chắc chắn muốn hủy giao dịch này?')) {
        fetch(`<?= SITE_URL ?>/admin/payments/cancel/${paymentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= $_SESSION['csrf_token'] ?? '' ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Giao dịch đã được hủy thành công', 'success');
                location.reload();
            } else {
                showNotification(data.message || 'Có lỗi xảy ra', 'error');
            }
        })
        .catch(error => {
            showNotification('Có lỗi xảy ra khi hủy giao dịch', 'error');
        });
    }
}

function printPaymentReceipt(paymentId) {
    window.open(`<?= SITE_URL ?>/admin/payments/print/${paymentId}`, '_blank');
}

function exportPayments() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);

    window.open(`<?= SITE_URL ?>/admin/payments/export?${params.toString()}`, '_blank');
}

function refreshPayments() {
    location.reload();
}

// Initialize DataTable if available
$(document).ready(function() {
    if (typeof $.fn.dataTable !== 'undefined') {
        $('#paymentsTable').DataTable({
            "pageLength": 25,
            "order": [[ 8, "desc" ]], // Sort by created_at desc
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Vietnamese.json"
            }
        });
    }
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.text-xs {
    font-size: 0.7rem;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}
</style>
