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
                <h1 class="h2">Edit Table <?= htmlspecialchars($data['table']['table_number']) ?></h1>
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
                            <form method="POST" action="/admin/tables/edit/<?= $data['table']['id'] ?>" class="needs-validation" novalidate>
                                <input type="hidden" name="csrf_token" value="<?= $data['csrf_token'] ?>">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="table_number" class="form-label">Table Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="table_number" name="table_number"
                                                   value="<?= htmlspecialchars($data['table']['table_number']) ?>" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid table number.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="capacity" class="form-label">Capacity (Guests) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="capacity" name="capacity"
                                                   min="1" max="20" value="<?= $data['table']['capacity'] ?>" required>
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
                                                <option value="Main Dining" <?= $data['table']['location'] === 'Main Dining' ? 'selected' : '' ?>>Main Dining</option>
                                                <option value="Private Room" <?= $data['table']['location'] === 'Private Room' ? 'selected' : '' ?>>Private Room</option>
                                                <option value="Terrace" <?= $data['table']['location'] === 'Terrace' ? 'selected' : '' ?>>Terrace</option>
                                                <option value="Bar Area" <?= $data['table']['location'] === 'Bar Area' ? 'selected' : '' ?>>Bar Area</option>
                                                <option value="VIP Section" <?= $data['table']['location'] === 'VIP Section' ? 'selected' : '' ?>>VIP Section</option>
                                                <option value="Outdoor" <?= $data['table']['location'] === 'Outdoor' ? 'selected' : '' ?>>Outdoor</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 form-check mt-4">
                                            <input type="checkbox" class="form-check-input" id="is_available" name="is_available"
                                                   <?= $data['table']['is_available'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="is_available">
                                                Available for booking
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"
                                              placeholder="Optional description about the table"><?= htmlspecialchars($data['table']['description'] ?? '') ?></textarea>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="/admin/tables" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Table
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Booking History -->
                    <?php if (!empty($data['bookingHistory'])): ?>
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Recent Booking History</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Customer</th>
                                            <th>Guests</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['bookingHistory'] as $booking): ?>
                                        <tr>
                                            <td><?= date('M j, Y', strtotime($booking['booking_date'])) ?></td>
                                            <td><?= date('g:i A', strtotime($booking['booking_time'])) ?></td>
                                            <td><?= htmlspecialchars($booking['customer_name']) ?></td>
                                            <td><?= $booking['guest_count'] ?></td>
                                            <td>
                                                <span class="badge bg-<?= $booking['status'] === 'completed' ? 'success' : ($booking['status'] === 'confirmed' ? 'primary' : 'secondary') ?>">
                                                    <?= ucfirst($booking['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <!-- Table Preview -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Table Preview</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="table-preview">
                                <div class="table-icon mb-3">
                                    <i class="fas fa-table fa-4x text-muted"></i>
                                </div>
                                <h5 id="preview-number">Table <?= htmlspecialchars($data['table']['table_number']) ?></h5>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-users"></i>
                                    <span id="preview-capacity"><?= $data['table']['capacity'] ?></span> guests
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span id="preview-location"><?= htmlspecialchars($data['table']['location'] ?: 'No location') ?></span>
                                </p>
                                <span class="badge <?= $data['table']['is_available'] ? 'bg-success' : 'bg-secondary' ?>" id="preview-status">
                                    <?= $data['table']['is_available'] ? 'Available' : 'Unavailable' ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Table Statistics -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Table Statistics</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="stat-item">
                                        <h4 class="stat-number text-primary"><?= count($data['bookingHistory'] ?? []) ?></h4>
                                        <p class="stat-label">Total Bookings</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <h4 class="stat-number text-success">
                                            <?= count(array_filter($data['bookingHistory'] ?? [], fn($b) => $b['status'] === 'completed')) ?>
                                        </h4>
                                        <p class="stat-label">Completed</p>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="small text-muted">
                                <p><strong>Created:</strong> <?= date('M j, Y g:i A', strtotime($data['table']['created_at'])) ?></p>
                                <p><strong>Last Updated:</strong> <?= date('M j, Y g:i A', strtotime($data['table']['updated_at'] ?? $data['table']['created_at'])) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="viewFullHistory()">
                                    <i class="fas fa-history"></i> View Full History
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="createBooking()">
                                    <i class="fas fa-plus"></i> Create Booking
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="toggleAvailability()">
                                    <i class="fas fa-toggle-on"></i> Toggle Availability
                                </button>
                            </div>
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

function viewFullHistory() {
    // Redirect to full booking history for this table
    window.location.href = `/admin/bookings?table_id=<?= $data['table']['id'] ?>`;
}

function createBooking() {
    // Redirect to create booking with this table pre-selected
    window.location.href = `/admin/bookings/create?table_id=<?= $data['table']['id'] ?>`;
}

function toggleAvailability() {
    const checkbox = document.getElementById('is_available');
    checkbox.checked = !checkbox.checked;

    // Trigger preview update
    const event = new Event('change');
    checkbox.dispatchEvent(event);

    // Show confirmation
    const action = checkbox.checked ? 'available' : 'unavailable';
    if (confirm(`Make this table ${action}?`)) {
        // Auto-submit form or make AJAX call
        document.querySelector('form').submit();
    } else {
        // Revert checkbox
        checkbox.checked = !checkbox.checked;
        checkbox.dispatchEvent(event);
    }
}
</script>

<?php require_once 'views/admin/layouts/footer.php'; ?>
