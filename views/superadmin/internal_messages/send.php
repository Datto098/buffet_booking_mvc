<?php require_once 'views/layouts/superadmin_header.php'; ?>
<?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

<div class="main-content fade-in">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>
                    <i class="fas fa-envelope"></i>
                    Gửi Thông Báo Nội Bộ
                </h1>
                <div class="btn-toolbar">
                    <a href="<?= SITE_URL ?>/superadmin/internal-messages/sent" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> Thông báo đã gửi
                    </a>
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

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-edit me-2"></i>Soạn thông báo mới
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="<?= SITE_URL ?>/superadmin/internal-messages/process" method="POST" enctype="multipart/form-data">
                                    <?= csrf_token_field() ?>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Tiêu đề thông báo <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="title" name="title" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="message_type" class="form-label">Loại thông báo</label>
                                                <select class="form-select" id="message_type" name="message_type">
                                                    <option value="general">Thông báo chung</option>
                                                    <option value="system_update">Cập nhật hệ thống</option>
                                                    <option value="policy_change">Thay đổi chính sách</option>
                                                    <option value="maintenance">Bảo trì</option>
                                                    <option value="personal">Thông báo cá nhân</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="priority" class="form-label">Mức độ ưu tiên</label>
                                                <select class="form-select" id="priority" name="priority">
                                                    <option value="low">Thấp</option>
                                                    <option value="normal" selected>Bình thường</option>
                                                    <option value="high">Cao</option>
                                                    <option value="urgent">Khẩn cấp</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_broadcast" name="is_broadcast">
                                                    <label class="form-check-label" for="is_broadcast">
                                                        Gửi cho tất cả Admin
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="content" class="form-label">Nội dung thông báo <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="content" name="content" rows="8" required></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="attachment" class="form-label">File đính kèm (tùy chọn)</label>
                                        <input type="file" class="form-control" id="attachment" name="attachment" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                                        <div class="form-text">Hỗ trợ: PDF, DOC, DOCX, TXT, JPG, PNG (tối đa 5MB)</div>
                                    </div>

                                    <div id="recipients-section" class="mb-3">
                                        <label class="form-label">Chọn người nhận <span class="text-danger">*</span></label>
                                        <div class="row">
                                            <?php foreach ($recipients as $recipient): ?>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input recipient-checkbox" type="checkbox"
                                                               name="recipients[]" value="<?= $recipient['id'] ?>"
                                                               id="recipient_<?= $recipient['id'] ?>">
                                                        <label class="form-check-label" for="recipient_<?= $recipient['id'] ?>">
                                                            <?= htmlspecialchars($recipient['full_name']) ?>
                                                            <small class="text-muted">(<?= $recipient['role'] ?>)</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <a href="<?= SITE_URL ?>/superadmin/internal-messages/sent" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Quay lại
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Gửi thông báo
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Hướng dẫn
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6>Loại thông báo:</h6>
                                    <ul class="list-unstyled">
                                        <li><span class="badge bg-secondary message-type-badge">Thông báo chung</span> - Thông tin chung</li>
                                        <li><span class="badge bg-info message-type-badge">Cập nhật hệ thống</span> - Thay đổi tính năng</li>
                                        <li><span class="badge bg-warning message-type-badge">Thay đổi chính sách</span> - Quy định mới</li>
                                        <li><span class="badge bg-danger message-type-badge">Bảo trì</span> - Bảo trì hệ thống</li>
                                        <li><span class="badge bg-success message-type-badge">Thông báo cá nhân</span> - Thông tin riêng</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <h6>Mức độ ưu tiên:</h6>
                                    <ul class="list-unstyled">
                                        <li><span class="badge bg-secondary priority-badge">Thấp</span> - Thông tin tham khảo</li>
                                        <li><span class="badge bg-primary priority-badge">Bình thường</span> - Thông tin quan trọng</li>
                                        <li><span class="badge bg-warning priority-badge">Cao</span> - Cần chú ý</li>
                                        <li><span class="badge bg-danger priority-badge">Khẩn cấp</span> - Cần xử lý ngay</li>
                                    </ul>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    <strong>Mẹo:</strong> Sử dụng "Gửi cho tất cả Admin" để gửi thông báo đến tất cả quản lý trong hệ thống.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle recipients section based on broadcast checkbox
        document.getElementById('is_broadcast').addEventListener('change', function() {
            const recipientsSection = document.getElementById('recipients-section');
            const recipientCheckboxes = document.querySelectorAll('.recipient-checkbox');

            if (this.checked) {
                recipientsSection.style.display = 'none';
                recipientCheckboxes.forEach(checkbox => checkbox.checked = false);
            } else {
                recipientsSection.style.display = 'block';
            }
        });

        // File preview
        document.getElementById('attachment').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('File quá lớn. Vui lòng chọn file nhỏ hơn 5MB.');
                    this.value = '';
                    return;
                }
            }
        });

        // Character counter for content
        document.getElementById('content').addEventListener('input', function() {
            const maxLength = 2000;
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;

            if (remaining < 0) {
                this.value = this.value.substring(0, maxLength);
            }
        });
    </script>

<?php require_once 'views/layouts/superadmin_footer.php'; ?>
