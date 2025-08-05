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
                    <i class="fas fa-filter"></i> Advanced Filter
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
            <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success'];
                                                unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error'];
                                                        unset($_SESSION['error']); ?>
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

    <!-- Filter Bar -->
    <div class="filter-bar mb-4">
        <form action="<?= SITE_URL ?>/admin/foods" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-4 col-md-6">
                <label class="form-label">Search Foods</label>
                <div class="search-box">
                    <input type="text" class="form-control" name="search" placeholder="Food name, description..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
            </div>
            <div class="col-lg-3 col-md-3">
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
            <div class="col-lg-2 col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="available" <?= ($_GET['status'] ?? '') == 'available' ? 'selected' : '' ?>>Available</option>
                    <option value="unavailable" <?= ($_GET['status'] ?? '') == 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-3">
                <label class="form-label">Type</label>
                <select class="form-select" name="type">
                    <option value="">All Types</option>
                    <option value="buffet" <?= ($_GET['type'] ?? '') == 'buffet' ? 'selected' : '' ?>>Buffet Items</option>
                    <option value="regular" <?= ($_GET['type'] ?? '') == 'regular' ? 'selected' : '' ?>>Regular Items</option>
                </select>
            </div>
            <div class="col-lg-1 col-md-12">
                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="<?= SITE_URL ?>/admin/foods" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Applied Filters Indicator -->
    <?php
    $activeFilters = [];
    if (!empty($_GET['search'])) $activeFilters[] = 'Search: "' . htmlspecialchars($_GET['search']) . '"';
    if (!empty($_GET['category'])) {
        $categoryName = '';
        foreach ($categories as $cat) {
            if ($cat['id'] == $_GET['category']) {
                $categoryName = $cat['name'];
                break;
            }
        }
        $activeFilters[] = 'Category: ' . htmlspecialchars($categoryName);
    }
    if (!empty($_GET['status'])) $activeFilters[] = 'Status: ' . ucfirst($_GET['status']);
    if (!empty($_GET['type'])) $activeFilters[] = 'Type: ' . ucfirst($_GET['type']);
    ?>

    <?php if (!empty($activeFilters)): ?>
        <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
            <div>
                <i class="fas fa-filter me-2"></i>
                <strong>Active Filters:</strong> <?= implode(' • ', $activeFilters) ?>
            </div>
            <a href="<?= SITE_URL ?>/admin/foods" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-times me-1"></i>Clear All
            </a>
        </div>
    <?php endif; ?>

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
                                <th>Type</th>
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
                                                    <img src="<?php echo SITE_URL . '/uploads/food_images/' . htmlspecialchars($food['image']); ?>"
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
                                        <?php if (($food['is_buffet_item'] ?? 0) == 1): ?>
                                            <div class="fw-medium text-success">FREE</div>
                                            <small class="text-muted">(Buffet)</small>
                                        <?php else: ?>
                                            <div class="fw-medium text-primary"><?php echo number_format($food['price'] ?? 0, 0, ',', '.'); ?>đ</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (($food['is_buffet_item'] ?? 0) == 1): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-utensils"></i> Buffet
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">
                                                <i class="fas fa-shopping-cart"></i> Regular
                                            </span>
                                        <?php endif; ?>
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
                                        <div class="btn-group" role="group"> <a href="<?= SITE_URL ?>/admin/foods/edit/<?php echo $food['id']; ?>"
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
                <?php if (($totalPages ?? 0) > 1): ?>
                    <?php
                    // Build query string for pagination to preserve filters
                    $queryParams = [];
                    if (!empty($_GET['search'])) $queryParams['search'] = $_GET['search'];
                    if (!empty($_GET['category'])) $queryParams['category'] = $_GET['category'];
                    if (!empty($_GET['status'])) $queryParams['status'] = $_GET['status'];
                    if (!empty($_GET['type'])) $queryParams['type'] = $_GET['type'];
                    ?>
                    <nav aria-label="Foods pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <?php
                                    $prevParams = $queryParams;
                                    $prevParams['page'] = $currentPage - 1;
                                    ?>
                                    <a class="page-link" href="?<?php echo http_build_query($prevParams); ?>">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                    <?php
                                    $pageParams = $queryParams;
                                    $pageParams['page'] = $i;
                                    ?>
                                    <a class="page-link" href="?<?php echo http_build_query($pageParams); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <?php
                                    $nextParams = $queryParams;
                                    $nextParams['page'] = $currentPage + 1;
                                    ?>
                                    <a class="page-link" href="?<?php echo http_build_query($nextParams); ?>">
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
                    <p class="text-muted">There are no food items to display.</p> <a href="<?= SITE_URL ?>/admin/foods/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add First Food
                    </a>
                </div>
            <?php endif; ?>
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
                    <form id="filterForm" action="<?= SITE_URL ?>/admin/foods" method="GET">
                        <div class="mb-3">
                            <label for="searchFilter" class="form-label">Search</label>
                            <input type="text" class="form-control" id="searchFilter" name="search"
                                   placeholder="Food name, description..."
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="categoryFilter" class="form-label">Category</label>
                            <select class="form-select" id="categoryFilter" name="category">
                                <option value="">All Categories</option>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"
                                                <?= ($_GET['category'] ?? '') == $category['id'] ? 'selected' : '' ?>>
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
                                <option value="available" <?= ($_GET['status'] ?? '') == 'available' ? 'selected' : '' ?>>Available</option>
                                <option value="unavailable" <?= ($_GET['status'] ?? '') == 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="typeFilter" class="form-label">Type</label>
                            <select class="form-select" id="typeFilter" name="type">
                                <option value="">All Types</option>
                                <option value="buffet" <?= ($_GET['type'] ?? '') == 'buffet' ? 'selected' : '' ?>>Buffet Items</option>
                                <option value="regular" <?= ($_GET['type'] ?? '') == 'regular' ? 'selected' : '' ?>>Regular Items</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="filterForm" class="btn btn-primary">Apply Filters</button>
                    <a href="<?= SITE_URL ?>/admin/foods" class="btn btn-outline-secondary">Clear All</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set global SITE_URL for admin.js functions
        window.SITE_URL = '<?= SITE_URL ?>';

        // Ensure form validation and prevent double submit
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.querySelector('form[action*="admin/foods"]');
            if (filterForm) {
                filterForm.addEventListener('submit', function(e) {
                    // Remove empty values to clean up URL
                    const inputs = this.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        if (!input.value || input.value.trim() === '') {
                            input.removeAttribute('name');
                        }
                    });
                });
            }
        });
    </script>
