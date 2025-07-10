<?php require_once 'views/layouts/superadmin_header.php'; ?>
<?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

<div class="main-content fade-in">
    <div class="container-fluid">
        <style>
            .message-type-badge {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }
            .priority-badge {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }
            .message-card {
                transition: transform 0.2s;
            }
            .message-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }
        </style>

        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>
                    <i class="fas fa-envelope"></i>
                    Thông Báo Đã Gửi
                </h1>
                <div class="btn-toolbar">
                    <a href="<?= SITE_URL ?>/superadmin/internal-messages/send" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Gửi thông báo mới
                    </a>
                </div>
            </div>
        </div>

                <!-- Flash Messages -->
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['flash_message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php
                    unset($_SESSION['flash_message']);
                    unset($_SESSION['flash_type']);
                    ?>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <?php if (isset($stats)): ?>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title"><?= $stats['total_sent'] ?? 0 ?></h4>
                                        <p class="card-text">Tổng thông báo</p>
                                    </div>
                                    <div>
                                        <i class="fas fa-envelope fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title"><?= $stats['urgent_sent'] ?? 0 ?></h4>
                                        <p class="card-text">Khẩn cấp</p>
                                    </div>
                                    <div>
                                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title"><?= $stats['broadcast_sent'] ?? 0 ?></h4>
                                        <p class="card-text">Gửi tất cả</p>
                                    </div>
                                    <div>
                                        <i class="fas fa-broadcast-tower fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Messages List -->
                <div class="row">
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="col-12 mb-3">
                                <div class="card message-card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="card-title mb-0">
                                                        <a href="<?= SITE_URL ?>/superadmin/internal-messages/view/<?= $message['id'] ?>"
                                                           class="text-decoration-none">
                                                            <?= htmlspecialchars($message['title']) ?>
                                                        </a>
                                                    </h5>
                                                    <div class="d-flex gap-2">
                                                        <?php
                                                        $typeColors = [
                                                            'general' => 'secondary',
                                                            'system_update' => 'info',
                                                            'policy_change' => 'warning',
                                                            'maintenance' => 'danger',
                                                            'personal' => 'success'
                                                        ];
                                                        $typeColor = $typeColors[$message['message_type']] ?? 'secondary';
                                                        ?>
                                                        <span class="badge bg-<?= $typeColor ?> message-type-badge">
                                                            <?= ucfirst(str_replace('_', ' ', $message['message_type'])) ?>
                                                        </span>

                                                        <?php
                                                        $priorityColors = [
                                                            'low' => 'secondary',
                                                            'normal' => 'primary',
                                                            'high' => 'warning',
                                                            'urgent' => 'danger'
                                                        ];
                                                        $priorityColor = $priorityColors[$message['priority']] ?? 'primary';
                                                        ?>
                                                        <span class="badge bg-<?= $priorityColor ?> priority-badge">
                                                            <?= ucfirst($message['priority']) ?>
                                                        </span>
                                                    </div>
                                                </div>

                                                <p class="card-text text-muted">
                                                    <?= htmlspecialchars(substr($message['content'], 0, 150)) ?>...
                                                </p>

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-users me-1"></i>
                                                        <?= $message['recipient_count'] ?? 0 ?> người nhận
                                                        <?php if (isset($message['read_count'])): ?>
                                                            <span class="text-success">
                                                                (<?= $message['read_count'] ?> đã đọc)
                                                            </span>
                                                        <?php endif; ?>
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>
                                                        <?= date('d/m/Y H:i', strtotime($message['created_at'])) ?>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="col-md-4 text-end">
                                                <div class="btn-group-vertical gap-1">
                                                    <a href="<?= SITE_URL ?>/superadmin/internal-messages/view/<?= $message['id'] ?>"
                                                       class="btn btn-sm btn-outline-primary mb-1">
                                                        <i class="fas fa-eye"></i> Xem chi tiết
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteMessage(<?= $message['id'] ?>)"
                                                            title="Xóa thông báo">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-envelope-open fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Chưa có thông báo nào được gửi</h5>
                                    <p class="text-muted">Bắt đầu gửi thông báo đầu tiên của bạn</p>
                                    <a href="<?= SITE_URL ?>/superadmin/internal-messages/send" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Gửi thông báo mới
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if (isset($page) && isset($limit) && !empty($messages)): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?>">Trước</a>
                            </li>
                        <?php endif; ?>

                        <li class="page-item active">
                            <span class="page-link">Trang <?= $page ?></span>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">Sau</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa thông báo này? Hành động này không thể hoàn tác.</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST" class="d-flex gap-2 w-100 justify-content-end">
                        <?= csrf_token_field() ?>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Hủy
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Xác nhận xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteMessage(messageId) {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const form = document.getElementById('deleteForm');
            form.action = '<?= SITE_URL ?>/superadmin/internal-messages/delete/' + messageId;
            modal.show();
        }
    </script>

<?php require_once 'views/layouts/superadmin_footer.php'; ?>
