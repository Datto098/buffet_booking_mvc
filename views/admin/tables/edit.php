<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token ?? ''; ?>">
    <title>Edit Table - Admin</title>
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
                            <i class="fas fa-chair"></i> Edit Table <?= htmlspecialchars($data['table']['table_number'] ?? '') ?>
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/tables">Tables</a></li>
                                <li class="breadcrumb-item active">Edit Table</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="<?= SITE_URL ?>/admin/tables" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Tables
                            </a>
                        </div>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Delete Table
                        </button>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if (isset($_SESSION['flash'])): ?>
                    <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                        <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                            <i class="fas fa-<?= $type === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                            <?= htmlspecialchars($message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['flash']); ?>
                <?php endif; ?>

                <!-- Table Edit Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chair"></i> Table Information
                                </h5>
                                <span class="badge bg-<?= ($data['table']['status'] ?? 'available') == 'available' ? 'success' : (($data['table']['status'] ?? '') == 'occupied' ? 'danger' : 'warning') ?>">
                                    <?= ucfirst($data['table']['status'] ?? 'available') ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <form action="<?= SITE_URL ?>/admin/tables/update/<?= $data['table']['id'] ?>" method="POST" id="editTableForm">
                                    <input type="hidden" name="csrf_token" value="<?= $data['csrf_token'] ?? $_SESSION['csrf_token'] ?? '' ?>">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="table_number" class="form-label">Table Number <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                <input type="text" class="form-control" id="table_number" name="table_number"
                                                       value="<?= htmlspecialchars($data['table']['table_number'] ?? '') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="capacity" class="form-label">Seating Capacity <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-users"></i></span>
                                                <input type="number" class="form-control" id="capacity" name="capacity"
                                                       value="<?= htmlspecialchars($data['table']['capacity'] ?? '') ?>"
                                                       min="1" max="20" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Table Status <span class="text-danger">*</span></label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="available" <?= ($data['table']['status'] ?? '') == 'available' ? 'selected' : '' ?>>Available</option>
                                                <option value="occupied" <?= ($data['table']['status'] ?? '') == 'occupied' ? 'selected' : '' ?>>Occupied</option>
                                                <option value="reserved" <?= ($data['table']['status'] ?? '') == 'reserved' ? 'selected' : '' ?>>Reserved</option>
                                                <option value="maintenance" <?= ($data['table']['status'] ?? '') == 'maintenance' ? 'selected' : '' ?>>Under Maintenance</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="location" class="form-label">Location/Area</label>
                                            <select class="form-select" id="location" name="location">
                                                <option value="">Select Location</option>
                                                <option value="main_hall" <?= ($data['table']['location'] ?? '') == 'main_hall' ? 'selected' : '' ?>>Main Hall</option>
                                                <option value="private_room" <?= ($data['table']['location'] ?? '') == 'private_room' ? 'selected' : '' ?>>Private Room</option>
                                                <option value="terrace" <?= ($data['table']['location'] ?? '') == 'terrace' ? 'selected' : '' ?>>Terrace</option>
                                                <option value="vip_section" <?= ($data['table']['location'] ?? '') == 'vip_section' ? 'selected' : '' ?>>VIP Section</option>
                                                <option value="outdoor" <?= ($data['table']['location'] ?? '') == 'outdoor' ? 'selected' : '' ?>>Outdoor</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="table_type" class="form-label">Table Type</label>
                                            <select class="form-select" id="table_type" name="table_type">
                                                <option value="regular" <?= ($data['table']['table_type'] ?? '') == 'regular' ? 'selected' : '' ?>>Regular</option>
                                                <option value="booth" <?= ($data['table']['table_type'] ?? '') == 'booth' ? 'selected' : '' ?>>Booth</option>
                                                <option value="round" <?= ($data['table']['table_type'] ?? '') == 'round' ? 'selected' : '' ?>>Round Table</option>
                                                <option value="rectangular" <?= ($data['table']['table_type'] ?? '') == 'rectangular' ? 'selected' : '' ?>>Rectangular</option>
                                                <option value="bar_height" <?= ($data['table']['table_type'] ?? '') == 'bar_height' ? 'selected' : '' ?>>Bar Height</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="price_per_hour" class="form-label">Price per Hour</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                                <input type="number" class="form-control" id="price_per_hour" name="price_per_hour"
                                                       value="<?= htmlspecialchars($data['table']['price_per_hour'] ?? '') ?>"
                                                       step="0.01" min="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"
                                                  placeholder="Describe table features, special amenities, etc..."><?= htmlspecialchars($data['table']['description'] ?? '') ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="has_window_view" name="has_window_view" value="1"
                                                       <?= ($data['table']['has_window_view'] ?? 0) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="has_window_view">
                                                    Window View
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_accessible" name="is_accessible" value="1"
                                                       <?= ($data['table']['is_accessible'] ?? 0) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="is_accessible">
                                                    Wheelchair Accessible
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="has_power_outlet" name="has_power_outlet" value="1"
                                                       <?= ($data['table']['has_power_outlet'] ?? 0) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="has_power_outlet">
                                                    Power Outlet Available
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Table
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Table Layout Preview -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-eye"></i> Table Preview
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="table-preview p-4">
                                    <div class="table-visual mx-auto" style="width: 120px; height: 80px; background: linear-gradient(135deg, #f8f9fc 0%, #e8ecf1 100%); border: 3px solid #dee2e6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <div class="text-center">
                                            <strong><?= htmlspecialchars($data['table']['table_number'] ?? 'T#') ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($data['table']['capacity'] ?? '0') ?> seats</small>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-muted small mt-3">Visual representation of table layout</p>
                            </div>
                        </div>

                        <!-- Table Statistics -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-chart-bar"></i> Table Statistics
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h4 class="text-primary mb-1"><?= $data['table']['total_bookings'] ?? 0 ?></h4>
                                            <small class="text-muted">Total Bookings</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success mb-1"><?= $data['table']['revenue'] ?? '0.00' ?></h4>
                                        <small class="text-muted">Revenue ($)</small>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Created:</small>
                                    <small><?= date('M d, Y', strtotime($data['table']['created_at'] ?? 'now')) ?></small>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Last Booking:</small>
                                    <small><?= !empty($data['table']['last_booking_date']) ? date('M d, Y', strtotime($data['table']['last_booking_date'])) : 'Never' ?></small>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Utilization Rate:</small>
                                    <small><?= $data['table']['utilization_rate'] ?? '0' ?>%</small>
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
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="viewTableBookings(<?= $data['table']['id'] ?>)">
                                        <i class="fas fa-calendar"></i> View Bookings
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="createBooking(<?= $data['table']['id'] ?>)">
                                        <i class="fas fa-plus"></i> New Booking
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="markMaintenance(<?= $data['table']['id'] ?>)">
                                        <i class="fas fa-tools"></i> Mark for Maintenance
                                    </button>
                                    <?php if (($data['table']['status'] ?? '') == 'available'): ?>
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="toggleTableStatus(<?= $data['table']['id'] ?>, 'occupied')">
                                            <i class="fas fa-user-check"></i> Mark Occupied
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="toggleTableStatus(<?= $data['table']['id'] ?>, 'available')">
                                            <i class="fas fa-check"></i> Mark Available
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
    <div class="modal fade" id="deleteTableModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this table?</p>
                    <p class="text-danger"><strong>Warning:</strong> This will cancel all future bookings for this table.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="<?= SITE_URL ?>/admin/tables/delete/<?= $data['table']['id'] ?>" method="POST" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= $data['csrf_token'] ?? $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Table
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'views/admin/layouts/footer.php'; ?>

    <script>
        // Form validation
        document.getElementById('editTableForm').addEventListener('submit', function(e) {
            const tableNumber = document.getElementById('table_number').value.trim();
            const capacity = document.getElementById('capacity').value;

            if (!tableNumber) {
                e.preventDefault();
                alert('Table number is required!');
                return false;
            }

            if (!capacity || capacity < 1) {
                e.preventDefault();
                alert('Valid seating capacity is required!');
                return false;
            }
        });

        // Delete confirmation
        function confirmDelete() {
            new bootstrap.Modal(document.getElementById('deleteTableModal')).show();
        }

        // Quick action functions
        function viewTableBookings(tableId) {
            window.location.href = `<?= SITE_URL ?>/admin/bookings?table_id=${tableId}`;
        }

        function createBooking(tableId) {
            window.location.href = `<?= SITE_URL ?>/admin/bookings/create?table_id=${tableId}`;
        }

        function markMaintenance(tableId) {
            if (confirm('Mark this table for maintenance? This will make it unavailable for bookings.')) {
                toggleTableStatus(tableId, 'maintenance');
            }
        }

        function toggleTableStatus(tableId, status) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `<?= SITE_URL ?>/admin/tables/${tableId}/toggle-status`;

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = 'csrf_token';
            csrfToken.value = '<?= $data['csrf_token'] ?? $_SESSION['csrf_token'] ?? '' ?>';

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;

            form.appendChild(csrfToken);
            form.appendChild(statusInput);
            document.body.appendChild(form);
            form.submit();
        }

        // Update table preview when capacity changes
        document.getElementById('capacity').addEventListener('input', function(e) {
            const capacity = e.target.value;
            const preview = document.querySelector('.table-visual small');
            if (preview) {
                preview.textContent = `${capacity} seats`;
            }
        });

        // Update table preview when table number changes
        document.getElementById('table_number').addEventListener('input', function(e) {
            const tableNumber = e.target.value;
            const preview = document.querySelector('.table-visual strong');
            if (preview) {
                preview.textContent = tableNumber || 'T#';
            }
        });
    </script>

    <style>
        .table-visual {
            transition: all 0.3s ease;
        }

        .table-visual:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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

                                <div class="d-flex justify-content-between">                                    <a href="<?= SITE_URL ?>/admin/tables" class="btn btn-secondary">
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
    window.location.href = `<?= SITE_URL ?>/admin/bookings?table_id=<?= $data['table']['id'] ?>`;
}

function createBooking() {
    // Redirect to create booking with this table pre-selected
    window.location.href = `<?= SITE_URL ?>/admin/bookings/create?table_id=<?= $data['table']['id'] ?>`;
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
