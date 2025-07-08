<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'views/layouts/superadmin_header.php'; ?>
    <title>Table Management - Super Admin</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Page Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2">
                            <i class="fas fa-chair me-2 text-warning"></i>Table Management
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/superadmin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Tables</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-primary" onclick="showAddTableModal()">
                            <i class="fas fa-plus me-1"></i>Add New Table
                        </button>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php
                $flash = $_SESSION['flash'] ?? [];
                foreach ($flash as $type => $message):
                ?>
                    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?= $type === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php
                endforeach;
                unset($_SESSION['flash']);
                ?>

                <!-- Statistics Cards -->                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Tables</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['total_tables'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-chair fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Available</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['available_tables'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Occupied</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['occupied_tables'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-utensils fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Capacity</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['total_capacity'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-users fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                </div>

                <!-- Filter Bar -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-filter me-2 text-warning"></i>Filters
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="<?= SITE_URL ?>/superadmin/tables" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Search Tables</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" class="form-control" name="search" placeholder="Table number, location, description..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">All Status</option>
                                    <option value="available" <?= ($_GET['status'] ?? '') == 'available' ? 'selected' : '' ?>>Available</option>
                                    <option value="unavailable" <?= ($_GET['status'] ?? '') == 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Location</label>
                                <select class="form-select" name="location">
                                    <option value="">All Locations</option>
                                    <?php if (!empty($locationStats)): ?>
                                        <?php foreach ($locationStats as $locationStat): ?>
                                            <option value="<?= htmlspecialchars($locationStat['location']) ?>" <?= ($_GET['location'] ?? '') == $locationStat['location'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($locationStat['location'] ?: 'Main Area') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="<?= SITE_URL ?>/superadmin/tables" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>                <!-- Tables Grid -->
                <div class="row">
                    <?php if (!empty($tables)): ?>
                        <?php foreach ($tables as $table): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm hover-shadow">
                                    <div class="card-header bg-gradient-<?php echo ($table['is_available'] ?? 1) ? 'success' : 'danger'; ?> text-white border-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0 fw-bold">
                                                <i class="fas fa-chair me-2"></i>
                                                Table <?php echo htmlspecialchars($table['table_number'] ?? 'Unknown'); ?>
                                            </h5>
                                            <span class="badge bg-white text-<?php echo ($table['is_available'] ?? 1) ? 'success' : 'danger'; ?> px-3 py-2">
                                                <i class="fas fa-<?php echo ($table['is_available'] ?? 1) ? 'check-circle' : 'times-circle'; ?> me-1"></i>
                                                <?php echo ($table['is_available'] ?? 1) ? 'Available' : 'Unavailable'; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="text-center p-3 bg-light rounded">
                                                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                                    <div class="fw-bold text-primary"><?php echo htmlspecialchars($table['capacity'] ?? 'N/A'); ?></div>
                                                    <small class="text-muted">Guests</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center p-3 bg-light rounded">
                                                    <i class="fas fa-map-marker-alt fa-2x text-info mb-2"></i>
                                                    <div class="fw-bold text-info"><?php echo htmlspecialchars($table['location'] ?: 'Main'); ?></div>
                                                    <small class="text-muted">Location</small>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if (!empty($table['description'])): ?>
                                            <div class="mb-3">
                                                <div class="bg-gradient-light p-3 rounded">
                                                    <div class="small text-muted mb-1">
                                                        <i class="fas fa-quote-left me-1"></i>Description
                                                    </div>
                                                    <div class="text-dark"><?php echo htmlspecialchars($table['description']); ?></div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Table Features -->
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            <?php if (($table['capacity'] ?? 0) >= 6): ?>
                                                <span class="badge bg-gradient-info px-3 py-2">
                                                    <i class="fas fa-users me-1"></i>Large Group
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($table['location']) && strtolower($table['location']) === 'vip'): ?>
                                                <span class="badge bg-gradient-warning px-3 py-2">
                                                    <i class="fas fa-crown me-1"></i>VIP Area
                                                </span>
                                            <?php endif; ?>
                                            <?php if (($table['is_available'] ?? 1)): ?>
                                                <span class="badge bg-gradient-success px-3 py-2">
                                                    <i class="fas fa-check me-1"></i>Ready
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white border-0 pt-0">
                                        <div class="btn-group w-100" role="group">                                            <button type="button" class="btn btn-outline-primary flex-fill me-2" onclick="editTable(<?php echo $table['id']; ?>)">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </button>
                                            <button type="button" class="btn btn-outline-danger flex-fill" onclick="deleteTable(<?php echo $table['id']; ?>)">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center py-5">
                                    <div class="mb-4">
                                        <i class="fas fa-chair fa-4x text-muted"></i>
                                    </div>
                                    <h4 class="text-muted mb-3">No tables found</h4>
                                    <p class="text-muted mb-4">Get started by adding your first table to manage restaurant seating efficiently.</p>
                                    <button type="button" class="btn btn-primary btn-lg" onclick="showAddTableModal()">
                                        <i class="fas fa-plus me-2"></i>Add First Table
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>
                            </button>                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

<!-- Add/Edit Table Modal -->
<div class="modal fade" id="tableModal" tabindex="-1" aria-labelledby="tableModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">            <div class="modal-header">
                <h5 class="modal-title" id="tableModalLabel">Add New Table</h5>
                <button type="button" class="btn-close" onclick="closeModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="tableForm">
                    <input type="hidden" id="tableId" name="table_id">
                    <div class="mb-3">
                        <label for="tableName" class="form-label">Table Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tableName" name="table_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="tableCapacity" class="form-label">Capacity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="tableCapacity" name="capacity" min="1" max="20" required>
                    </div>
                    <div class="mb-3">
                        <label for="tableLocation" class="form-label">Location</label>
                        <input type="text" class="form-control" id="tableLocation" name="location" placeholder="e.g., Main Hall, Patio, VIP Section">
                    </div>
                    <div class="mb-3">
                        <label for="tableDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="tableDescription" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="saveTable()">Save Table</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.SITE_URL = '<?= SITE_URL ?>';    function showAddTableModal() {
        console.log('showAddTableModal called');

        // Reset form and modal
        document.getElementById('tableModalLabel').textContent = 'Add New Table';
        document.getElementById('tableForm').reset();
        document.getElementById('tableId').value = '';

        // Get modal element
        const modalElement = document.getElementById('tableModal');
        if (!modalElement) {
            console.error('Modal element not found');
            return;
        }

        // Remove any existing modal backdrops
        const existingBackdrops = document.querySelectorAll('.modal-backdrop');
        existingBackdrops.forEach(backdrop => backdrop.remove());

        // Check if Bootstrap is loaded
        if (typeof bootstrap !== 'undefined') {
            try {
                // Check if modal instance already exists and dispose it
                const existingModal = bootstrap.Modal.getInstance(modalElement);
                if (existingModal) {
                    existingModal.dispose();
                }

                // Create new modal instance
                const modal = new bootstrap.Modal(modalElement, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                });
                modal.show();
                return;
            } catch (error) {
                console.error('Bootstrap modal error:', error);
            }
        }

        // jQuery fallback
        if (typeof $ !== 'undefined') {
            console.log('Using jQuery modal fallback');
            $('#tableModal').modal('show');
            return;
        }

        // Manual fallback
        console.log('Using manual modal display');
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
        document.body.classList.add('modal-open');

        // Add single backdrop
        if (!document.querySelector('.modal-backdrop')) {
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.style.zIndex = '9998';
            document.body.appendChild(backdrop);

            // Close modal when clicking backdrop
            backdrop.onclick = function() {
                closeModal();
            };
        }    }

    function closeModal() {
        const modalElement = document.getElementById('tableModal');
        if (!modalElement) return;

        // Remove all backdrops
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());

        // Hide modal
        modalElement.style.display = 'none';
        modalElement.classList.remove('show');
        document.body.classList.remove('modal-open');

        // Dispose Bootstrap modal if exists
        if (typeof bootstrap !== 'undefined') {
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.dispose();
            }
        }
    }

    function editTable(tableId) {
        fetch(`${window.SITE_URL || ''}/superadmin/tables/get/${tableId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('tableModalLabel').textContent = 'Edit Table';
                    document.getElementById('tableId').value = data.table.id;
                    document.getElementById('tableName').value = data.table.table_number || '';
                    document.getElementById('tableCapacity').value = data.table.capacity || '';
                    document.getElementById('tableLocation').value = data.table.location || '';
                    document.getElementById('tableDescription').value = data.table.description || '';

                    const modal = new bootstrap.Modal(document.getElementById('tableModal'));
                    modal.show();
                } else {
                    alert('Error loading table data: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading table data');
            });
    }

    function saveTable() {
        const form = document.getElementById('tableForm');
        const formData = new FormData(form);
        const tableId = document.getElementById('tableId').value;

        const url = tableId ? `${window.SITE_URL || ''}/superadmin/tables/update/${tableId}` : `${window.SITE_URL || ''}/superadmin/tables/create`;

        fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())            .then(data => {
                if (data.success) {
                    closeModal();
                    location.reload();
                } else {
                    alert('Error saving table: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving table');
            });
    }

    function deleteTable(tableId) {
        if (confirm('Are you sure you want to delete this table? This action cannot be undone.')) {
            fetch(`${window.SITE_URL || ''}/superadmin/tables/delete/${tableId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting table: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting table');
                });
        }
    }        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, Bootstrap available:', typeof bootstrap !== 'undefined');

        // Verify modal elements exist
        const modalElement = document.getElementById('tableModal');
        if (modalElement) {
            console.log('Modal element found');
        } else {
            console.error('Modal element not found');
        }

        const addTableBtn = document.querySelector('button[onclick="showAddTableModal()"]');
        if (addTableBtn) {
            console.log('Add table button found');
        } else {
            console.error('Add table button not found');
        }
    });
</script>

<style>
    /* Ensure modal displays properly with high z-index */
    .modal {
        z-index: 9999 !important;
    }

    .modal-backdrop {
        z-index: 9998 !important;
    }

    /* Override any SuperAdmin layout interference */
    .modal.fade.show {
        display: block !important;
        opacity: 1 !important;
    }

    /* Ensure modal is visible and clickable */
    .modal.show {
        display: block !important;
        visibility: visible !important;
    }

    /* Force modal to be on top of everything */
    .modal-dialog {
        margin: 1.75rem auto;
        position: relative;
        z-index: 10000 !important;
    }

    .modal-content {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 10001 !important;
    }

    .modal-header {
        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
        color: white;
        border-bottom: none;
        border-radius: 15px 15px 0 0;
    }

    .modal-footer {
        border-top: 1px solid #e9ecef;
        border-radius: 0 0 15px 15px;
    }

    /* Button styling */
    .btn-close {
        filter: brightness(0) invert(1);
    }

    /* Ensure modal backdrop is properly positioned */
    .modal-backdrop.fade.show {
        opacity: 0.5 !important;
    }

    /* Override any main-content z-index issues */
    .main-content {
        position: relative;
        z-index: 1;
    }

    /* Custom styles for table cards */
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.bg-gradient-light {
    background: linear-gradient(135deg, #f8f9fc 0%, #f1f3f6 100%);
}

/* Table grid responsive adjustments */
@media (max-width: 768px) {
    .card .btn-group {
        flex-direction: column;
    }

    .card .btn-group .btn {
        margin-bottom: 0.25rem;
    }
}
</style>

<?php
function getTableStatusBadgeColor($status)
{
    switch ($status) {
        case 'available':
            return 'success';
        case 'occupied':
            return 'warning';
        case 'maintenance':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>

<?php require_once 'views/layouts/superadmin_footer.php'; ?>
