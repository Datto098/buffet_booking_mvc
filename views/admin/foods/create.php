<?php include '../views/admin/layouts/header.php'; ?>

<div class="admin-wrapper">
    <?php include '../views/admin/layouts/sidebar.php'; ?>

    <div class="admin-content">
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">Add New Food Item</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/admin/foods">Food Management</a></li>
                            <li class="breadcrumb-item active">Add New</li>
                        </ol>
                    </nav>
                </div>
                <a href="/admin/foods" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Back to Foods
                </a>
            </div>
        </div>

        <div class="content-body">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Food Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="/admin/foods/create" method="POST" enctype="multipart/form-data" id="createFoodForm">
                                <?php echo $this->csrfToken(); ?>

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
                                </div>

                                <div class="row">
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
                                            <label for="availability" class="form-label">Availability</label>
                                            <select class="form-select" id="availability" name="availability">
                                                <option value="available">Available</option>
                                                <option value="unavailable">Unavailable</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter food description..."></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="ingredients" class="form-label">Ingredients</label>
                                    <textarea class="form-control" id="ingredients" name="ingredients" rows="3" placeholder="List the main ingredients..."></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="spice_level" class="form-label">Spice Level</label>
                                            <select class="form-select" id="spice_level" name="spice_level">
                                                <option value="mild">Mild</option>
                                                <option value="medium">Medium</option>
                                                <option value="hot">Hot</option>
                                                <option value="very_hot">Very Hot</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="prep_time" class="form-label">Prep Time (minutes)</label>
                                            <input type="number" class="form-control" id="prep_time" name="prep_time" min="1" placeholder="15">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="calories" class="form-label">Calories (per serving)</label>
                                            <input type="number" class="form-control" id="calories" name="calories" min="0" placeholder="250">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_vegetarian" name="is_vegetarian" value="1">
                                        <label class="form-check-label" for="is_vegetarian">
                                            Vegetarian
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1">
                                        <label class="form-check-label" for="is_featured">
                                            Featured Item
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Food Item
                                    </button>
                                    <a href="/admin/foods" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Food Image</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="image" class="form-label">Upload Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">Recommended size: 800x600px. Max file size: 2MB</div>
                            </div>

                            <div class="image-preview mt-3" id="imagePreview" style="display: none;">
                                <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                        </div>
                    </div>

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
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview functionality
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = imagePreview.querySelector('img');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    });

    // Form validation
    const form = document.getElementById('createFoodForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Reset previous validation states
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        let isValid = true;

        // Validate required fields
        const requiredFields = ['name', 'price', 'category_id'];
        requiredFields.forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                field.nextElementSibling.textContent = 'This field is required';
                isValid = false;
            }
        });

        // Validate price
        const priceField = form.querySelector('[name="price"]');
        if (priceField.value && (parseFloat(priceField.value) < 0 || parseFloat(priceField.value) > 9999)) {
            priceField.classList.add('is-invalid');
            priceField.nextElementSibling.textContent = 'Price must be between 0 and 9999';
            isValid = false;
        }

        if (isValid) {
            showLoadingButton(form.querySelector('button[type="submit"]'));
            form.submit();
        }
    });
});
</script>

<?php include '../views/admin/layouts/footer.php'; ?>
