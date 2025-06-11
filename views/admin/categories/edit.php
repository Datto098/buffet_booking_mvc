<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token ?? ''; ?>">
    <title>Edit Category - Admin</title>
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
                        <h1 class="h2">
                            <i class="fas fa-edit"></i> Edit Category
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/categories">Categories</a></li>
                                <li class="breadcrumb-item active">Edit Category</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="<?= SITE_URL ?>/admin/categories" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Categories
                            </a>
                        </div>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Delete Category
                        </button>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?= $_SESSION['flash_type'] == 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                        <?= htmlspecialchars($_SESSION['flash_message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php
                    unset($_SESSION['flash_message']);
                    unset($_SESSION['flash_type']);
                    ?>
                <?php endif; ?>

                <!-- Category Edit Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-tags"></i> Category Information
                                </h5>
                                <span class="badge bg-<?= ($category['is_active'] ?? 0) == 1 ? 'success' : 'danger' ?>">
                                    <?= ($category['is_active'] ?? 0) == 1 ? 'Active' : 'Inactive' ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <form action="<?= SITE_URL ?>/admin/categories/update/<?= $category['id'] ?>" method="POST" enctype="multipart/form-data" id="editCategoryForm">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <input type="hidden" name="_method" value="PUT">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-tags"></i></span>
                                                <input type="text" class="form-control" id="name" name="name"
                                                       value="<?= htmlspecialchars($category['name'] ?? '') ?>"
                                                       maxlength="100" required>
                                            </div>
                                        </div>                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="active" <?= ($category['is_active'] ?? 0) == 1 ? 'selected' : '' ?>>Active</option>
                                                <option value="inactive" <?= ($category['is_active'] ?? 0) == 0 ? 'selected' : '' ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="sort_order" class="form-label">Sort Order</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                                                <input type="number" class="form-control" id="sort_order" name="sort_order"
                                                       value="<?= htmlspecialchars($category['sort_order'] ?? '0') ?>" min="0">
                                            </div>
                                            <div class="form-text">Lower numbers appear first</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="color" class="form-label">Category Color</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-palette"></i></span>
                                                <input type="color" class="form-control form-control-color" id="color" name="color"
                                                       value="<?= htmlspecialchars($category['color'] ?? '#007bff') ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4"
                                                  maxlength="500" placeholder="Describe this category..."><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                                        <div class="form-text">Maximum 500 characters</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="icon" class="form-label">Category Icon</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-icons"></i></span>
                                            <input type="text" class="form-control" id="icon" name="icon"
                                                   value="<?= htmlspecialchars($category['icon'] ?? '') ?>"
                                                   placeholder="e.g., fas fa-utensils">
                                        </div>
                                        <div class="form-text">FontAwesome icon class (e.g., fas fa-utensils)</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="image" class="form-label">Category Image</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        <div class="form-text">Upload an image for this category (optional)</div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Category
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Category Image Preview -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-image"></i> Category Image
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="image-preview mb-3">
                                    <?php if (!empty($category['image'])): ?>
                                        <img src="<?= SITE_URL ?>/uploads/categories/<?= htmlspecialchars($category['image']) ?>"
                                             class="img-fluid rounded" style="max-height: 150px; object-fit: cover;" alt="Category Image">
                                    <?php else: ?>
                                        <div class="image-placeholder">
                                            <i class="fas fa-tags fa-3x text-muted"></i>
                                            <p class="text-muted mt-2">No image uploaded</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <p class="text-muted small">Choose a file above to upload a new image</p>
                            </div>
                        </div>

                        <!-- Icon Preview -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-icons"></i> Icon Preview
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="icon-preview p-3">
                                    <?php if (!empty($category['icon'])): ?>
                                        <i class="<?= htmlspecialchars($category['icon']) ?> fa-3x"
                                           style="color: <?= htmlspecialchars($category['color'] ?? '#007bff') ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-question-circle fa-3x text-muted"></i>
                                    <?php endif; ?>
                                </div>
                                <p class="text-muted small">Icon will be displayed with the selected color</p>
                            </div>
                        </div>

                        <!-- Category Statistics -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-chart-bar"></i> Category Statistics
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h4 class="text-primary mb-1"><?= $category['total_foods'] ?? 0 ?></h4>
                                            <small class="text-muted">Food Items</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success mb-1"><?= $category['total_orders'] ?? 0 ?></h4>
                                        <small class="text-muted">Total Orders</small>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Created:</small>
                                    <small><?= date('M d, Y', strtotime($category['created_at'] ?? 'now')) ?></small>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Last Updated:</small>
                                    <small><?= date('M d, Y', strtotime($category['updated_at'] ?? 'now')) ?></small>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-bolt"></i> Quick Actions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="viewCategoryFoods(<?= $category['id'] ?>)">
                                        <i class="fas fa-utensils"></i> View Foods
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="addFoodToCategory(<?= $category['id'] ?>)">
                                        <i class="fas fa-plus"></i> Add New Food
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="duplicateCategory(<?= $category['id'] ?>)">
                                        <i class="fas fa-copy"></i> Duplicate Category
                                    </button>
                                    <?php if (($category['is_active'] ?? 0) == 1): ?>
                                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="toggleCategoryStatus(<?= $category['id'] ?>, 0)">
                                            <i class="fas fa-eye-slash"></i> Deactivate
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="toggleCategoryStatus(<?= $category['id'] ?>, 1)">
                                            <i class="fas fa-eye"></i> Activate
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this category?</p>
                    <p class="text-danger"><strong>Warning:</strong> This will also remove all food items in this category.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="<?= SITE_URL ?>/admin/categories/delete/<?= $category['id'] ?>" method="POST" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Category
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>    <?php require_once 'views/admin/layouts/footer.php'; ?>    <script>
        window.SITE_URL = '<?= SITE_URL ?>';
        document.addEventListener('DOMContentLoaded', function() {
            initializeCategoryEditForm();
        });
    </script>

    <style>
        .image-placeholder {
            height: 120px;
            background: linear-gradient(135deg, #f8f9fc 0%, #e8ecf1 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 3px dashed #dee2e6;
            border-radius: 8px;
        }

        .icon-preview {
            background: linear-gradient(135deg, #f8f9fc 0%, #e8ecf1 100%);
            border-radius: 8px;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-control:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .input-group-text {
            background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
            border-color: #e3e6f0;
        }
    </style>
</body>
</html>
