<?php include '../views/admin/layouts/header.php'; ?>

<div class="admin-wrapper">
    <?php include '../views/admin/layouts/sidebar.php'; ?>

    <div class="admin-content">
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">Category Management</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item active">Categories</li>
                        </ol>
                    </nav>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                    <i class="fas fa-plus"></i> Add Category
                </button>
            </div>
        </div>

        <div class="content-body">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Food Categories</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($categories)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th>Food Items</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="categoriesTableBody">
                                            <?php foreach ($categories as $category): ?>
                                                <tr data-category-id="<?php echo $category['id']; ?>">
                                                    <td>
                                                        <span class="fw-medium"><?php echo htmlspecialchars($category['id']); ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php if (!empty($category['icon'])): ?>
                                                                <i class="<?php echo htmlspecialchars($category['icon']); ?> me-2 text-primary"></i>
                                                            <?php endif; ?>
                                                            <span class="fw-medium"><?php echo htmlspecialchars($category['name']); ?></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted">
                                                            <?php echo !empty($category['description']) ? htmlspecialchars(substr($category['description'], 0, 50)) . '...' : 'No description'; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-dark">
                                                            <?php echo $category['food_count'] ?? 0; ?> items
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php echo ($category['status'] == 'active') ? 'success' : 'secondary'; ?>">
                                                            <?php echo ucfirst(htmlspecialchars($category['status'])); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                                    onclick="editCategory(<?php echo $category['id']; ?>)"
                                                                    data-bs-toggle="tooltip" title="Edit Category">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <?php if ($category['food_count'] == 0): ?>
                                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                                        onclick="deleteCategory(<?php echo $category['id']; ?>)"
                                                                        data-bs-toggle="tooltip" title="Delete Category">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Categories Found</h5>
                                    <p class="text-muted">Create your first food category to get started.</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                                        <i class="fas fa-plus"></i> Add First Category
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Category Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="stat-item">
                                        <h4 class="stat-number text-primary"><?php echo count($categories ?? []); ?></h4>
                                        <p class="stat-label">Total Categories</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <h4 class="stat-number text-success"><?php echo $stats['active_categories'] ?? 0; ?></h4>
                                        <p class="stat-label">Active</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row text-center mt-3">
                                <div class="col-6">
                                    <div class="stat-item">
                                        <h4 class="stat-number text-info"><?php echo $stats['total_foods'] ?? 0; ?></h4>
                                        <p class="stat-label">Total Foods</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <h4 class="stat-number text-warning"><?php echo $stats['empty_categories'] ?? 0; ?></h4>
                                        <p class="stat-label">Empty</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title">Popular Categories</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($popularCategories)): ?>
                                <div class="popular-categories">
                                    <?php foreach ($popularCategories as $category): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($category['icon'])): ?>
                                                    <i class="<?php echo htmlspecialchars($category['icon']); ?> me-2 text-primary"></i>
                                                <?php endif; ?>
                                                <span><?php echo htmlspecialchars($category['name']); ?></span>
                                            </div>
                                            <span class="badge bg-primary"><?php echo $category['order_count']; ?> orders</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center">No data available</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title">Quick Tips</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled small text-muted">
                                <li><i class="fas fa-check text-success"></i> Use descriptive category names</li>
                                <li><i class="fas fa-check text-success"></i> Add icons to make categories more visual</li>
                                <li><i class="fas fa-check text-success"></i> Group similar food items together</li>
                                <li><i class="fas fa-check text-success"></i> Keep categories organized and logical</li>
                                <li><i class="fas fa-check text-success"></i> Consider customer browsing patterns</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
            <div class="modal-body">
                <form id="createCategoryForm">
                    <?php echo $this->csrfToken(); ?>
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="categoryDescription" name="description" rows="3" placeholder="Brief description of this category..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="categoryIcon" class="form-label">Icon (FontAwesome class)</label>
                        <input type="text" class="form-control" id="categoryIcon" name="icon" placeholder="fas fa-utensils">
                        <div class="form-text">Example: fas fa-utensils, fas fa-pizza-slice, fas fa-coffee</div>
                    </div>
                    <div class="mb-3">
                        <label for="categoryStatus" class="form-label">Status</label>
                        <select class="form-select" id="categoryStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="categorySortOrder" class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="categorySortOrder" name="sort_order" value="0" min="0">
                        <div class="form-text">Lower numbers appear first</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCategory()">Create Category</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editCategoryForm">
                    <?php echo $this->csrfToken(); ?>
                    <input type="hidden" id="editCategoryId" name="category_id">
                    <div class="mb-3">
                        <label for="editCategoryName" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editCategoryName" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editCategoryDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editCategoryDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editCategoryIcon" class="form-label">Icon (FontAwesome class)</label>
                        <input type="text" class="form-control" id="editCategoryIcon" name="icon" placeholder="fas fa-utensils">
                        <div class="form-text">Example: fas fa-utensils, fas fa-pizza-slice, fas fa-coffee</div>
                    </div>
                    <div class="mb-3">
                        <label for="editCategoryStatus" class="form-label">Status</label>
                        <select class="form-select" id="editCategoryStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editCategorySortOrder" class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="editCategorySortOrder" name="sort_order" min="0">
                        <div class="form-text">Lower numbers appear first</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateCategory()">Update Category</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function saveCategory() {
    const form = document.getElementById('createCategoryForm');
    const formData = new FormData(form);

    // Reset validation states
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    fetch('/admin/categories/create', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Category created successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('createCategoryModal')).hide();
            location.reload();
        } else {
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        input.nextElementSibling.textContent = data.errors[field][0];
                    }
                });
            } else {
                showNotification(data.message || 'Failed to create category', 'error');
            }
        }
    })
    .catch(error => {
        showNotification('Error creating category', 'error');
    });
}

function editCategory(categoryId) {
    fetch(`/admin/categories/get/${categoryId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const category = data.category;
                document.getElementById('editCategoryId').value = category.id;
                document.getElementById('editCategoryName').value = category.name;
                document.getElementById('editCategoryDescription').value = category.description || '';
                document.getElementById('editCategoryIcon').value = category.icon || '';
                document.getElementById('editCategoryStatus').value = category.status;
                document.getElementById('editCategorySortOrder').value = category.sort_order || 0;

                const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                modal.show();
            } else {
                showNotification('Failed to load category data', 'error');
            }
        })
        .catch(error => {
            showNotification('Error loading category data', 'error');
        });
}

function updateCategory() {
    const form = document.getElementById('editCategoryForm');
    const formData = new FormData(form);

    // Reset validation states
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    fetch('/admin/categories/update', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Category updated successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editCategoryModal')).hide();
            location.reload();
        } else {
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        input.nextElementSibling.textContent = data.errors[field][0];
                    }
                });
            } else {
                showNotification(data.message || 'Failed to update category', 'error');
            }
        }
    })
    .catch(error => {
        showNotification('Error updating category', 'error');
    });
}

function deleteCategory(categoryId) {
    if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        return;
    }

    fetch('/admin/categories/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ category_id: categoryId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Category deleted successfully', 'success');
            document.querySelector(`tr[data-category-id="${categoryId}"]`).remove();
        } else {
            showNotification(data.message || 'Failed to delete category', 'error');
        }
    })
    .catch(error => {
        showNotification('Error deleting category', 'error');
    });
}

// Icon preview functionality
document.getElementById('categoryIcon').addEventListener('input', function(e) {
    // You could add icon preview functionality here
});

document.getElementById('editCategoryIcon').addEventListener('input', function(e) {
    // You could add icon preview functionality here
});
</script>

<?php include '../views/admin/layouts/footer.php'; ?>
