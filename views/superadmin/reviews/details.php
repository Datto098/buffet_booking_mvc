<?php
// Review Details Modal Content
if (!isset($reviewData) || !$reviewData) {
    echo '<div class="alert alert-danger">Review not found</div>';
    return;
}
?>

<div class="modal-header">
    <h5 class="modal-title">
        <i class="fas fa-star text-warning me-2"></i>Review Details
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <div class="row">
        <!-- Review Information -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-comment-alt me-2"></i>Review Content
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Rating Display -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Rating:</label>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?= $i <= $reviewData['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="badge bg-primary"><?= $reviewData['rating'] ?>/5</span>
                        </div>
                    </div>

                    <!-- Title -->
                    <?php if (!empty($reviewData['title'])): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title:</label>
                        <p class="mb-0"><?= htmlspecialchars($reviewData['title']) ?></p>
                    </div>
                    <?php endif; ?>

                    <!-- Comment -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Comment:</label>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(htmlspecialchars($reviewData['comment'])) ?>
                        </div>
                    </div>

                    <!-- Helpful Count -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Helpful Votes:</label>
                        <span class="badge bg-info"><?= $reviewData['helpful_count'] ?? 0 ?></span>
                    </div>

                    <!-- Dates -->
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Created:</label>
                            <p class="text-muted small"><?= date('M d, Y H:i', strtotime($reviewData['created_at'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Updated:</label>
                            <p class="text-muted small"><?= date('M d, Y H:i', strtotime($reviewData['updated_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Information -->
        <div class="col-md-4">
            <!-- Status Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Approval Status:</label>
                        <br>
                        <?php if ($reviewData['is_approved'] == 1): ?>
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>Approved
                            </span>
                        <?php elseif ($reviewData['is_approved'] == 0): ?>
                            <span class="badge bg-danger">
                                <i class="fas fa-times me-1"></i>Rejected
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning">
                                <i class="fas fa-clock me-1"></i>Pending
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Verification Status:</label>
                        <br>
                        <?php if ($reviewData['is_verified']): ?>
                            <span class="badge bg-info">
                                <i class="fas fa-shield-alt me-1"></i>Verified
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary">
                                <i class="fas fa-question me-1"></i>Unverified
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Customer
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <img src="<?= !empty($reviewData['user_avatar']) ? SITE_URL . '/uploads/user_avatars/' . $reviewData['user_avatar'] : SITE_URL . '/assets/images/default-avatar.png' ?>"
                             alt="Avatar" class="rounded-circle me-2" width="40" height="40">
                        <div>
                            <h6 class="mb-0"><?= htmlspecialchars($reviewData['user_name']) ?></h6>
                            <small class="text-muted"><?= htmlspecialchars($reviewData['user_email']) ?></small>
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-phone me-1"></i><?= htmlspecialchars($reviewData['user_phone'] ?? 'N/A') ?>
                        </small>
                    </div>
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>Member since <?= date('M Y', strtotime($reviewData['user_created_at'])) ?>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Food Item Information -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-utensils me-2"></i>Food Item
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <img src="<?= !empty($reviewData['food_image']) ? SITE_URL . '/uploads/food_images/' . $reviewData['food_image'] : SITE_URL . '/assets/images/food-placeholder.svg' ?>"
                             alt="Food" class="rounded me-2" width="50" height="50" style="object-fit: cover;">
                        <div>
                            <h6 class="mb-0"><?= htmlspecialchars($reviewData['food_name']) ?></h6>
                            <small class="text-muted"><?= htmlspecialchars($reviewData['category_name'] ?? 'N/A') ?></small>
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-tag me-1"></i>$<?= number_format($reviewData['food_price'], 2) ?>
                        </small>
                    </div>
                    <?php if (!empty($reviewData['order_id'])): ?>
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-receipt me-1"></i>Order #<?= $reviewData['order_id'] ?>
                        </small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <div class="d-flex justify-content-between w-100">
        <div>
            <!-- Action Buttons -->
            <?php if ($reviewData['is_approved'] !== 1): ?>
                <button type="button" class="btn btn-success btn-sm" onclick="approveReview(<?= $reviewData['id'] ?>)" data-bs-dismiss="modal">
                    <i class="fas fa-check me-1"></i>Approve
                </button>
            <?php endif; ?>

            <?php if ($reviewData['is_approved'] !== 0): ?>
                <button type="button" class="btn btn-danger btn-sm" onclick="rejectReview(<?= $reviewData['id'] ?>)" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Reject
                </button>
            <?php endif; ?>

            <?php if (!$reviewData['is_verified']): ?>
                <button type="button" class="btn btn-info btn-sm" onclick="verifyReview(<?= $reviewData['id'] ?>)" data-bs-dismiss="modal">
                    <i class="fas fa-shield-alt me-1"></i>Verify
                </button>
            <?php endif; ?>

            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteReview(<?= $reviewData['id'] ?>)" data-bs-dismiss="modal">
                <i class="fas fa-trash me-1"></i>Delete
            </button>
        </div>
        <div>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>
