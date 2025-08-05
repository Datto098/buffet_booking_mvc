<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'views/layouts/superadmin_header.php'; ?>
    <title>Booking Management - Super Admin</title>
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
                            <i class="fas fa-calendar-check me-2 text-info"></i>Booking Management
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/superadmin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Bookings</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-outline-primary" onclick="refreshBookings()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh Data
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

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Bookings</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['total_bookings'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Pending</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['pending_bookings'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-clock fa-2x opacity-75"></i>
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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Confirmed</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['confirmed_bookings'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Tables Used</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['tables_used'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-chair fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                </div>

                <!-- Filter Bar -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-filter me-2 text-info"></i>Filters
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?= SITE_URL ?>/superadmin/bookings" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Search Bookings</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" class="form-control" name="search" placeholder="Customer name, phone..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">All Status</option>
                                    <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">From Date</label>
                                <input type="date" class="form-control" name="date_from" value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">To Date</label>
                                <input type="date" class="form-control" name="date_to" value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="<?= SITE_URL ?>/superadmin/bookings" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-bold">
                                <i class="fas fa-calendar-check me-2 text-info"></i>All Bookings
                            </h6>
                            <div class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>
                                <?php echo count($bookings ?? []); ?> bookings displayed
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 fw-bold">Booking ID</th>
                                        <th class="border-0 fw-bold">Customer</th>
                                        <th class="border-0 fw-bold">Table</th>
                                        <th class="border-0 fw-bold">Date & Time</th>
                                        <th class="border-0 fw-bold">Guests</th>
                                        <th class="border-0 fw-bold">Status</th>
                                        <th class="border-0 fw-bold">Special Requests</th>
                                        <th class="border-0 fw-bold text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?php if (!empty($bookings)): ?>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <strong>#<?php echo $booking['id']; ?></strong>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-bold"><?php echo htmlspecialchars($booking['customer_name']); ?></div>
                                                <div class="text-muted small"><?php echo htmlspecialchars($booking['phone_number'] ?? ''); ?></div>
                                                <div class="text-muted small"><?php echo htmlspecialchars($booking['customer_email'] ?? ''); ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($booking['table_name'])): ?>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($booking['table_name']); ?></span>
                                                <div class="small text-muted">Capacity: <?php echo $booking['table_capacity'] ?? 'N/A'; ?></div>
                                            <?php else: ?>
                                                <span class="text-muted">No table assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo !empty($booking['booking_date']) ? date('M d, Y', strtotime($booking['booking_date'])) : 'Invalid Date'; ?></div>
                                            <div class="text-muted"><?php echo !empty($booking['booking_time']) ? date('H:i', strtotime($booking['booking_time'])) : 'Invalid Time'; ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo $booking['number_of_guests']; ?> guests</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo getBookingStatusColor($booking['status']); ?>">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($booking['special_requests'])): ?>
                                                <div class="text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($booking['special_requests']); ?>">
                                                    <?php echo htmlspecialchars($booking['special_requests']); ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">None</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-info"
                                                    onclick="viewBookingDetails(<?php echo $booking['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'confirmed')">Confirm Booking</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'completed')">Mark Completed</a></li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'cancelled')">Cancel Booking</a></li>
                                                    </ul>
                                                </div>
                                                <!-- Assign Table button - Hidden as requested -->
                                                <?php /*
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                    onclick="assignTable(<?php echo $booking['id']; ?>)">
                                                    <i class="fas fa-chair"></i>
                                                </button>
                                                */ ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No bookings found</h5>
                                        <p class="text-muted">Try adjusting your filters or check back later.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                    <nav aria-label="Booking pagination">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>&<?php echo http_build_query($_GET); ?>">Previous</a>
                            </li>

                            <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                                <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query($_GET); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?php echo $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>&<?php echo http_build_query($_GET); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
        </main>
    </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-labelledby="bookingDetailsModalLabel" aria-hidden="true" style="z-index: 2000">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingDetailsModalLabel">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="bookingDetailsContent">
                <div class="text-center">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Table Modal -->
<div class="modal fade" id="assignTableModal" tabindex="-1" aria-labelledby="assignTableModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignTableModalLabel">Assign Table</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignTableForm">
                    <input type="hidden" id="bookingId" name="booking_id">
                    <div class="mb-3">
                        <label for="tableSelect" class="form-label">Available Tables</label>
                        <select class="form-select" id="tableSelect" name="table_id" required>
                            <option value="">Select a table...</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="saveTableAssignment()">Assign Table</button>
            </div>
        </div>
    </div>
</div>




<script>
     window.SITE_URL = '<?= SITE_URL ?>';
     window.csrfToken = '<?= $csrf_token ?? '' ?>';
    function refreshBookings() {
        location.reload();
    }

    function viewBookingDetails(bookingId) {
        const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
        const content = document.getElementById('bookingDetailsContent');

        // Show loading
        content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-danger" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

        modal.show(); // Fetch booking details
        fetch(`${window.SITE_URL || ''}/superadmin/bookings/details/${bookingId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    content.innerHTML = data.html;
                } else {
                    content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        Error loading booking details: ${data.message || 'Unknown error'}
                    </div>
                `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Error loading booking details. Please try again.
                </div>
            `;
            });
    }

    function updateBookingStatus(bookingId, status) {
        if (confirm(`Are you sure you want to update this booking status to "${status}"?`)) {
            fetch(`${window.SITE_URL || ''}/superadmin/bookings/updateStatus/${bookingId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': window.csrfToken || ''
                    },
                    body: JSON.stringify({
                        status: status,
                        csrf_token: window.csrfToken || ''
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error updating booking status: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating booking status: ' + error.message);
                });
        }
    }

    function assignTable(bookingId) { // Load available tables
        fetch(`${window.SITE_URL || ''}/superadmin/tables/available/${bookingId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tableSelect = document.getElementById('tableSelect');
                    tableSelect.innerHTML = '<option value="">Select a table...</option>';

                    data.tables.forEach(table => {
                        const option = document.createElement('option');
                        option.value = table.id;
                        option.textContent = `${table.name} (Capacity: ${table.capacity})`;
                        tableSelect.appendChild(option);
                    });

                    document.getElementById('bookingId').value = bookingId;

                    const modal = new bootstrap.Modal(document.getElementById('assignTableModal'));
                    modal.show();
                } else {
                    alert('Error loading available tables: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading available tables');
            });
    }

    function saveTableAssignment() {
        const bookingId = document.getElementById('bookingId').value;
        const tableId = document.getElementById('tableSelect').value;

        if (!tableId) {
            alert('Please select a table');
            return;
        }
        fetch(`${window.SITE_URL || ''}/superadmin/bookings/assignTable/${bookingId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    table_id: tableId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('assignTableModal'));
                    modal.hide();
                    location.reload();
                } else {
                    alert('Error assigning table: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error assigning table');
            });
    }
</script>

<?php
// Helper functions for booking management
function getBookingStatusIcon($status) {
    switch($status) {
        case 'pending': return 'clock';
        case 'confirmed': return 'check-circle';
        case 'completed': return 'check-double';
        case 'cancelled': return 'times-circle';
        default: return 'question-circle';
    }
}

function getBookingStatusColor($status) {
    switch($status) {
        case 'pending': return 'warning';
        case 'confirmed': return 'info';
        case 'completed': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}
?>

<?php require_once 'views/layouts/superadmin_footer.php'; ?>
