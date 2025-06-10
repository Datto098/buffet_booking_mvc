<?php require_once 'views/layouts/superadmin_header.php'; ?>
<?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

<div class="main-content fade-in">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>
                    <i class="fas fa-calendar-check"></i>
                    Booking Management
                </h1>
                <div class="btn-toolbar">
                    <button type="button" class="btn btn-outline-primary" onclick="refreshBookings()">
                        <i class="fas fa-sync-alt"></i> Refresh Data
                    </button>
                </div>
            </div>
        </div>

        <!-- Booking Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-primary me-3">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">Total Bookings</div>
                                <div class="h4 mb-0 fw-bold text-dark"><?php echo $stats['total_bookings'] ?? 0; ?></div>
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
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">Pending</div>
                                <div class="h4 mb-0 fw-bold text-dark"><?php echo $stats['pending_bookings'] ?? 0; ?></div>
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
                                <div class="text-muted text-uppercase small fw-bold mb-1">Confirmed</div>
                                <div class="h4 mb-0 fw-bold text-dark"><?php echo $stats['confirmed_bookings'] ?? 0; ?></div>
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
                                <i class="fas fa-chair"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">Tables Used</div>
                                <div class="h4 mb-0 fw-bold text-dark"><?php echo $stats['tables_used'] ?? 0; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from"
                            value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to"
                            value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                            placeholder="Customer name, phone..."
                            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-search"></i> Filter </button>
                        <a href="<?= SITE_URL ?>/superadmin/bookings" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-danger">
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Table</th>
                                <th>Date & Time</th>
                                <th>Guests</th>
                                <th>Status</th>
                                <th>Special Requests</th>
                                <th>Actions</th>
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
                                            <span class="badge bg-<?php echo getBookingStatusBadgeColor($booking['status']); ?>">
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
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                    onclick="assignTable(<?php echo $booking['id']; ?>)">
                                                    <i class="fas fa-chair"></i>
                                                </button>
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
</script>

<script>
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
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error updating booking status: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating booking status');
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
function getBookingStatusBadgeColor($status)
{
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'confirmed':
            return 'success';
        case 'completed':
            return 'info';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>



<?php require_once 'views/layouts/superadmin_footer.php'; ?>
