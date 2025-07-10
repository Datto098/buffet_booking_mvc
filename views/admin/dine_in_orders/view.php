<?php require_once 'views/admin/layouts/header.php'; ?>
<?php
function getStatusClass($status) {
    switch($status) {
        case 'pending': return 'bg-warning';
        case 'preparing': return 'bg-info';
        case 'ready': return 'bg-success';
        case 'served': return 'bg-primary';
        case 'completed': return 'bg-secondary';
        case 'cancelled': return 'bg-danger';
        default: return 'bg-secondary';
    }
}
function getStatusText($status) {
    switch($status) {
        case 'pending': return 'Chờ xác nhận';
        case 'preparing': return 'Đang chế biến';
        case 'ready': return 'Sẵn sàng';
        case 'served': return 'Đã phục vụ';
        case 'completed': return 'Hoàn thành';
        case 'cancelled': return 'Đã hủy';
        default: return $status;
    }
}
?>
<div class="container py-4">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2>Chi tiết đơn gọi món #<?= $order['id'] ?></h2>
            <p>Bàn: <b><?= htmlspecialchars($order['table_number']) ?></b></p>
            <p>Trạng thái: <span class="badge <?= getStatusClass($order['status']) ?>"><?= getStatusText($order['status']) ?></span></p>
            <p>Thời gian: <?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></p>
            <p>Ghi chú: <?= htmlspecialchars($order['notes']) ?></p>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= SITE_URL ?>/admin/dine-in-orders" class="btn btn-secondary mb-2"><i class="fas fa-arrow-left"></i> Quay lại</a>
            <button class="btn btn-warning mb-2" onclick="showUpdateStatusModal(<?= $order['id'] ?>, '<?= $order['status'] ?>')"><i class="fas fa-edit"></i> Cập nhật trạng thái</button>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header"><b>Danh sách món đã gọi</b></div>
        <div class="card-body p-0">
            <?php if (!empty($order['items'])): ?>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tên món</th>
                                <th>Ảnh</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['food_name']) ?></td>
                                    <td><img src="<?= SITE_URL ?>/uploads/food_images/<?= $item['image'] ?>" width="60"></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= number_format($item['price'], 0, ',', '.') ?> ₫</td>
                                    <td><?= number_format($item['total'], 0, ',', '.') ?> ₫</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info m-0">Chưa có món nào.</div>
            <?php endif; ?>
        </div>
        <div class="card-footer text-end">
            <b>Tổng cộng: <?= number_format($order['total_amount'], 0, ',', '.') ?> ₫</b>
        </div>
    </div>
</div>
<script>
// Sử dụng lại modal cập nhật trạng thái từ index.php
</script>
<?php require_once 'views/admin/layouts/footer.php'; ?>
