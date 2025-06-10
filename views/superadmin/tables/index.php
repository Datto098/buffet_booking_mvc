<?php require_once 'views/layouts/superadmin_header.php'; ?>
<?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

<div class="main-content fade-in">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>
                    <i class="fas fa-chair"></i>
                    Table Management
                </h1>
                <div class="btn-toolbar">
                    <button type="button" class="btn btn-primary" onclick="showAddTableModal()">
                        <i class="fas fa-plus"></i> Add New Table
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-primary me-3">
                                <i class="fas fa-chair"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">Total Tables</div>
                                <div class="h4 mb-0 fw-bold text-dark"><?php echo $stats['total_tables'] ?? 0; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-success me-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">Available</div>
                                <div class="h4 mb-0 fw-bold text-dark"><?php echo $stats['available_tables'] ?? 0; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-warning me-3">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">Occupied</div>
                                <div class="h4 mb-0 fw-bold text-dark"><?php echo $stats['occupied_tables'] ?? 0; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-info me-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">Total Capacity</div>
                                <div class="h4 mb-0 fw-bold text-dark"><?php echo $stats['total_capacity'] ?? 0; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- Tables Grid -->
        <div class="row">
            <?php if (!empty($tables)): ?>
                <?php foreach ($tables as $table): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 <?php echo ($table['is_available'] ?? 1) ? 'border-success' : 'border-danger'; ?>">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($table['table_number'] ?? 'Unknown Table'); ?></h5>
                                <span class="badge bg-<?php echo ($table['is_available'] ?? 1) ? 'success' : 'danger'; ?>">
                                    <?php echo ($table['is_available'] ?? 1) ? 'Available' : 'Unavailable'; ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <small class="text-muted">Capacity</small>
                                        <div class="fw-bold">
                                            <i class="fas fa-users"></i> <?php echo htmlspecialchars($table['capacity'] ?? 'N/A'); ?> guests
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($table['description'])): ?>
                                    <div class="mb-3">
                                        <small class="text-muted">Description</small>
                                        <div class="small"><?php echo htmlspecialchars($table['description']); ?></div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($table['location'])): ?>
                                    <div class="mb-3">
                                        <small class="text-muted">Location</small>
                                        <div class="small"><?php echo htmlspecialchars($table['location']); ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="editTable(<?php echo $table['id']; ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                        onclick="deleteTable(<?php echo $table['id']; ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-chair fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No tables found</h5>
                            <p class="text-muted">Add your first table to get started.</p>
                            <button type="button" class="btn btn-danger" onclick="showAddTableModal()">
                                <i class="fas fa-plus"></i> Add First Table
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
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
