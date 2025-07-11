<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'views/layouts/superadmin_header.php'; ?>
    <title>Promotion Management - Super Admin</title>
    <style>
        .promotion-application-type {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .food-item-checkbox {
            transition: all 0.2s ease;
        }

        .food-item-checkbox:hover {
            background-color: #e3f2fd;
            border-radius: 4px;
            transform: translateX(2px);
        }

        .category-checkbox {
            transition: all 0.2s ease;
        }

        .category-checkbox:hover {
            background-color: #f3e5f5;
            border-radius: 4px;
            transform: translateX(2px);
        }

        .selection-counter {
            background: linear-gradient(45deg, #42a5f5, #478ed1);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .promotion-form-section {
            border-left: 4px solid #2196f3;
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 0 8px 8px 0;
        }
    </style>
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
                            <i class="fas fa-tags me-2 text-success"></i>Promotion Management
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/superadmin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Promotions</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-primary" id="createPromotionBtn" data-bs-toggle="modal" data-bs-target="#promotionModal">
                            <i class="fas fa-plus me-1"></i>Create Promotion
                        </button>
                    </div>
                </div>                <!-- Flash Messages -->
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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Promotions</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['total_promotions'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-tags fa-2x opacity-75"></i>
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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Active Promotions</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['active_promotions'] ?? 0) ?></div>
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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Expiring Soon</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['expiring_promotions'] ?? 0) ?></div>
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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Savings</div>
                                        <div class="h4 mb-0 font-weight-bold">$<?= number_format($stats['total_savings'] ?? 0, 2) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                </div>

                <!-- Filters -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-filter me-2 text-success"></i>Filters
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label fw-bold">Status</label>
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
                        <div> <button type="submit" class="btn btn-danger">
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
                                <h6 class="card-title mb-0"><?php echo htmlspecialchars($promotion['name']); ?></h6>                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                        id="promotion-status-<?php echo $promotion['id']; ?>"
                                        <?php echo $promotion['is_active'] ? 'checked' : ''; ?>
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
                                    <?php endif; ?>                                </div>

                                <!-- Application Type Info -->
                                <div class="mb-3">
                                    <strong>Applies to:</strong>
                                    <?php
                                    $applicationType = $promotion['application_type'] ?? 'all';
                                    switch($applicationType) {
                                        case 'specific_items':
                                            echo '<span class="badge bg-info">Specific Items</span>';
                                            break;
                                        case 'categories':
                                            echo '<span class="badge bg-warning">Categories</span>';
                                            break;
                                        default:
                                            echo '<span class="badge bg-success">All Items</span>';
                                    }
                                    ?>
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
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#promotionModal" onclick="setupNewPromotionForm()">
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
            </div>            <div class="modal-body">
                <form id="promotionForm">
                    <input type="hidden" id="promotionId" name="promotion_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?? ''; ?>">

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
                            <input type="number" class="form-control" id="discountValue" name="discount_value" min="0" step="0.01" required>                            <div class="form-text" id="discountHelp">Enter percentage (1-100) or fixed amount</div>
                        </div>
                    </div>                    <!-- Application Type Selection -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Apply Promotion To <span class="text-danger">*</span></label>
                            <div class="promotion-form-section">
                                <div class="form-check application-type-radio">
                                    <input class="form-check-input" type="radio" name="application_type" id="applyAll" value="all" checked onchange="updateApplicationTypeFields()">
                                    <label class="form-check-label" for="applyAll">
                                        <strong>🌐 All Items</strong> <small class="text-muted">(General promotion for entire menu)</small>
                                    </label>
                                </div>
                                <div class="form-check application-type-radio">
                                    <input class="form-check-input" type="radio" name="application_type" id="applySpecific" value="specific_items" onchange="updateApplicationTypeFields()">
                                    <label class="form-check-label" for="applySpecific">
                                        <strong>🎯 Specific Food Items</strong> <small class="text-muted">(Choose individual dishes)</small>
                                    </label>
                                </div>
                                <div class="form-check application-type-radio">
                                    <input class="form-check-input" type="radio" name="application_type" id="applyCategories" value="categories" onchange="updateApplicationTypeFields()">
                                    <label class="form-check-label" for="applyCategories">
                                        <strong>📂 Entire Categories</strong> <small class="text-muted">(Apply to all items in selected categories)</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div><!-- Food Items Selection -->
                    <div id="foodItemsSection" class="mb-3" style="display: none;">
                        <label class="form-label">Select Food Items <small class="text-muted">(Choose which items get the discount)</small></label>                        <div class="mb-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAllFoodItems()">
                                <i class="fas fa-check-double"></i> Select All
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAllFoodItems()">
                                <i class="fas fa-times"></i> Clear All
                            </button>
                            <span id="selectedFoodCount" class="ms-2 selection-counter">0 items selected</span>
                        </div>
                        <div class="border rounded p-3 scrollable-selection" style="max-height: 300px; overflow-y: auto; background-color: #f8f9fa;">
                            <?php if (!empty($food_items)): ?>
                                <?php foreach ($food_items as $item): ?>
                                    <div class="form-check mb-2 food-item-checkbox p-2">
                                        <input class="form-check-input" type="checkbox" name="food_items[]" value="<?= $item['id'] ?>" id="food_<?= $item['id'] ?>" onchange="updateFoodItemCount()">
                                        <label class="form-check-label d-flex justify-content-between align-items-center" for="food_<?= $item['id'] ?>" style="width: 100%;">
                                            <div>
                                                <strong><?= htmlspecialchars($item['name']) ?></strong>
                                                <?php if (!empty($item['description'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($item['description']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <span class="badge bg-primary">$<?= number_format($item['price'], 2) ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No food items available</p>
                            <?php endif; ?>
                        </div>
                    </div>                    <!-- Categories Selection -->
                    <div id="categoriesSection" class="mb-3" style="display: none;">
                        <label class="form-label">Select Categories <small class="text-muted">(All items in selected categories will get the discount)</small></label>                        <div class="mb-2">
                            <span id="selectedCategoryCount" class="selection-counter">0 categories selected</span>
                        </div>
                        <div class="border rounded p-3 scrollable-selection" style="background-color: #f8f9fa;">
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <div class="form-check mb-2 category-checkbox p-2">
                                        <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $category['id'] ?>" id="category_<?= $category['id'] ?>" onchange="updateCategoryCount()">
                                        <label class="form-check-label" for="category_<?= $category['id'] ?>">
                                            <strong><?= htmlspecialchars($category['name']) ?></strong>
                                            <?php if (!empty($category['description'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($category['description']) ?></small>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No categories available</p>
                            <?php endif; ?>
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
    function getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content') ||
               document.querySelector('input[name="csrf_token"]').value;
    }    function showAddPromotionModal() {
        console.log('showAddPromotionModal called');

        // Check if modal element exists
        const modalElement = document.getElementById('promotionModal');
        if (!modalElement) {
            console.error('Modal element promotionModal not found');
            alert('Modal element not found');
            return;
        }

        // Check if form element exists
        const formElement = document.getElementById('promotionForm');
        if (!formElement) {
            console.error('Form element promotionForm not found');
            alert('Form element not found');
            return;
        }

        document.getElementById('promotionModalLabel').textContent = 'Add New Promotion';
        document.getElementById('promotionForm').reset();
        document.getElementById('promotionId').value = '';

        // Reset application type to 'all'
        const applyAllElement = document.getElementById('applyAll');
        if (applyAllElement) {
            applyAllElement.checked = true;
        }

        // Check if these functions exist
        if (typeof updateApplicationTypeFields === 'function') {
            updateApplicationTypeFields();
        }
        if (typeof clearSelections === 'function') {
            clearSelections();
        }

        // Set default dates
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const nextMonth = new Date(today);
        nextMonth.setMonth(nextMonth.getMonth() + 1);

        const startDateElement = document.getElementById('startDate');
        const endDateElement = document.getElementById('endDate');

        if (startDateElement) {
            startDateElement.value = tomorrow.toISOString().split('T')[0];
        }
        if (endDateElement) {
            endDateElement.value = nextMonth.toISOString().split('T')[0];
        }

        try {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('Modal should be shown now');
        } catch (error) {
            console.error('Error showing modal:', error);
            alert('Error showing modal: ' + error.message);
        }
    }function editPromotion(promotionId) {
        console.log('Edit promotion called with ID:', promotionId);

        fetch(`<?= SITE_URL ?>/superadmin/promotions/get/${promotionId}`)
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                return response.text();
            })
            .then(text => {
                console.log('Raw response:', text);

                try {
                    const data = JSON.parse(text);
                    console.log('Parsed data:', data);

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

                        // Set application type
                        const applicationType = data.promotion.application_type || 'all';
                        document.querySelector(`input[name="application_type"][value="${applicationType}"]`).checked = true;
                        updateApplicationTypeFields();

                        // Clear existing selections
                        clearSelections();

                        // Set food items if applicable
                        if (data.promotion.food_items && data.promotion.food_items.length > 0) {
                            console.log('Setting food items:', data.promotion.food_items);
                            data.promotion.food_items.forEach(itemId => {
                                const checkbox = document.getElementById(`food_${itemId}`);
                                if (checkbox) {
                                    checkbox.checked = true;
                                } else {
                                    console.warn('Food item checkbox not found:', itemId);
                                }
                            });
                            updateFoodItemCount();
                        }

                        // Set categories if applicable
                        if (data.promotion.categories && data.promotion.categories.length > 0) {
                            console.log('Setting categories:', data.promotion.categories);
                            data.promotion.categories.forEach(categoryId => {
                                const checkbox = document.getElementById(`category_${categoryId}`);
                                if (checkbox) {
                                    checkbox.checked = true;
                                } else {
                                    console.warn('Category checkbox not found:', categoryId);
                                }
                            });
                            updateCategoryCount();
                        }

                        updateDiscountHelp();

                        const modal = new bootstrap.Modal(document.getElementById('promotionModal'));
                        modal.show();
                    } else {
                        alert('Error loading promotion data: ' + (data.message || 'Unknown error'));
                    }
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Raw response that failed to parse:', text);
                    alert('Error: Invalid response format. Check console for details.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Error loading promotion data: ' + error.message + '. Check console for details.');
            });
    }

    function savePromotion() {        const form = document.getElementById('promotionForm');
        const formData = new FormData(form);
        const promotionId = document.getElementById('promotionId').value;

        const url = promotionId ? `<?= SITE_URL ?>/superadmin/promotions/edit/${promotionId}` : '<?= SITE_URL ?>/superadmin/promotions/create';

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

    function togglePromotionStatus(promotionId) {        const checkbox = document.getElementById(`promotion-status-${promotionId}`);
        const status = checkbox.checked ? 'active' : 'inactive';

        fetch(`<?= SITE_URL ?>/superadmin/promotions/toggle/${promotionId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    status: status
                })
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
    }    function deletePromotion(promotionId) {
        if (confirm('Are you sure you want to delete this promotion? This action cannot be undone.')) {
            fetch(`<?= SITE_URL ?>/superadmin/promotions/delete/${promotionId}`, {
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
                });        }
    }

    function viewPromotionStats(promotionId) {
        // TODO: Implement promotion statistics view
        // This could show usage statistics, revenue generated, etc.
        alert('Promotion statistics feature coming soon!');
    }

    // Update discount help text based on type
    document.getElementById('promotionType').addEventListener('change', updateDiscountHelp);

    function updateDiscountHelp() {
        const type = document.getElementById('promotionType').value;
        const helpText = document.getElementById('discountHelp');
        const discountInput = document.getElementById('discountValue');

        switch (type) {
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
    }    // Auto-generate promotion code
    document.getElementById('promotionName').addEventListener('input', function() {
        const name = this.value;
        const code = name.replace(/[^a-zA-Z0-9]/g, '').toUpperCase().substring(0, 10);
        if (code && !document.getElementById('promotionCode').value) {
            document.getElementById('promotionCode').value = code;
        }
    });

    // Handle application type changes
    function updateApplicationTypeFields() {
        const applicationType = document.querySelector('input[name="application_type"]:checked').value;
        const foodItemsSection = document.getElementById('foodItemsSection');
        const categoriesSection = document.getElementById('categoriesSection');

        // Hide all sections first
        foodItemsSection.style.display = 'none';
        categoriesSection.style.display = 'none';

        // Show relevant section
        if (applicationType === 'specific_items') {
            foodItemsSection.style.display = 'block';
        } else if (applicationType === 'categories') {
            categoriesSection.style.display = 'block';
        }
    }

    // Clear selections when switching application type
    function clearSelections() {
        // Clear food item checkboxes
        document.querySelectorAll('input[name="food_items[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });        // Clear category checkboxes
        document.querySelectorAll('input[name="categories[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Update counters
        updateFoodItemCount();
        updateCategoryCount();
    }

    // Select all food items
    function selectAllFoodItems() {
        document.querySelectorAll('input[name="food_items[]"]').forEach(checkbox => {
            checkbox.checked = true;
        });
        updateFoodItemCount();
    }

    // Clear all food items
    function clearAllFoodItems() {
        document.querySelectorAll('input[name="food_items[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        updateFoodItemCount();
    }

    // Update food item count
    function updateFoodItemCount() {
        const checkedBoxes = document.querySelectorAll('input[name="food_items[]"]:checked');
        const counter = document.getElementById('selectedFoodCount');
        if (counter) {
            counter.textContent = `${checkedBoxes.length} items selected`;
        }
    }

    // Update category count
    function updateCategoryCount() {
        const checkedBoxes = document.querySelectorAll('input[name="categories[]"]:checked');
        const counter = document.getElementById('selectedCategoryCount');
        if (counter) {
            counter.textContent = `${checkedBoxes.length} categories selected`;
        }
    }

    // Helper function for setting up new promotion form
    function setupNewPromotionForm() {
        // Reset form for new promotion
        document.getElementById('promotionModalLabel').textContent = 'Add New Promotion';
        document.getElementById('promotionForm').reset();
        document.getElementById('promotionId').value = '';

        // Reset application type to 'all'
        const applyAllElement = document.getElementById('applyAll');
        if (applyAllElement) {
            applyAllElement.checked = true;
        }

        // Check if these functions exist
        if (typeof updateApplicationTypeFields === 'function') {
            updateApplicationTypeFields();
        }
        if (typeof clearSelections === 'function') {
            clearSelections();
        }

        // Set default dates
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const nextMonth = new Date(today);
        nextMonth.setMonth(nextMonth.getMonth() + 1);

        const startDateElement = document.getElementById('startDate');
        const endDateElement = document.getElementById('endDate');

        if (startDateElement) {
            startDateElement.value = tomorrow.toISOString().split('T')[0];
        }
        if (endDateElement) {
            endDateElement.value = nextMonth.toISOString().split('T')[0];
        }
    }

    // Enhanced form validation
    function validatePromotionForm(form) {
        const applicationType = form.querySelector('input[name="application_type"]:checked')?.value;

        if (applicationType === 'specific_items') {
            const selectedItems = form.querySelectorAll('input[name="food_items[]"]:checked');
            if (selectedItems.length === 0) {
                alert('Please select at least one food item for this promotion.');
                return false;
            }
        } else if (applicationType === 'categories') {
            const selectedCategories = form.querySelectorAll('input[name="categories[]"]:checked');
            if (selectedCategories.length === 0) {
                alert('Please select at least one category for this promotion.');
                return false;
            }
        }

        return true;
    }

    // Add form validation on submit
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('promotionForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validatePromotionForm(this)) {
                    e.preventDefault();
                }
            });
        }

        // Add event listener for create promotion button
        const createBtn = document.getElementById('createPromotionBtn');
        if (createBtn) {
            createBtn.addEventListener('click', function() {
                // Reset form for new promotion
                document.getElementById('promotionModalLabel').textContent = 'Add New Promotion';
                document.getElementById('promotionForm').reset();
                document.getElementById('promotionId').value = '';

                // Reset application type to 'all'
                const applyAllElement = document.getElementById('applyAll');
                if (applyAllElement) {
                    applyAllElement.checked = true;
                }

                // Check if these functions exist
                if (typeof updateApplicationTypeFields === 'function') {
                    updateApplicationTypeFields();
                }
                if (typeof clearSelections === 'function') {
                    clearSelections();
                }

                // Set default dates
                const today = new Date();
                const tomorrow = new Date(today);
                tomorrow.setDate(tomorrow.getDate() + 1);
                const nextMonth = new Date(today);
                nextMonth.setMonth(nextMonth.getMonth() + 1);

                const startDateElement = document.getElementById('startDate');
                const endDateElement = document.getElementById('endDate');

                if (startDateElement) {
                    startDateElement.value = tomorrow.toISOString().split('T')[0];
                }
                if (endDateElement) {
                    endDateElement.value = nextMonth.toISOString().split('T')[0];
                }
            });
        }

        // Initialize counters
        updateFoodItemCount();
        updateCategoryCount();
    });
</script>

<?php
function getPromotionCardClass($promotion)
{
    $now = date('Y-m-d');
    if (!$promotion['is_active']) {
        return 'border-secondary';
    } elseif ($promotion['end_date'] < $now) {
        return 'border-warning';
    } else {
        return 'border-success';
    }
}

function getPromotionTypeBadgeColor($type)
{
    switch ($type) {
        case 'percentage':
            return 'success';
        case 'fixed':
            return 'info';
        case 'buy_one_get_one':
            return 'warning';        default:
            return 'secondary';
    }
}
?>

            </main>
        </div>
    </div>
</body>
</html>
