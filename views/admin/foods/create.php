<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <div>
                    <h1 class="h2">Add New Food Item</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/buffet_booking_mvc/admin/foods">Food Management</a></li>
                            <li class="breadcrumb-item active">Add New</li>
                        </ol>
                    </nav>
                </div>
                <a href="/buffet_booking_mvc/admin/foods" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Back to Foods
                </a>
            </div>

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash'])): ?>
                <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Food Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="/buffet_booking_mvc/admin/foods/create" method="POST" enctype="multipart/form-data" id="createFoodForm">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Food Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price (₹) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                            <select class="form-select" id="category_id" name="category_id" required>
                                                <option value="">Select Category</option>
                                                <?php if (!empty($categories)): ?>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                                            <?php echo htmlspecialchars($category['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="subcategory_id" class="form-label">Subcategory</label>
                                            <select class="form-select" id="subcategory_id" name="subcategory_id">
                                                <option value="">Select Subcategory</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter food description..."></textarea>
                                </div>                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1" checked>
                                        <label class="form-check-label" for="is_available">
                                            Available for Order
                                        </label>
                                    </div>
                                </div>

                                <!-- Buffet Settings -->
                                <div class="card mb-3 border-success">
                                    <div class="card-header bg-light-success">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-utensils text-success"></i> Buffet Settings
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_buffet_item" name="is_buffet_item" value="1">
                                            <label class="form-check-label" for="is_buffet_item">
                                                <i class="fas fa-gift text-success"></i>
                                                <strong>Buffet Item (Free with Entry)</strong>
                                            </label>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Check this to mark as buffet item - customers can order for free with entry ticket.
                                            Price will be automatically set to 0.
                                        </small>
                                    </div>
                                </div>

                                <!-- Featured Food Settings -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Featured Food Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_popular" name="is_popular" value="1">
                                                    <label class="form-check-label" for="is_popular">
                                                        Popular
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_new" name="is_new" value="1">
                                                    <label class="form-check-label" for="is_new">
                                                        New
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_seasonal" name="is_seasonal" value="1">
                                                    <label class="form-check-label" for="is_seasonal">
                                                        Seasonal
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted">Foods marked as Popular, New, or Seasonal will appear in the "Món Ăn Đặc Sắc" section on the homepage.</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="image" class="form-label">Food Image</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <div class="form-text">Recommended size: 800x600px. Max file size: 2MB</div>

                                    <div class="image-preview mt-3" id="imagePreview" style="display: none;">
                                        <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Food Item
                                    </button>
                                    <a href="/buffet_booking_mvc/admin/foods" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>                </div>

                <div class="col-lg-4">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title">Tips</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled small text-muted">
                                <li><i class="fas fa-check text-success"></i> Use clear, appetizing food images</li>
                                <li><i class="fas fa-check text-success"></i> Keep descriptions concise but informative</li>
                                <li><i class="fas fa-check text-success"></i> Set accurate pricing and availability</li>
                                <li><i class="fas fa-check text-success"></i> Include important allergen information</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>



<script>
    // Set up site URL for admin functions
    window.SITE_URL = '<?= SITE_URL ?>';

    document.addEventListener('DOMContentLoaded', function() {
        initializeFoodCreateForm();
    });
</script>
<script>
// Buffet item checkbox handler
document.addEventListener('DOMContentLoaded', function() {
    const buffetCheckbox = document.getElementById('is_buffet_item');
    const priceInput = document.getElementById('price');

    if (buffetCheckbox && priceInput) {
        buffetCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Save current price value
                priceInput.dataset.originalPrice = priceInput.value;
                priceInput.value = '0';
                priceInput.readOnly = true;
                priceInput.classList.add('bg-light');

                // Show tooltip
                if (!priceInput.title) {
                    priceInput.title = 'Price is automatically set to 0 for buffet items';
                }
            } else {
                // Restore original price
                if (priceInput.dataset.originalPrice) {
                    priceInput.value = priceInput.dataset.originalPrice;
                }
                priceInput.readOnly = false;
                priceInput.classList.remove('bg-light');
                priceInput.title = '';
            }
        });

        // Initialize on page load
        if (buffetCheckbox.checked) {
            priceInput.readOnly = true;
            priceInput.classList.add('bg-light');
            priceInput.title = 'Price is automatically set to 0 for buffet items';
        }
    }
});
</script>
