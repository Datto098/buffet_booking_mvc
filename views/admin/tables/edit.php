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
                                </h5>                                <span class="badge bg-<?= ($data['table']['is_available'] ?? 1) ? 'success' : 'danger' ?>">
                                    <?= ($data['table']['is_available'] ?? 1) ? 'Available' : 'Unavailable' ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <form action="<?= SITE_URL ?>/admin/tables/edit/<?= $data['table']['id'] ?>" method="POST" id="editTableForm">
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
                                    </div>                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="is_available" class="form-label">Table Availability <span class="text-danger">*</span></label>
                                            <select class="form-select" id="is_available" name="is_available" required>
                                                <option value="1" <?= ($data['table']['is_available'] ?? 1) == 1 ? 'selected' : '' ?>>Available</option>
                                                <option value="0" <?= ($data['table']['is_available'] ?? 1) == 0 ? 'selected' : '' ?>>Unavailable</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="location" class="form-label">Location (Address)</label>
                                            <select class="form-select" id="location" name="location">
                                                <option value="">Select Address</option>
                                                <?php if (!empty($data['addresses'])): ?>
                                                    <?php foreach ($data['addresses'] as $addr): ?>
                                                        <option value="<?= htmlspecialchars($addr['address']) ?>" <?= ($data['table']['location'] ?? '') == $addr['address'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($addr['address']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"
                                                  placeholder="Describe table features, special amenities, etc..."><?= htmlspecialchars($data['table']['description'] ?? '') ?></textarea>
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
                                    </button>                                    <button type="button" class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-tools"></i> Maintenance Mode
                                    </button>
                                    <?php if (($data['table']['is_available'] ?? 1) == 1): ?>
                                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="toggleTableStatus(<?= $data['table']['id'] ?>, 0)">
                                            <i class="fas fa-times-circle"></i> Mark Unavailable
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="toggleTableStatus(<?= $data['table']['id'] ?>, 1)">
                                            <i class="fas fa-check-circle"></i> Mark Available
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
        window.SITE_URL = '<?= SITE_URL ?>';

        document.addEventListener('DOMContentLoaded', function() {
            initializeTableEditForm();
        });

        // Override global functions with table-specific data
        window.viewFullHistory = () => viewFullHistory('<?= $data['table']['id'] ?>');
        window.createBooking = () => createBookingForTable('<?= $data['table']['id'] ?>');
        window.toggleAvailability = toggleTableAvailability;
        window.confirmDelete = confirmTableDelete;
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
