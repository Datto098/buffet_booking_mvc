<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token ?? ''; ?>">
    <title>Edit Food Item - Admin</title>
    <?php require_once 'views/admin/layouts/header.php'; ?>
    <style>
        .image-placeholder {
            width: 100%;
            background: linear-gradient(135deg, #f8f9fc 0%, #e8ecf1 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 3px dashed #dee2e6;
            border-radius: 12px;
            margin-bottom: 15px;
            padding: 30px;
        }

        .image-container {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .food-image {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
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
                            <i class="fas fa-utensils"></i> Edit Food Item
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/foods">Foods</a></li>
                                <li class="breadcrumb-item active">Edit Food</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="<?= SITE_URL ?>/admin/foods" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Foods
                            </a>
                        </div>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Delete Food
                        </button>
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

                <!-- Food Edit Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-utensils"></i>
                                    Food Information
                                </h5>
                                <span class="badge bg-<?= ($food['is_available'] ?? 0) == 1 ? 'success' : 'danger' ?>">
                                    <?= ($food['is_available'] ?? 0) == 1 ? 'Available' : 'Unavailable' ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <form action="<?= SITE_URL ?>/admin/foods/edit/<?= $food['id'] ?>" method="POST" enctype="multipart/form-data" id="editFoodForm">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                                    <!-- Food Name and Category -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Food Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-utensils"></i></span>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    value="<?= htmlspecialchars($food['name'] ?? '') ?>" required
                                                    placeholder="Enter food name...">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                            <select class="form-select" id="category_id" name="category_id" required>
                                                <option value="">Choose category...</option>
                                                <?php if (isset($categories) && !empty($categories)): ?>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?= $category['id'] ?>" <?= ($food['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($category['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Price and Status -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                                <input type="number" class="form-control" id="price" name="price"
                                                    value="<?= htmlspecialchars($food['price'] ?? '') ?>" required min="0" step="1000"
                                                    placeholder="Enter price in VND...">
                                                <span class="input-group-text">VND</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="is_available" class="form-label">Availability</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1"
                                                    <?= ($food['is_available'] ?? 0) == 1 ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="is_available">
                                                    Available for Order
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4"
                                            placeholder="Describe the food item, ingredients, preparation method, etc..."><?= htmlspecialchars($food['description'] ?? '') ?></textarea>
                                    </div>

                                    <!-- Image Upload -->
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Food Image</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i> Choose a new image to replace the current one. Leave empty to keep current image.
                                            <br>Accepted formats: JPG, PNG, GIF. Maximum size: 5MB.
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Food Item
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Food Image Preview -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-image"></i> Food Image
                                </h6>
                            </div>
                            <div class="card-body text-center"
                                style="overflow: hidden;">
                                <div class="image-preview"
                                    style="max-width: 100%">
                                    <?php if (!empty($food['image'])): ?>
                                        <div class="image-container">
                                            <img src="<?= SITE_URL ?>/uploads/food_images/<?= htmlspecialchars($food['image']) ?>"
                                                class="food-image" alt="Food Image">
                                        </div>
                                    <?php else: ?>
                                        <div class="image-placeholder">
                                            <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No image uploaded</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <p class="text-muted small mt-3">Choose a file above to upload a new image</p>
                            </div>
                        </div>

                        <!-- Food Statistics -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-chart-bar"></i> Food Statistics
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h4 class="text-primary mb-1"><?= $food['total_orders'] ?? 0 ?></h4>
                                            <small class="text-muted">Times Ordered</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success mb-1"><?= $food['average_rating'] ?? '0.0' ?></h4>
                                        <small class="text-muted">Avg Rating</small>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Created:</small>
                                    <small><?= date('M d, Y', strtotime($food['created_at'] ?? 'now')) ?></small>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Last Updated:</small>
                                    <small><?= date('M d, Y', strtotime($food['updated_at'] ?? 'now')) ?></small>
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
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="viewFoodOrders(<?= $food['id'] ?>)">
                                        <i class="fas fa-shopping-cart"></i> View Orders
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="viewFoodReviews(<?= $food['id'] ?>)">
                                        <i class="fas fa-star"></i> View Reviews
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="duplicateFood(<?= $food['id'] ?>)">
                                        <i class="fas fa-copy"></i> Duplicate Food
                                    </button>
                                    <?php if (($food['status'] ?? '') == 'active'): ?>
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="toggleFoodStatus(<?= $food['id'] ?>, 'inactive')">
                                            <i class="fas fa-eye-slash"></i> Make Unavailable
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="toggleFoodStatus(<?= $food['id'] ?>, 'active')">
                                            <i class="fas fa-eye"></i> Make Available
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
    <div class="modal fade" id="deleteFoodModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this food item?</p>
                    <p class="text-danger"><strong>Warning:</strong> This action cannot be undone and will remove the item from all existing orders.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="<?= SITE_URL ?>/admin/foods/delete/<?= $food['id'] ?>" method="POST" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Food
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>    <?php require_once 'views/admin/layouts/footer.php'; ?>    <script>
        // Set up site URL for admin functions
        window.SITE_URL = '<?= SITE_URL ?>';

        // Page initialization
        document.addEventListener('DOMContentLoaded', function() {
            initializeFoodEditForm();
        });
    </script>
</body>

</html>
