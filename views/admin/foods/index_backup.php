<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token ?? ''; ?>">
    <title>Foods Management - Admin</title>
    <?php require_once 'views/admin/layouts/header.php'; ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php require_once 'views/admin/layouts/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Page Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2">Foods Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Foods</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportFoods()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                        <a href="<?= SITE_URL ?>/admin/foods/create" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add Food
                        </a>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-primary">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Foods
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $totalFoods ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-utensils fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-success">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Available
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $availableFoods ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-warning">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Categories
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $totalCategories ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-list fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-info">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Popular Today
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $popularToday ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-star fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Actions Bar -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchFoods" placeholder="Search by food name, category, or price...">
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleBulkActions()">
                                <i class="fas fa-tasks"></i> Bulk Actions
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="refreshFoods()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions Bar (Hidden by default) -->
                <div id="bulkActionsBar" class="row mb-3" style="display: none;">
                    <div class="col-12">
                        <div class="alert alert-light border">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <strong><span id="selectedCount">0</span> food(s) selected</strong>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm" onclick="bulkUpdateStatus('available')">
                                        <i class="fas fa-check"></i> Make Available
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="bulkUpdateStatus('unavailable')">
                                        <i class="fas fa-ban"></i> Make Unavailable
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="clearSelection()">
                                        <i class="fas fa-times"></i> Clear Selection
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Foods Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-utensils"></i> Foods List
                        </h5>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary">
                                <?php echo count($foods ?? []); ?> total
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($foods)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="foodsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" class="form-check-input" id="selectAll">
                                            </th>
                                            <th>Food</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($foods as $food): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="form-check-input food-checkbox"
                                                           value="<?php echo $food['id']; ?>">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <?php if (!empty($food['image'])): ?>
                                                                <img src="<?php echo htmlspecialchars($food['image']); ?>"
                                                                     alt="<?php echo htmlspecialchars($food['name']); ?>"
                                                                     class="rounded" width="40" height="40" style="object-fit: cover;">
                                                            <?php else: ?>
                                                                <i class="fas fa-utensils fa-2x text-muted"></i>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium"><?php echo htmlspecialchars($food['name'] ?? ''); ?></div>
                                                            <small class="text-muted"><?php echo htmlspecialchars(substr($food['description'] ?? '', 0, 50)); ?><?php echo strlen($food['description'] ?? '') > 50 ? '...' : ''; ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        <?php echo htmlspecialchars($food['category_name'] ?? 'Uncategorized'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="fw-medium text-success">$<?php echo number_format($food['price'] ?? 0, 2); ?></div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClass = ($food['is_available'] ?? 0) ? 'success' : 'danger';
                                                    $statusText = ($food['is_available'] ?? 0) ? 'Available' : 'Unavailable';
                                                    $statusIcon = ($food['is_available'] ?? 0) ? 'check-circle' : 'times-circle';
                                                    ?>
                                                    <span class="badge bg-<?php echo $statusClass; ?>">
                                                        <i class="fas fa-<?php echo $statusIcon; ?>"></i> <?php echo $statusText; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <div class="fw-medium"><?php echo date('M j, Y', strtotime($food['created_at'])); ?></div>
                                                        <small class="text-muted"><?php echo date('g:i A', strtotime($food['created_at'])); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="<?= SITE_URL ?>/admin/foods/edit/<?php echo $food['id']; ?>"
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="Edit Food">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-info"
                                                                onclick="viewFood(<?php echo $food['id']; ?>)"
                                                                title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="deleteFood(<?php echo $food['id']; ?>)"
                                                                title="Delete Food">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($totalPages ?? 0 > 1): ?>
                                <nav aria-label="Foods pagination" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($currentPage > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>">
                                                    <i class="fas fa-chevron-left"></i> Previous
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                            <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($currentPage < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>">
                                                    Next <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-utensils fa-4x text-muted"></i>
                                </div>
                                <h5 class="text-muted">No Foods Found</h5>
                                <p class="text-muted">There are no food items to display.</p>
                                <a href="<?= SITE_URL ?>/admin/foods/create" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add First Food
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">
                        <i class="fas fa-filter"></i> Filter Foods
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="mb-3">
                            <label for="categoryFilter" class="form-label">Category</label>
                            <select class="form-select" id="categoryFilter" name="category">
                                <option value="">All Categories</option>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="statusFilter" class="form-label">Status</label>
                            <select class="form-select" id="statusFilter" name="status">
                                <option value="">All Status</option>
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="minPrice" class="form-label">Min Price</label>
                                <input type="number" class="form-control" id="minPrice" name="min_price" step="0.01" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label for="maxPrice" class="form-label">Max Price</label>
                                <input type="number" class="form-control" id="maxPrice" name="max_price" step="0.01" placeholder="999.99">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">Clear</button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'views/admin/layouts/footer.php'; ?>

    <script>
        // Foods Management JavaScript
        function toggleBulkActions() {
            const bulkBar = document.getElementById('bulkActionsBar');
            if (bulkBar) {
                bulkBar.style.display = bulkBar.style.display === 'none' ? 'block' : 'none';
            }
        }

        function refreshFoods() {
            location.reload();
        }

        function exportFoods() {
            window.location.href = '<?= SITE_URL ?>/admin/foods?export=csv';
        }

        function viewFood(foodId) {
            window.location.href = '<?= SITE_URL ?>/admin/foods/view/' + foodId;
        }

        function deleteFood(foodId) {
            if (confirm('Are you sure you want to delete this food item?')) {
                fetch('<?= SITE_URL ?>/admin/foods/delete/' + foodId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Failed to delete food item');
                    }
                });
            }
        }

        function bulkUpdateStatus(status) {
            const selected = document.querySelectorAll('.food-checkbox:checked');
            if (selected.length === 0) {
                alert('Please select at least one food item');
                return;
            }

            const foodIds = Array.from(selected).map(cb => cb.value);

            fetch('<?= SITE_URL ?>/admin/foods/bulk-update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    food_ids: foodIds,
                    status: status
                })
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Failed to update food status');
                }
            });
        }

        function clearSelection() {
            document.querySelectorAll('.food-checkbox').forEach(cb => cb.checked = false);
            updateSelectedCount();
            document.getElementById('bulkActionsBar').style.display = 'none';
        }

        function updateSelectedCount() {
            const count = document.querySelectorAll('.food-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = count;
        }

        function applyFilters() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            window.location.href = '<?= SITE_URL ?>/admin/foods?' + params.toString();
        }

        function clearFilters() {
            window.location.href = '<?= SITE_URL ?>/admin/foods';
        }

        // Search functionality
        const searchInput = document.getElementById('searchFoods');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#foodsTable tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Select all functionality
        document.getElementById('selectAll')?.addEventListener('change', function() {
            document.querySelectorAll('.food-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Individual checkbox functionality
        document.querySelectorAll('.food-checkbox').forEach(cb => {
            cb.addEventListener('change', updateSelectedCount);
        });
    </script>
</body>
</html>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Food Statistics -->
                <div class="stats-cards">
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-utensils"></i>
                                </div>
                                <div class="stat-content">
                                    <h3><?= $totalFoods ?></h3>
                                    <p>Total Foods</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div class="stat-content">
                                    <h3><?= count(array_filter($foods, function ($food) {
                                            return $food['status'] == 'available';
                                        })) ?></h3>
                                    <p>Available Foods</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-eye-slash"></i>
                                </div>
                                <div class="stat-content">
                                    <h3><?= count(array_filter($foods, function ($food) {
                                            return $food['status'] == 'unavailable';
                                        })) ?></h3>
                                    <p>Unavailable Foods</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>$<?= number_format(array_sum(array_column($foods, 'price')), 0) ?></h3>
                                    <p>Total Value</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter Bar -->
                <div class="filter-bar">
                    <form action="<?= SITE_URL ?>/admin/foods" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Search Foods</label>
                            <div class="search-box">
                                <input type="text" class="form-control" name="search" placeholder="Search by name..."
                                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= ($_GET['category'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Status</option>
                                <option value="available" <?= ($_GET['status'] ?? '') == 'available' ? 'selected' : '' ?>>Available</option>
                                <option value="unavailable" <?= ($_GET['status'] ?? '') == 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <a href="<?= SITE_URL ?>/admin/foods" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Foods Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h3><i class="fas fa-list me-2"></i>Foods List</h3>
                        <div class="table-actions">
                            <button class="btn btn-outline-success btn-sm me-2" onclick="exportTable()">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="toggleView()">
                                <i class="fas fa-th me-1"></i>Grid View
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover dataTable" id="foodsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($foods)): ?>
                                    <?php foreach ($foods as $food): ?>
                                        <tr>
                                            <td><strong>#<?= htmlspecialchars($food['id']) ?></strong></td>
                                            <td>
                                                <div class="food-image">
                                                    <?php if (!empty($food['image'])): ?>
                                                        <img src="<?= SITE_URL ?>/uploads/food_images/<?= htmlspecialchars($food['image']) ?>"
                                                            alt="<?= htmlspecialchars($food['name']) ?>" class="img-thumbnail">
                                                    <?php else: ?>
                                                        <div class="no-image">
                                                            <i class="fas fa-image"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="food-info">
                                                    <strong><?= htmlspecialchars($food['name']) ?></strong>
                                                    <small class="text-muted d-block">
                                                        <?= htmlspecialchars(substr($food['description'] ?? 'No description', 0, 50)) ?>
                                                        <?= strlen($food['description'] ?? '') > 50 ? '...' : '' ?>
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= htmlspecialchars($food['category_name'] ?? 'No Category') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="price-tag">
                                                    $<?= number_format($food['price'], 2) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?= $food['status'] ?>">
                                                    <?= ucfirst($food['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($food['created_at'])) ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="<?= SITE_URL ?>/admin/foods/edit/<?= $food['id'] ?>"
                                                        class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="tooltip" title="Edit Food">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if ($food['status'] == 'available'): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                            onclick="toggleFoodStatus(<?= $food['id'] ?>, 'unavailable')"
                                                            data-bs-toggle="tooltip" title="Mark Unavailable">
                                                            <i class="fas fa-eye-slash"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-outline-success"
                                                            onclick="toggleFoodStatus(<?= $food['id'] ?>, 'available')"
                                                            data-bs-toggle="tooltip" title="Mark Available">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                                        onclick="deleteFood(<?= $food['id'] ?>)"
                                                        data-bs-toggle="tooltip" title="Delete Food">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No Foods Found</h5>
                                                <p class="text-muted">Start by adding your first food item.</p>
                                                <a href="<?= SITE_URL ?>/admin/foods/create" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>Add Food
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination-container">
                            <nav aria-label="Foods pagination">
                                <ul class="pagination justify-content-center">
                                    <?php if ($currentPage > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $currentPage - 1 ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($currentPage < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $currentPage + 1 ?>">Next</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteFoodModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this food item? This action cannot be undone.</p>
                    <p class="text-muted"><small>Note: This will also remove the food from all orders and menus.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="" method="POST" id="deleteFoodForm" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete Food
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Delete food function
        function deleteFood(id) {
            document.getElementById('deleteFoodForm').action = `<?= SITE_URL ?>/admin/foods/delete/${id}`;
            new bootstrap.Modal(document.getElementById('deleteFoodModal')).show();
        }

        // Toggle food status
        function toggleFoodStatus(id, status) {
            if (confirm(`Are you sure you want to mark this food as ${status}?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `<?= SITE_URL ?>/admin/foods/${id}/toggle-status`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = 'csrf_token';
                csrfToken.value = '<?= $_SESSION['csrf_token'] ?? '' ?>';

                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = status;

                form.appendChild(csrfToken);
                form.appendChild(statusInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Export table function
        function exportTable() {
            window.location.href = '<?= SITE_URL ?>/admin/foods/export';
        }

        // Toggle view function (placeholder)
        function toggleView() {
            alert('Grid view coming soon!');
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <?php require_once 'views/admin/layouts/footer.php'; ?>
</body>

</html>
