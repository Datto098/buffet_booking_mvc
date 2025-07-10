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
        border-left: 4px solid #dee2e6;
    }

    .message-card.unread {
        border-left-color: #007bff;
        background-color: #f8f9fa;
    }

    .message-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-envelope me-2"></i>Thông Báo Nội Bộ
        <?php if ($unreadCount > 0): ?>
            <span class="badge bg-danger ms-2"><?= $unreadCount ?> chưa đọc</span>
        <?php endif; ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshMessages()">
                <i class="fas fa-sync-alt"></i> Làm mới
            </button>
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

<!-- Messages List -->
<div class="row">
    <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $message): ?>
            <div class="col-12 mb-3">
                <div class="card message-card <?= !$message['is_read'] ? 'unread' : '' ?>">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">
                                        <a href="<?= SITE_URL ?>/admin/internal-messages/view/<?= $message['id'] ?>"
                                            class="text-decoration-none <?= !$message['is_read'] ? 'fw-bold' : '' ?>">
                                            <?= htmlspecialchars($message['title']) ?>
                                            <?php if (!$message['is_read']): ?>
                                                <span class="badge bg-primary ms-2">Mới</span>
                                            <?php endif; ?>
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
                                        <i class="fas fa-user me-1"></i>
                                        Từ: <?= htmlspecialchars($message['sender_name']) ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?= date('d/m/Y H:i', strtotime($message['created_at'])) ?>
                                        <?php if ($message['is_read']): ?>
                                            <span class="text-success ms-2">
                                                <i class="fas fa-check"></i> Đã đọc
                                            </span>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-4 text-end">
                                <div class="btn-group-vertical">
                                    <a href="<?= SITE_URL ?>/admin/internal-messages/view/<?= $message['id'] ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </a>
                                    <?php if ($message['attachment_path']): ?>
                                        <a href="<?= SITE_URL ?>/admin/internal-messages/download-attachment/<?= $message['id'] ?>"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-download"></i> Tải file
                                        </a>
                                    <?php endif; ?>
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
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có thông báo nào</h5>
                    <p class="text-muted">Bạn sẽ nhận được thông báo từ Super Admin tại đây</p>
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

<script>
    function refreshMessages() {
        location.reload();
    }

    // Auto refresh unread count every 30 seconds, but with throttling for performance
    let lastUpdateTime = Date.now();
    let updateTimer = null;

    function updateUnreadCount() {
        // Không cập nhật nếu người dùng đang tương tác với trang
        if (document.hidden || Date.now() - lastUpdateTime < 15000) {
            return;
        }

        lastUpdateTime = Date.now();

        // Sử dụng AbortController để có thể hủy fetch request nếu cần
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 5000); // Timeout sau 5 giây

        fetch('<?= SITE_URL ?>/admin/internal-messages/get-unread-count', {
            signal: controller.signal
        })
            .then(response => response.json())
            .then(data => {
                clearTimeout(timeoutId);
                const badge = document.querySelector('.nav-link.active .badge');
                if (data.count > 0) {
                    if (badge) {
                        badge.textContent = data.count;
                    } else {
                        const navLink = document.querySelector('.nav-link.active');
                        if (navLink) {
                            navLink.innerHTML += `<span class="badge bg-danger ms-2">${data.count}</span>`;
                        }
                    }
                } else {
                    if (badge) {
                        badge.remove();
                    }
                }
            })
            .catch(error => {
                if (error.name === 'AbortError') {
                    console.log('Fetch aborted due to timeout');
                }
            });
    }

    // Cập nhật khi tab được kích hoạt lại
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            updateUnreadCount();
        }
    });

    // Cập nhật định kỳ nhưng với thời gian dài hơn
    setInterval(updateUnreadCount, 60000);
</script>
