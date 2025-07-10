<?php require_once 'views/layouts/superadmin_header.php'; ?>
<?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

<div class="main-content fade-in">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>
                    <i class="fas fa-envelope"></i>
                    Chi Tiết Thông Báo
                </h1>
                <div class="btn-toolbar">
                    <a href="<?= SITE_URL ?>/superadmin/internal-messages/sent" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <a href="<?= SITE_URL ?>/superadmin/internal-messages/send" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Gửi thông báo mới
                    </a>
                </div>
            </div>
        </div>

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Message Details -->
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-envelope me-2"></i>Thông tin thông báo
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
                            </div>
                            <div class="card-body">
                                <h4 class="card-title mb-3"><?= htmlspecialchars($message['title']) ?></h4>

                                <div class="content-box">
                                    <?= nl2br(htmlspecialchars($message['content'])) ?>
                                </div>

                                <?php if ($message['attachment_path']): ?>
                                <div class="mb-3">
                                    <h6><i class="fas fa-paperclip me-2"></i>File đính kèm:</h6>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file me-2"></i>
                                        <span class="me-3"><?= htmlspecialchars($message['attachment_name']) ?></span>
                                        <a href="<?= SITE_URL ?>/superadmin/internal-messages/download-attachment/<?= $message['id'] ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> Tải xuống
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            Người gửi: <?= htmlspecialchars($message['sender_name']) ?>
                                        </small>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            Gửi lúc: <?= date('d/m/Y H:i:s', strtotime($message['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Recipients Information -->
                        <?php if (!empty($recipients)): ?>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-users me-2"></i>Người nhận (<?= count($recipients) ?>)
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6>Danh sách người nhận:</h6>
                                    <?php foreach ($recipients as $recipient): ?>
                                        <div class="recipient-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?= htmlspecialchars($recipient['full_name']) ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= $recipient['email'] ?></small>
                                                    <br>
                                                    <span class="badge bg-secondary"><?= $recipient['role'] ?></span>
                                                </div>
                                                <div class="text-end">
                                                    <?php if ($recipient['is_read']): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check"></i> Đã đọc
                                                        </span>
                                                        <br>
                                                        <small class="text-muted">
                                                            <?= date('d/m/Y H:i', strtotime($recipient['read_at'])) ?>
                                                        </small>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-clock"></i> Chưa đọc
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php
                                $readCount = count(array_filter($recipients, function($r) { return $r['is_read']; }));
                                $unreadCount = count($recipients) - $readCount;
                                ?>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <h6 class="text-success mb-0"><?= $readCount ?></h6>
                                            <small class="text-muted">Đã đọc</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <h6 class="text-warning mb-0"><?= $unreadCount ?></h6>
                                            <small class="text-muted">Chưa đọc</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Message Statistics -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-bar me-2"></i>Thống kê
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <h6 class="text-primary mb-0"><?= count($recipients) ?></h6>
                                            <small class="text-muted">Tổng người nhận</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <h6 class="text-info mb-0">
                                                <?= $message['is_broadcast'] ? 'Có' : 'Không' ?>
                                            </h6>
                                            <small class="text-muted">Gửi tất cả</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'views/layouts/superadmin_footer.php'; ?>
