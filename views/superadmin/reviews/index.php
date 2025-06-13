<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once 'views/layouts/superadmin_header.php'; ?>
    <title>Reviews Management - Super Admin</title>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Page Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2">
                            <i class="fas fa-star me-2 text-warning"></i>Reviews Management
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/superadmin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Reviews</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-outline-primary" onclick="refreshPage()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                    </div>
                </div> <!-- Flash Messages -->
                <?php
                $flash = $_SESSION['flash'] ?? [];
                foreach ($flash as $type => $message):
                ?>
                    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?= $type === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php
                endforeach;
                unset($_SESSION['flash']);
                ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Reviews</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['total_reviews'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-comments fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Approved</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['approved_reviews'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Pending</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['pending_reviews'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-clock fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Avg Rating</div>
                                        <div class="h4 mb-0 font-weight-bold">
                                            <?= number_format($stats['average_rating'] ?? 0, 1) ?> <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-star fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-filter me-2"></i>Filters
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="approved" <?= ($filters['status'] ?? '') == 'approved' ? 'selected' : '' ?>>Approved</option>
                                    <option value="pending" <?= ($filters['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="verified" <?= ($filters['status'] ?? '') == 'verified' ? 'selected' : '' ?>>Verified</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="rating" class="form-label">Rating</label>
                                <select class="form-select" id="rating" name="rating">
                                    <option value="">All Ratings</option>
                                    <option value="5" <?= ($filters['rating'] ?? '') == '5' ? 'selected' : '' ?>>5 Stars</option>
                                    <option value="4" <?= ($filters['rating'] ?? '') == '4' ? 'selected' : '' ?>>4 Stars</option>
                                    <option value="3" <?= ($filters['rating'] ?? '') == '3' ? 'selected' : '' ?>>3 Stars</option>
                                    <option value="2" <?= ($filters['rating'] ?? '') == '2' ? 'selected' : '' ?>>2 Stars</option>
                                    <option value="1" <?= ($filters['rating'] ?? '') == '1' ? 'selected' : '' ?>>1 Star</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search"
                                    value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                                    placeholder="Search reviews, users, or food items...">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <a href="<?= SITE_URL ?>/superadmin/reviews" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Reviews Table -->
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>Reviews List
                        </h6>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="bulkAction('approve')" id="bulkApproveBtn" disabled>
                                <i class="fas fa-check me-1"></i>Approve Selected
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="bulkAction('delete')" id="bulkDeleteBtn" disabled>
                                <i class="fas fa-trash me-1"></i>Delete Selected
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </th>
                                        <th>Review</th>
                                        <th>Rating</th>
                                        <th>Customer</th>
                                        <th>Food Item</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($reviews)): ?>
                                        <?php foreach ($reviews as $review): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="form-check-input review-checkbox"
                                                        value="<?= $review['id'] ?>">
                                                </td>
                                                <td>
                                                    <div class="review-content">
                                                        <?php if (!empty($review['title'])): ?>
                                                            <div class="fw-bold text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($review['title']) ?>">
                                                                <?= htmlspecialchars($review['title']) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="text-muted small text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($review['comment'] ?? '') ?>">
                                                            <?= htmlspecialchars(substr($review['comment'] ?? '', 0, 100)) ?>
                                                            <?= strlen($review['comment'] ?? '') > 100 ? '...' : '' ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-warning">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <?php if ($i <= $review['rating']): ?>
                                                                <i class="fas fa-star"></i>
                                                            <?php else: ?>
                                                                <i class="far fa-star"></i>
                                                            <?php endif; ?>
                                                        <?php endfor; ?>
                                                        <span class="text-muted ms-1">(<?= $review['rating'] ?>)</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <i class="fas fa-user-circle fa-2x text-muted"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">
                                                                <?= htmlspecialchars(($review['first_name'] ?? '') . ' ' . ($review['last_name'] ?? '')) ?>
                                                            </div>
                                                            <small class="text-muted"><?= htmlspecialchars($review['user_email'] ?? '') ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($review['food_image'])): ?>
                                                            <img src="<?= SITE_URL ?>/uploads/food_images/<?= $review['food_image'] ?>"
                                                                alt="Food" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                                style="width: 40px; height: 40px;">
                                                                <i class="fas fa-utensils text-muted"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <div class="fw-medium text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($review['food_name'] ?? 'Unknown') ?>">
                                                                <?= htmlspecialchars($review['food_name'] ?? 'Unknown') ?>
                                                            </div>
                                                            <?php if (!empty($review['order_number'])): ?>
                                                                <small class="text-muted">Order: <?= htmlspecialchars($review['order_number']) ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column gap-1">
                                                        <span class="badge bg-<?= $review['is_approved'] ? 'success' : 'warning' ?>">
                                                            <?= $review['is_approved'] ? 'Approved' : 'Pending' ?>
                                                        </span>
                                                        <?php if ($review['is_verified']): ?>
                                                            <span class="badge bg-info">Verified</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="small">
                                                        <div><?= date('M j, Y', strtotime($review['created_at'])) ?></div>
                                                        <div class="text-muted"><?= date('g:i A', strtotime($review['created_at'])) ?></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-outline-info btn-sm"
                                                            onclick="viewReview(<?= $review['id'] ?>)" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>

                                                        <?php if (!$review['is_approved']): ?>
                                                            <button type="button" class="btn btn-outline-success btn-sm"
                                                                onclick="approveReview(<?= $review['id'] ?>)" title="Approve">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-outline-warning btn-sm"
                                                                onclick="rejectReview(<?= $review['id'] ?>)" title="Unapprove">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <?php if (!$review['is_verified']): ?>
                                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                                onclick="verifyReview(<?= $review['id'] ?>)" title="Verify">
                                                                <i class="fas fa-shield-alt"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                            onclick="deleteReview(<?= $review['id'] ?>)" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-star fa-3x mb-3"></i>
                                                    <h6>No reviews found</h6>
                                                    <p>No reviews match your current filter criteria.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Reviews pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $currentPage - 1 ?>&<?= http_build_query($_GET) ?>">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query($_GET) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $currentPage + 1 ?>&<?= http_build_query($_GET) ?>">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Review Details Modal -->
    <div class="modal fade" id="reviewDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-star me-2 text-warning"></i>Review Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reviewDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'views/layouts/superadmin_footer.php'; ?>

    <script>
        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.review-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkButtons();
        });

        // Individual checkbox change
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('review-checkbox')) {
                updateBulkButtons();
            }
        });

        function updateBulkButtons() {
            const checkedBoxes = document.querySelectorAll('.review-checkbox:checked');
            const bulkApproveBtn = document.getElementById('bulkApproveBtn');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

            if (checkedBoxes.length > 0) {
                bulkApproveBtn.disabled = false;
                bulkDeleteBtn.disabled = false;
            } else {
                bulkApproveBtn.disabled = true;
                bulkDeleteBtn.disabled = true;
            }
        }

        function viewReview(id) {
            fetch(`<?= SITE_URL ?>/superadmin/reviews/details/${id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('reviewDetailsContent').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('reviewDetailsModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Failed to load review details', 'error');
                });
        }

        function approveReview(id) {
            if (confirm('Are you sure you want to approve this review?')) {
                fetch(`<?= SITE_URL ?>/superadmin/reviews/approve/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('Review approved successfully', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showAlert(data.message || 'Failed to approve review', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('An error occurred', 'error');
                    });
            }
        }

        function rejectReview(id) {
            if (confirm('Are you sure you want to reject this review?')) {
                fetch(`<?= SITE_URL ?>/superadmin/reviews/reject/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('Review rejected successfully', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showAlert(data.message || 'Failed to reject review', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('An error occurred', 'error');
                    });
            }
        }

        function verifyReview(id) {
            if (confirm('Are you sure you want to verify this review?')) {
                fetch(`<?= SITE_URL ?>/superadmin/reviews/verify/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('Review verified successfully', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showAlert(data.message || 'Failed to verify review', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('An error occurred', 'error');
                    });
            }
        }

        function deleteReview(id) {
            if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
                fetch(`<?= SITE_URL ?>/superadmin/reviews/delete/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('Review deleted successfully', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showAlert(data.message || 'Failed to delete review', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('An error occurred', 'error');
                    });
            }
        }

        function bulkAction(action) {
            const checkedBoxes = document.querySelectorAll('.review-checkbox:checked');
            const reviewIds = Array.from(checkedBoxes).map(checkbox => checkbox.value);

            if (reviewIds.length === 0) {
                showAlert('Please select at least one review', 'warning');
                return;
            }

            const actionText = action === 'approve' ? 'approve' : 'delete';
            if (confirm(`Are you sure you want to ${actionText} ${reviewIds.length} selected review(s)?`)) {
                const formData = new FormData();
                formData.append('action', action);
                reviewIds.forEach(id => formData.append('review_ids[]', id));

                fetch(`<?= SITE_URL ?>/superadmin/reviews/bulk-action`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert(data.message, 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showAlert(data.message || `Failed to ${actionText} reviews`, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('An error occurred', 'error');
                    });
            }
        }

        function refreshPage() {
            location.reload();
        }

        function showAlert(message, type = 'info') {
            const alertContainer = document.createElement('div');
            alertContainer.className = `alert alert-${type} alert-dismissible fade show`;
            alertContainer.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            const target = document.querySelector('main .border-bottom').nextElementSibling;
            target.parentNode.insertBefore(alertContainer, target);

            setTimeout(() => {
                alertContainer.remove();
            }, 5000);
        }
    </script>
</body>

</html>
