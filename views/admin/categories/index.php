<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token ?? ''; ?>">
    <title>Categories Management - Admin</title>
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
                        <h1 class="h2">Categories Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Categories</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportCategories()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
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
                                            Total Categories
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $totalCategories ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tags fa-2x text-gray-300"></i>
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
                                            Active Categories
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $activeCategories ?? 0; ?>
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
                                            Food Items
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $totalFoodItems ?? 0; ?>
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
                            <input type="text" class="form-control" id="searchCategories" placeholder="Search by name, description...">
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleBulkActions()">
                                <i class="fas fa-tasks"></i> Bulk Actions
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="refreshCategories()">
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
                                    <strong><span id="selectedCount">0</span> category(s) selected</strong>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm" onclick="bulkUpdateStatus('active')">
                                        <i class="fas fa-check"></i> Activate Selected
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="bulkUpdateStatus('inactive')">
                                        <i class="fas fa-ban"></i> Deactivate Selected
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="clearSelection()">
                                        <i class="fas fa-times"></i> Clear Selection
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tags"></i> Categories List
                        </h5>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary">
                                <?php echo count($categories ?? []); ?> total
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($categories)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="categoriesTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" class="form-check-input" id="selectAll">
                                            </th>
                                            <th>Category ID</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Food Items</th>
                                            <th>Created</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categories as $category): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="form-check-input category-checkbox"
                                                           value="<?php echo $category['id']; ?>">
                                                </td>
                                                <td>
                                                    <strong class="text-primary">#<?php echo htmlspecialchars($category['id']); ?></strong>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <i class="fas fa-tag fa-2x text-muted"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium"><?php echo htmlspecialchars($category['name']); ?></div>
                                                            <small class="text-muted"><?php echo htmlspecialchars($category['slug'] ?? ''); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-medium"><?php echo htmlspecialchars(substr($category['description'] ?? '', 0, 50)); ?><?php echo strlen($category['description'] ?? '') > 50 ? '...' : ''; ?></div>
                                                </td>
                                                <td>
                                                    <?php if (isset($category['status']) && $category['status'] === 'active'): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle"></i> Active
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-times-circle"></i> Inactive
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        <i class="fas fa-utensils"></i> <?php echo htmlspecialchars($category['food_count'] ?? 0); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="fw-medium"><?php echo date('M j, Y', strtotime($category['created_at'])); ?></div>
                                                    <small class="text-muted"><?php echo date('g:i A', strtotime($category['created_at'])); ?></small>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-primary"
                                                                onclick="editCategory(<?php echo $category['id']; ?>)"
                                                                title="Edit Category">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-info"
                                                                onclick="viewCategory(<?php echo $category['id']; ?>)"
                                                                title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="deleteCategory(<?php echo $category['id']; ?>)"
                                                                title="Delete Category">
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
                            <?php if ($totalPages > 1): ?>
                                <nav aria-label="Categories pagination" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>">
                                                <i class="fas fa-chevron-left"></i> Previous
                                            </a>
                                        </li>

                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                            <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>">
                                                Next <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-tags fa-4x text-muted"></i>
                                </div>
                                <h5 class="text-muted">No Categories Found</h5>
                                <p class="text-muted">There are no categories to display.</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                                    <i class="fas fa-plus"></i> Add First Category
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Create Category Modal -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="categoryForm" method="POST" action="<?= SITE_URL ?>/admin/categories/create">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?? ''; ?>">

                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="categoryName" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="categoryDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="categoryDescription" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="categoryStatus" class="form-label">Status</label>
                            <select class="form-select" id="categoryStatus" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Categories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="mb-3">
                            <label for="filterStatus" class="form-label">Status</label>
                            <select class="form-select" id="filterStatus" name="status">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
                </div>
            </div>
        </div>
    </div>    <?php require_once 'views/admin/layouts/footer.php'; ?>    <script>
        // Set global SITE_URL for admin.js functions
        window.SITE_URL = '<?= SITE_URL ?>';
    </script>
</body>
</html>
