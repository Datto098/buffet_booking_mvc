 <style>
     .message-type-badge {
         font-size: 0.9rem;
         padding: 0.5rem 1rem;
     }

     .priority-badge {
         font-size: 0.9rem;
         padding: 0.5rem 1rem;
     }

     .content-box {
         background: #f8f9fa;
         border: 1px solid #dee2e6;
         border-radius: 0.5rem;
         padding: 1.5rem;
         margin: 1rem 0;
     }

     .attachment-box {
         background: #e9ecef;
         border: 1px solid #dee2e6;
         border-radius: 0.5rem;
         padding: 1rem;
         margin: 1rem 0;
     }
 </style>

 <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
     <h1 class="h2">
         <i class="fas fa-envelope me-2"></i>Chi Tiết Thông Báo
     </h1>
     <div class="btn-toolbar mb-2 mb-md-0">
         <div class="btn-group me-2">
             <a href="<?= SITE_URL ?>/admin/internal-messages" class="btn btn-sm btn-outline-secondary">
                 <i class="fas fa-arrow-left"></i> Quay lại
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
                     <div class="attachment-box">
                         <h6><i class="fas fa-paperclip me-2"></i>File đính kèm:</h6>
                         <div class="d-flex align-items-center">
                             <i class="fas fa-file me-2"></i>
                             <span class="me-3"><?= htmlspecialchars($message['attachment_name']) ?></span>
                             <a href="<?= SITE_URL ?>/admin/internal-messages/download-attachment/<?= $message['id'] ?>"
                                 class="btn btn-sm btn-primary">
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

                 <?php if (isset($message['read_at'])): ?>
                     <div class="row mt-2">
                         <div class="col-12 text-end">
                             <small class="text-success">
                                 <i class="fas fa-check me-1"></i>
                                 Đã đọc lúc: <?= date('d/m/Y H:i:s', strtotime($message['read_at'])) ?>
                             </small>
                         </div>
                     </div>
                 <?php endif; ?>
             </div>
         </div>
     </div>

     <div class="col-lg-4">
         <!-- Message Information -->
         <div class="card">
             <div class="card-header">
                 <h5 class="card-title mb-0">
                     <i class="fas fa-info-circle me-2"></i>Thông tin bổ sung
                 </h5>
             </div>
             <div class="card-body">
                 <div class="mb-3">
                     <h6>Loại thông báo:</h6>
                     <p class="mb-2">
                         <?php
                            $typeLabels = [
                                'general' => 'Thông báo chung',
                                'system_update' => 'Cập nhật hệ thống',
                                'policy_change' => 'Thay đổi chính sách',
                                'maintenance' => 'Bảo trì hệ thống',
                                'personal' => 'Thông báo cá nhân'
                            ];
                            echo $typeLabels[$message['message_type']] ?? 'Không xác định';
                            ?>
                     </p>
                 </div>

                 <div class="mb-3">
                     <h6>Mức độ ưu tiên:</h6>
                     <p class="mb-2">
                         <?php
                            $priorityLabels = [
                                'low' => 'Thấp - Thông tin tham khảo',
                                'normal' => 'Bình thường - Thông tin quan trọng',
                                'high' => 'Cao - Cần chú ý',
                                'urgent' => 'Khẩn cấp - Cần xử lý ngay'
                            ];
                            echo $priorityLabels[$message['priority']] ?? 'Không xác định';
                            ?>
                     </p>
                 </div>

                 <div class="mb-3">
                     <h6>Phạm vi gửi:</h6>
                     <p class="mb-2">
                         <?= $message['is_broadcast'] ? 'Gửi cho tất cả Admin' : 'Gửi cho một số Admin cụ thể' ?>
                     </p>
                 </div>

                 <?php if ($message['attachment_path']): ?>
                     <div class="mb-3">
                         <h6>File đính kèm:</h6>
                         <p class="mb-2">
                             <i class="fas fa-file me-1"></i>
                             <?= htmlspecialchars($message['attachment_name']) ?>
                         </p>
                     </div>
                 <?php endif; ?>
             </div>
         </div>

         <!-- Quick Actions -->
         <div class="card mt-3">
             <div class="card-header">
                 <h5 class="card-title mb-0">
                     <i class="fas fa-tools me-2"></i>Thao tác nhanh
                 </h5>
             </div>
             <div class="card-body">
                 <div class="d-grid gap-2">
                     <a href="<?= SITE_URL ?>/admin/internal-messages" class="btn btn-outline-secondary">
                         <i class="fas fa-list"></i> Danh sách thông báo
                     </a>
                     <?php if ($message['attachment_path']): ?>
                         <a href="<?= SITE_URL ?>/admin/internal-messages/download-attachment/<?= $message['id'] ?>"
                             class="btn btn-outline-primary">
                             <i class="fas fa-download"></i> Tải file đính kèm
                         </a>
                     <?php endif; ?>
                 </div>
             </div>
         </div>
     </div>
 </div>
