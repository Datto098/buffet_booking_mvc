<?php include '../views/admin/layouts/header.php'; ?>

<div class="admin-wrapper">
    <?php include '../views/admin/layouts/sidebar.php'; ?>

    <div class="admin-content">
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">Booking Management</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item active">Bookings</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="exportBookings()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="stat-number text-primary"><?php echo $stats['total_bookings'] ?? 0; ?></h3>
                                    <p class="stat-label">Total Bookings</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="stat-number text-success"><?php echo $stats['confirmed_bookings'] ?? 0; ?></h3>
                                    <p class="stat-label">Confirmed</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-check-circle text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="stat-number text-warning"><?php echo $stats['pending_bookings'] ?? 0; ?></h3>
                                    <p class="stat-label">Pending</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-clock text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="stat-number text-info"><?php echo $stats['today_bookings'] ?? 0; ?></h3>
                                    <p class="stat-label">Today</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-day text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Bookings</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($bookings)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Customer</th>
                                        <th>Date & Time</th>
                                        <th>Guests</th>
                                        <th>Table</th>
                                        <th>Status</th>
                                        <th>Special Requests</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td>
                                                <strong>#<?php echo htmlspecialchars($booking['id']); ?></strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <i class="fas fa-user-circle fa-2x text-muted"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium"><?php echo htmlspecialchars($booking['customer_name']); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars($booking['customer_email']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-medium"><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></div>
                                                    <small class="text-muted"><?php echo date('g:i A', strtotime($booking['booking_time'])); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <i class="fas fa-users"></i> <?php echo htmlspecialchars($booking['number_of_guests']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($booking['table_number'])): ?>
                                                    <span class="badge bg-info">Table <?php echo htmlspecialchars($booking['table_number']); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">Not assigned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = 'secondary';
                                                $statusIcon = 'circle';
                                                switch ($booking['status']) {
                                                    case 'confirmed':
                                                        $statusClass = 'success';
                                                        $statusIcon = 'check-circle';
                                                        break;
                                                    case 'pending':
                                                        $statusClass = 'warning';
                                                        $statusIcon = 'clock';
                                                        break;
                                                    case 'cancelled':
                                                        $statusClass = 'danger';
                                                        $statusIcon = 'times-circle';
                                                        break;
                                                    case 'completed':
                                                        $statusClass = 'primary';
                                                        $statusIcon = 'star';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge bg-<?php echo $statusClass; ?>">
                                                    <i class="fas fa-<?php echo $statusIcon; ?>"></i>
                                                    <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($booking['special_requests'])): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                            data-bs-toggle="tooltip"
                                                            title="<?php echo htmlspecialchars($booking['special_requests']); ?>">
                                                        <i class="fas fa-comment"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted">None</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                                            type="button" data-bs-toggle="dropdown">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <button class="dropdown-item" onclick="viewBookingDetails(<?php echo $booking['id']; ?>)">
                                                                <i class="fas fa-eye"></i> View Details
                                                            </button>
                                                        </li>
                                                        <?php if ($booking['status'] == 'pending'): ?>
                                                            <li>
                                                                <button class="dropdown-item text-success" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'confirmed')">
                                                                    <i class="fas fa-check"></i> Confirm
                                                                </button>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if (in_array($booking['status'], ['pending', 'confirmed'])): ?>
                                                            <li>
                                                                <button class="dropdown-item" onclick="assignTable(<?php echo $booking['id']; ?>)">
                                                                    <i class="fas fa-table"></i> Assign Table
                                                                </button>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <button class="dropdown-item text-danger" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'cancelled')">
                                                                    <i class="fas fa-times"></i> Cancel
                                                                </button>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if ($booking['status'] == 'confirmed'): ?>
                                                            <li>
                                                                <button class="dropdown-item text-primary" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'completed')">
                                                                    <i class="fas fa-star"></i> Mark Complete
                                                                </button>
                                                            </li>
                                                        <?php endif; ?>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button class="dropdown-item" onclick="sendConfirmationEmail(<?php echo $booking['id']; ?>)">
                                                                <i class="fas fa-envelope"></i> Send Email
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Bookings pagination" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($currentPage > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo ($currentPage - 1); ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($currentPage < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo ($currentPage + 1); ?>">Next</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Bookings Found</h5>
                            <p class="text-muted">No bookings match your current filters.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bookingDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Table Assignment Modal -->
<div class="modal fade" id="tableAssignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Table</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="tableAssignForm">
                    <input type="hidden" id="bookingIdForTable" name="booking_id">
                    <div class="mb-3">
                        <label for="tableNumber" class="form-label">Table Number</label>
                        <select class="form-select" id="tableNumber" name="table_number" required>
                            <option value="">Select Table</option>
                            <!-- Table options will be loaded via AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tableNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="tableNotes" name="notes" rows="3" placeholder="Any special notes about table assignment..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveTableAssignment()">Assign Table</button>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Bookings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm" method="GET">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="filterStatus" class="form-label">Status</label>
                                <select class="form-select" id="filterStatus" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="filterGuests" class="form-label">Number of Guests</label>
                                <select class="form-select" id="filterGuests" name="guests">
                                    <option value="">Any Size</option>
                                    <option value="1-2" <?php echo (isset($_GET['guests']) && $_GET['guests'] == '1-2') ? 'selected' : ''; ?>>1-2 Guests</option>
                                    <option value="3-4" <?php echo (isset($_GET['guests']) && $_GET['guests'] == '3-4') ? 'selected' : ''; ?>>3-4 Guests</option>
                                    <option value="5-8" <?php echo (isset($_GET['guests']) && $_GET['guests'] == '5-8') ? 'selected' : ''; ?>>5-8 Guests</option>
                                    <option value="9+" <?php echo (isset($_GET['guests']) && $_GET['guests'] == '9+') ? 'selected' : ''; ?>>9+ Guests</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="filterDateFrom" class="form-label">Date From</label>
                                <input type="date" class="form-control" id="filterDateFrom" name="date_from" value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="filterDateTo" class="form-label">Date To</label>
                                <input type="date" class="form-control" id="filterDateTo" name="date_to" value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear Filters</button>
                <button type="button" class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
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

function viewBookingDetails(bookingId) {
    const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
    const content = document.getElementById('bookingDetailsContent');

    content.innerHTML = '<div class="text-center py-3"><div class="spinner-border" role="status"></div></div>';
    modal.show();

    fetch(`/admin/bookings/details/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                content.innerHTML = data.html;
            } else {
                content.innerHTML = '<div class="alert alert-danger">Failed to load booking details.</div>';
            }
        })
        .catch(error => {
            content.innerHTML = '<div class="alert alert-danger">Error loading booking details.</div>';
        });
}

function updateBookingStatus(bookingId, status) {
    if (!confirm(`Are you sure you want to ${status} this booking?`)) {
        return;
    }

    fetch('/admin/bookings/update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            booking_id: bookingId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Booking status updated successfully', 'success');
            location.reload();
        } else {
            showNotification(data.message || 'Failed to update booking status', 'error');
        }
    })
    .catch(error => {
        showNotification('Error updating booking status', 'error');
    });
}

function assignTable(bookingId) {
    document.getElementById('bookingIdForTable').value = bookingId;

    // Load available tables
    fetch(`/admin/bookings/available-tables/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('tableNumber');
            select.innerHTML = '<option value="">Select Table</option>';

            if (data.success && data.tables) {
                data.tables.forEach(table => {
                    select.innerHTML += `<option value="${table.number}">Table ${table.number} (${table.capacity} seats)</option>`;
                });
            }
        });

    const modal = new bootstrap.Modal(document.getElementById('tableAssignModal'));
    modal.show();
}

function saveTableAssignment() {
    const form = document.getElementById('tableAssignForm');
    const formData = new FormData(form);

    fetch('/admin/bookings/assign-table', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Table assigned successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('tableAssignModal')).hide();
            location.reload();
        } else {
            showNotification(data.message || 'Failed to assign table', 'error');
        }
    })
    .catch(error => {
        showNotification('Error assigning table', 'error');
    });
}

function sendConfirmationEmail(bookingId) {
    fetch('/admin/bookings/send-confirmation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ booking_id: bookingId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Confirmation email sent successfully', 'success');
        } else {
            showNotification(data.message || 'Failed to send email', 'error');
        }
    })
    .catch(error => {
        showNotification('Error sending email', 'error');
    });
}

function applyFilters() {
    document.getElementById('filterForm').submit();
}

function clearFilters() {
    window.location.href = '/admin/bookings';
}

function exportBookings() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = `/admin/bookings?${params.toString()}`;
}
</script>

<?php include '../views/admin/layouts/footer.php'; ?>
