<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'views/admin/layouts/header.php'; ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php require_once 'views/admin/layouts/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Flash Messages -->
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show mt-3" role="alert">
                        <?= htmlspecialchars($_SESSION['flash_message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php
                    unset($_SESSION['flash_message']);
                    unset($_SESSION['flash_type']);
                    ?>
                <?php endif; ?>

                <!-- Page Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2">
                            <i class="fas fa-plus-circle me-2"></i>Add New Category
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/categories">Categories</a></li>
                                <li class="breadcrumb-item active">Add Category</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="<?= SITE_URL ?>/admin/categories" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Categories
                        </a>
                    </div>
                </div>

                <!-- Category Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Category Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="categoryForm" action="<?= SITE_URL ?>/admin/categories/store" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">
                                                Category Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="name"
                                                   name="name"
                                                   required
                                                   maxlength="100"
                                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="active" <?= ($_POST['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                                                <option value="inactive" <?= ($_POST['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control"
                                                  id="description"
                                                  name="description"
                                                  rows="3"
                                                  maxlength="500"
                                                  placeholder="Enter category description..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                                        <div class="form-text">Maximum 500 characters</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="sort_order" class="form-label">Sort Order</label>
                                            <input type="number"
                                                   class="form-control"
                                                   id="sort_order"
                                                   name="sort_order"
                                                   min="0"
                                                   max="999"
                                                   value="<?= htmlspecialchars($_POST['sort_order'] ?? '0') ?>">
                                            <div class="form-text">Lower numbers appear first</div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="parent_id" class="form-label">Parent Category</label>
                                            <select class="form-select" id="parent_id" name="parent_id">
                                                <option value="">None (Main Category)</option>
                                                <?php if (isset($categories) && !empty($categories)): ?>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?= $category['id'] ?>"
                                                                <?= ($_POST['parent_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($category['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                        <div>
                                            <button type="button" class="btn btn-outline-primary me-2" onclick="previewCategory()">
                                                <i class="fas fa-eye"></i> Preview
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Create Category
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Guidelines Card -->
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-lightbulb me-2"></i>Guidelines
                                </h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0 small">
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Use clear, descriptive names
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Keep descriptions concise
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Set appropriate sort order
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Use subcategories for organization
                                    </li>
                                    <li class="mb-0">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Start with active status
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Preview Card -->
                        <div class="card shadow" id="previewCard" style="display: none;">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-eye me-2"></i>Preview
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="categoryPreview">
                                    <!-- Preview content will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php require_once 'views/admin/layouts/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeCategoryForm();
        });

        function initializeCategoryForm() {
            const form = document.getElementById('categoryForm');
            if (form) {
                form.addEventListener('submit', handleCategorySubmit);
            }

            // Real-time character counting
            const description = document.getElementById('description');
            if (description) {
                description.addEventListener('input', updateCharacterCount);
            }

            // Name validation
            const nameInput = document.getElementById('name');
            if (nameInput) {
                nameInput.addEventListener('input', validateName);
            }
        }

        function handleCategorySubmit(e) {
            e.preventDefault();

            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRFToken': document.querySelector('input[name="csrf_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Category created successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = '<?= SITE_URL ?>/admin/categories';
                    }, 1500);
                } else {
                    showNotification(data.message || 'Error creating category', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }

        function validateName() {
            const nameInput = document.getElementById('name');
            const value = nameInput.value.trim();

            if (value.length < 2) {
                nameInput.setCustomValidity('Category name must be at least 2 characters');
                nameInput.classList.add('is-invalid');
            } else if (value.length > 100) {
                nameInput.setCustomValidity('Category name must be less than 100 characters');
                nameInput.classList.add('is-invalid');
            } else {
                nameInput.setCustomValidity('');
                nameInput.classList.remove('is-invalid');
                nameInput.classList.add('is-valid');
            }
        }

        function updateCharacterCount() {
            const description = document.getElementById('description');
            const maxLength = 500;
            const currentLength = description.value.length;
            const formText = description.nextElementSibling;

            formText.textContent = `${currentLength}/${maxLength} characters`;

            if (currentLength > maxLength * 0.9) {
                formText.classList.add('text-warning');
            } else {
                formText.classList.remove('text-warning');
            }
        }

        function previewCategory() {
            const name = document.getElementById('name').value;
            const description = document.getElementById('description').value;
            const status = document.getElementById('status').value;
            const sortOrder = document.getElementById('sort_order').value;

            if (!name.trim()) {
                showNotification('Please enter a category name to preview', 'warning');
                return;
            }

            const previewHtml = `
                <div class="card border-0">
                    <div class="card-body p-0">
                        <h6 class="mb-2">${escapeHtml(name)}</h6>
                        <p class="text-muted small mb-2">${escapeHtml(description) || 'No description'}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-${status === 'active' ? 'success' : 'secondary'}">${status}</span>
                            <small class="text-muted">Order: ${sortOrder}</small>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('categoryPreview').innerHTML = previewHtml;
            document.getElementById('previewCard').style.display = 'block';
        }

        function resetForm() {
            if (confirm('Are you sure you want to reset all fields?')) {
                document.getElementById('categoryForm').reset();
                document.getElementById('previewCard').style.display = 'none';

                // Remove validation classes
                const inputs = document.querySelectorAll('.is-valid, .is-invalid');
                inputs.forEach(input => {
                    input.classList.remove('is-valid', 'is-invalid');
                });
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }
    </script>
</body>
</html>
