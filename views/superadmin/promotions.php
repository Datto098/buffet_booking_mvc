<?php require_once 'views/layouts/superadmin_header.php'; ?>
<?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

<div class="main-content fade-in">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>
                    <i class="fas fa-tags"></i>
                    Promotion Management
                </h1>
                <div class="btn-toolbar">
                    <button type="button" class="btn btn-primary" onclick="showAddPromotionModal()">
                        <i class="fas fa-plus"></i> Add New Promotion
                    </button>
                </div>
            </div>
        </div>

        <!-- Promotion Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-primary me-3">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">Total Promotions</div>
                                <div class="h4 mb-0 fw-bold text-dark"><?php echo $stats['total_promotions'] ?? 0; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-success me-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">Active</div>
                                <div class="h4 mb-0 fw-bold text-dark"><?php echo $stats['active_promotions'] ?? 0; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-warning me-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">Scheduled</div>
                                <div class="h4 mb-0 fw-bold text-dark"><?php echo $stats['scheduled_promotions'] ?? 0; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-info me-3">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">Usage Rate</div>
                                <div class="h4 mb-0 fw-bold text-dark"><?php echo number_format($stats['usage_rate'] ?? 0, 1); ?>%</div>
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
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo $stats['active_promotions'] ?? 0; ?></h4>
                                    <p class="mb-0">Active</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo $stats['expired_promotions'] ?? 0; ?></h4>
                                    <p class="mb-0">Expired</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo $stats['used_promotions'] ?? 0; ?></h4>
                                    <p class="mb-0">Times Used</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="active" <?php echo (isset($_GET['status']) && $_GET['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo (isset($_GET['status']) && $_GET['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                <option value="expired" <?php echo (isset($_GET['status']) && $_GET['status'] == 'expired') ? 'selected' : ''; ?>>Expired</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Types</option>
                                <option value="percentage" <?php echo (isset($_GET['type']) && $_GET['type'] == 'percentage') ? 'selected' : ''; ?>>Percentage</option>
                                <option value="fixed" <?php echo (isset($_GET['type']) && $_GET['type'] == 'fixed') ? 'selected' : ''; ?>>Fixed Amount</option>
                                <option value="buy_one_get_one" <?php echo (isset($_GET['type']) && $_GET['type'] == 'buy_one_get_one') ? 'selected' : ''; ?>>Buy One Get One</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   placeholder="Promotion name, code..."
                                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div>                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="<?= SITE_URL ?>/superadmin/promotions" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Promotions Grid -->
            <div class="row">
                <?php if (!empty($promotions)): ?>
                    <?php foreach ($promotions as $promotion): ?>
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card h-100 <?php echo getPromotionCardClass($promotion); ?>">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0"><?php echo htmlspecialchars($promotion['name']); ?></h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               id="promotion-status-<?php echo $promotion['id']; ?>"
                                               <?php echo $promotion['status'] === 'active' ? 'checked' : ''; ?>
                                               onchange="togglePromotionStatus(<?php echo $promotion['id']; ?>)">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <span class="badge bg-primary"><?php echo strtoupper($promotion['code']); ?></span>
                                        <span class="badge bg-<?php echo getPromotionTypeBadgeColor($promotion['type']); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $promotion['type'])); ?>
                                        </span>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Discount:</strong>
                                        <?php if ($promotion['type'] === 'percentage'): ?>
                                            <?php echo $promotion['discount_value']; ?>%
                                        <?php elseif ($promotion['type'] === 'fixed'): ?>
                                            $<?php echo number_format($promotion['discount_value'], 2); ?>
                                        <?php else: ?>
                                            Buy One Get One
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($promotion['description'])): ?>
                                        <div class="mb-3">
                                            <small class="text-muted"><?php echo htmlspecialchars($promotion['description']); ?></small>
                                        </div>
                                    <?php endif; ?>

                                    <div class="row small text-muted">
                                        <div class="col-6">
                                            <div><strong>Valid From:</strong></div>
                                            <div><?php echo date('M d, Y', strtotime($promotion['start_date'])); ?></div>
                                        </div>
                                        <div class="col-6">
                                            <div><strong>Valid Until:</strong></div>
                                            <div><?php echo date('M d, Y', strtotime($promotion['end_date'])); ?></div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row small">
                                        <div class="col-6">
                                            <div class="text-muted">Usage Limit:</div>
                                            <div class="fw-bold"><?php echo $promotion['usage_limit'] ? number_format($promotion['usage_limit']) : 'Unlimited'; ?></div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted">Used:</div>
                                            <div class="fw-bold"><?php echo number_format($promotion['used_count'] ?? 0); ?> times</div>
                                        </div>
                                    </div>

                                    <?php if (!empty($promotion['minimum_amount'])): ?>
                                        <div class="mt-2 small">
                                            <div class="text-muted">Minimum Order:</div>
                                            <div class="fw-bold">$<?php echo number_format($promotion['minimum_amount'], 2); ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer">
                                    <div class="btn-group w-100" role="group">
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                                onclick="editPromotion(<?php echo $promotion['id']; ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-sm"
                                                onclick="viewPromotionStats(<?php echo $promotion['id']; ?>)">
                                            <i class="fas fa-chart-bar"></i> Stats
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="deletePromotion(<?php echo $promotion['id']; ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No promotions found</h5>
                                <p class="text-muted">Create your first promotion to attract customers.</p>
                                <button type="button" class="btn btn-danger" onclick="showAddPromotionModal()">
                                    <i class="fas fa-plus"></i> Add First Promotion
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                <nav aria-label="Promotion pagination">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>&<?php echo http_build_query($_GET); ?>">Previous</a>
                        </li>

                        <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query($_GET); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?php echo $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>&<?php echo http_build_query($_GET); ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </main>
    </div>
</div>

<!-- Add/Edit Promotion Modal -->
<div class="modal fade" id="promotionModal" tabindex="-1" aria-labelledby="promotionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="promotionModalLabel">Add New Promotion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="promotionForm">
                    <input type="hidden" id="promotionId" name="promotion_id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="promotionName" class="form-label">Promotion Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="promotionName" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="promotionCode" class="form-label">Promotion Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="promotionCode" name="code" required style="text-transform: uppercase;">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="promotionType" class="form-label">Discount Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="promotionType" name="type" required>
                                <option value="">Select Type</option>
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed Amount</option>
                                <option value="buy_one_get_one">Buy One Get One</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="discountValue" class="form-label">Discount Value <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="discountValue" name="discount_value" min="0" step="0.01" required>
                            <div class="form-text" id="discountHelp">Enter percentage (1-100) or fixed amount</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="startDate" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="startDate" name="start_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="endDate" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="endDate" name="end_date" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="usageLimit" class="form-label">Usage Limit</label>
                            <input type="number" class="form-control" id="usageLimit" name="usage_limit" min="1">
                            <div class="form-text">Leave empty for unlimited usage</div>
                        </div>
                        <div class="col-md-6">
                            <label for="minimumAmount" class="form-label">Minimum Order Amount</label>
                            <input type="number" class="form-control" id="minimumAmount" name="minimum_amount" min="0" step="0.01">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="savePromotion()">Save Promotion</button>
            </div>
        </div>
    </div>
</div>

<script>
function showAddPromotionModal() {
    document.getElementById('promotionModalLabel').textContent = 'Add New Promotion';
    document.getElementById('promotionForm').reset();
    document.getElementById('promotionId').value = '';

    // Set default dates
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const nextMonth = new Date(today);
    nextMonth.setMonth(nextMonth.getMonth() + 1);

    document.getElementById('startDate').value = tomorrow.toISOString().split('T')[0];
    document.getElementById('endDate').value = nextMonth.toISOString().split('T')[0];

    const modal = new bootstrap.Modal(document.getElementById('promotionModal'));
    modal.show();
}

function editPromotion(promotionId) {
    fetch(`/superadmin/promotions/get/${promotionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('promotionModalLabel').textContent = 'Edit Promotion';
                document.getElementById('promotionId').value = data.promotion.id;
                document.getElementById('promotionName').value = data.promotion.name;
                document.getElementById('promotionCode').value = data.promotion.code;
                document.getElementById('promotionType').value = data.promotion.type;
                document.getElementById('discountValue').value = data.promotion.discount_value;
                document.getElementById('startDate').value = data.promotion.start_date;
                document.getElementById('endDate').value = data.promotion.end_date;
                document.getElementById('usageLimit').value = data.promotion.usage_limit || '';
                document.getElementById('minimumAmount').value = data.promotion.minimum_amount || '';
                document.getElementById('description').value = data.promotion.description || '';

                updateDiscountHelp();

                const modal = new bootstrap.Modal(document.getElementById('promotionModal'));
                modal.show();
            } else {
                alert('Error loading promotion data: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading promotion data');
        });
}

function savePromotion() {
    const form = document.getElementById('promotionForm');
    const formData = new FormData(form);
    const promotionId = document.getElementById('promotionId').value;

    const url = promotionId ? `/superadmin/promotions/edit/${promotionId}` : '/superadmin/promotions/create';

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('promotionModal'));
            modal.hide();
            location.reload();
        } else {
            alert('Error saving promotion: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving promotion');
    });
}

function togglePromotionStatus(promotionId) {
    const checkbox = document.getElementById(`promotion-status-${promotionId}`);
    const status = checkbox.checked ? 'active' : 'inactive';

    fetch(`/superadmin/promotions/toggle/${promotionId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            // Revert checkbox state
            checkbox.checked = !checkbox.checked;
            alert('Error updating promotion status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert checkbox state
        checkbox.checked = !checkbox.checked;
        alert('Error updating promotion status');
    });
}

function deletePromotion(promotionId) {
    if (confirm('Are you sure you want to delete this promotion? This action cannot be undone.')) {
        fetch(`/superadmin/promotions/delete/${promotionId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting promotion: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting promotion');
        });
    }
}

// Update discount help text based on type
document.getElementById('promotionType').addEventListener('change', updateDiscountHelp);

function updateDiscountHelp() {
    const type = document.getElementById('promotionType').value;
    const helpText = document.getElementById('discountHelp');
    const discountInput = document.getElementById('discountValue');

    switch(type) {
        case 'percentage':
            helpText.textContent = 'Enter percentage (1-100)';
            discountInput.setAttribute('max', '100');
            discountInput.setAttribute('min', '1');
            break;
        case 'fixed':
            helpText.textContent = 'Enter fixed discount amount in dollars';
            discountInput.removeAttribute('max');
            discountInput.setAttribute('min', '0.01');
            break;
        case 'buy_one_get_one':
            helpText.textContent = 'Enter 1 for buy one get one free';
            discountInput.value = '1';
            discountInput.setAttribute('readonly', true);
            break;
        default:
            helpText.textContent = 'Select discount type first';
            discountInput.removeAttribute('readonly');
            break;
    }
}

// Auto-generate promotion code
document.getElementById('promotionName').addEventListener('input', function() {
    const name = this.value;
    const code = name.replace(/[^a-zA-Z0-9]/g, '').toUpperCase().substring(0, 10);
    if (code && !document.getElementById('promotionCode').value) {
        document.getElementById('promotionCode').value = code;
    }
});
</script>

<?php
function getPromotionCardClass($promotion) {
    $now = date('Y-m-d');
    if ($promotion['status'] === 'inactive') {
        return 'border-secondary';
    } elseif ($promotion['end_date'] < $now) {
        return 'border-warning';
    } else {
        return 'border-success';
    }
}

function getPromotionTypeBadgeColor($type) {
    switch($type) {
        case 'percentage':
            return 'success';
        case 'fixed':
            return 'info';
        case 'buy_one_get_one':
            return 'warning';
        default:
            return 'secondary';
    }
}
?>

</div>

<?php require_once 'views/layouts/superadmin_footer.php'; ?>
