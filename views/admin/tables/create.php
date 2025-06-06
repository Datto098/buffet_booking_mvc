<?php
$pageTitle = $data['title'];
require_once 'views/admin/layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php require_once 'views/admin/layouts/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Add New Table</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="/admin/tables" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Tables
                    </a>
                </div>
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
                    <!-- Table Form -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Table Information</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="/admin/tables/create" class="needs-validation" novalidate>
                                <input type="hidden" name="csrf_token" value="<?= $data['csrf_token'] ?>">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="table_number" class="form-label">Table Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="table_number" name="table_number" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid table number.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="capacity" class="form-label">Capacity (Guests) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" max="20" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid capacity between 1 and 20.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="location" class="form-label">Location</label>
                                            <select class="form-select" id="location" name="location">
                                                <option value="">Select Location</option>
                                                <option value="Main Dining">Main Dining</option>
                                                <option value="Private Room">Private Room</option>
                                                <option value="Terrace">Terrace</option>
                                                <option value="Bar Area">Bar Area</option>
                                                <option value="VIP Section">VIP Section</option>
                                                <option value="Outdoor">Outdoor</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 form-check mt-4">
                                            <input type="checkbox" class="form-check-input" id="is_available" name="is_available" checked>
                                            <label class="form-check-label" for="is_available">
                                                Available for booking
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Optional description about the table (special features, location details, etc.)"></textarea>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="/admin/tables" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Table
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Table Preview -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Preview</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="table-preview">
                                <div class="table-icon mb-3">
                                    <i class="fas fa-table fa-4x text-muted"></i>
                                </div>
                                <h5 id="preview-number">Table #</h5>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-users"></i>
                                    <span id="preview-capacity">0</span> guests
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span id="preview-location">No location</span>
                                </p>
                                <span class="badge bg-success" id="preview-status">Available</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Tips</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success"></i>
                                    Use unique table numbers
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success"></i>
                                    Set realistic capacity
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success"></i>
                                    Choose appropriate location
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success"></i>
                                    Add helpful descriptions
                                </li>
                                <li>
                                    <i class="fas fa-check text-success"></i>
                                    Consider table layout
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Real-time preview
    const tableNumberInput = document.getElementById('table_number');
    const capacityInput = document.getElementById('capacity');
    const locationSelect = document.getElementById('location');
    const availableCheckbox = document.getElementById('is_available');

    function updatePreview() {
        // Update table number
        const number = tableNumberInput.value || '#';
        document.getElementById('preview-number').textContent = `Table ${number}`;

        // Update capacity
        const capacity = capacityInput.value || '0';
        document.getElementById('preview-capacity').textContent = capacity;

        // Update location
        const location = locationSelect.value || 'No location';
        document.getElementById('preview-location').textContent = location;

        // Update status
        const statusBadge = document.getElementById('preview-status');
        if (availableCheckbox.checked) {
            statusBadge.textContent = 'Available';
            statusBadge.className = 'badge bg-success';
        } else {
            statusBadge.textContent = 'Unavailable';
            statusBadge.className = 'badge bg-secondary';
        }
    }

    // Add event listeners for real-time preview
    tableNumberInput.addEventListener('input', updatePreview);
    capacityInput.addEventListener('input', updatePreview);
    locationSelect.addEventListener('change', updatePreview);
    availableCheckbox.addEventListener('change', updatePreview);

    // Validate table number uniqueness
    tableNumberInput.addEventListener('blur', function() {
        const tableNumber = this.value;
        if (tableNumber) {
            // You could add AJAX validation here to check if table number exists
            // For now, we'll rely on server-side validation
        }
    });

    // Capacity validation
    capacityInput.addEventListener('input', function() {
        const capacity = parseInt(this.value);
        if (capacity < 1) {
            this.setCustomValidity('Capacity must be at least 1');
        } else if (capacity > 20) {
            this.setCustomValidity('Capacity cannot exceed 20');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>

<?php require_once 'views/admin/layouts/footer.php'; ?>
