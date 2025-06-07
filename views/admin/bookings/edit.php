<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token ?? ''; ?>">
    <title>Edit Booking - Admin</title>
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
                            <i class="fas fa-calendar-edit"></i> Edit Booking #<?= htmlspecialchars($booking['id'] ?? '') ?>
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/bookings">Bookings</a></li>
                                <li class="breadcrumb-item active">Edit Booking</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="<?= SITE_URL ?>/admin/bookings" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Bookings
                            </a>
                        </div>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Cancel Booking
                        </button>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?= $_SESSION['flash_type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                        <?= htmlspecialchars($_SESSION['flash_message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php
                    unset($_SESSION['flash_message']);
                    unset($_SESSION['flash_type']);
                    ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <!-- Booking Edit Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-calendar-alt"></i> Booking Information
                                </h5>
                                <span class="badge bg-<?=
                                    ($booking['status'] ?? '') == 'confirmed' ? 'success' :
                                    (($booking['status'] ?? '') == 'pending' ? 'warning' :
                                    (($booking['status'] ?? '') == 'cancelled' ? 'danger' : 'secondary'))
                                ?>">
                                    <?= ucfirst($booking['status'] ?? 'pending') ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <form action="<?= SITE_URL ?>/admin/bookings/update/<?= $booking['id'] ?>" method="POST" id="editBookingForm">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" class="form-control" id="customer_name" name="customer_name"
                                                       value="<?= htmlspecialchars($booking['customer_name'] ?? '') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_email" class="form-label">Customer Email <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                <input type="email" class="form-control" id="customer_email" name="customer_email"
                                                       value="<?= htmlspecialchars($booking['customer_email'] ?? '') ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_phone" class="form-label">Phone Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                <input type="tel" class="form-control" id="customer_phone" name="customer_phone"
                                                       value="<?= htmlspecialchars($booking['customer_phone'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="party_size" class="form-label">Party Size <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-users"></i></span>
                                                <input type="number" class="form-control" id="party_size" name="party_size"
                                                       value="<?= htmlspecialchars($booking['party_size'] ?? '') ?>"
                                                       min="1" max="20" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="booking_date" class="form-label">Booking Date <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                <input type="date" class="form-control" id="booking_date" name="booking_date"
                                                       value="<?= !empty($booking['booking_date']) ? date('Y-m-d', strtotime($booking['booking_date'])) : '' ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="booking_time" class="form-label">Booking Time <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                                <input type="time" class="form-control" id="booking_time" name="booking_time"
                                                       value="<?= !empty($booking['booking_time']) ? date('H:i', strtotime($booking['booking_time'])) : '' ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="table_id" class="form-label">Table Assignment</label>
                                            <select class="form-select" id="table_id" name="table_id">
                                                <option value="">Select Table</option>
                                                <?php if (!empty($tables)): ?>
                                                    <?php foreach ($tables as $table): ?>
                                                        <option value="<?= $table['id'] ?>"
                                                                <?= ($booking['table_id'] ?? '') == $table['id'] ? 'selected' : '' ?>>
                                                            Table <?= htmlspecialchars($table['table_number']) ?> (<?= $table['capacity'] ?> seats)
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Booking Status <span class="text-danger">*</span></label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="pending" <?= ($booking['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="confirmed" <?= ($booking['status'] ?? '') == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                                <option value="seated" <?= ($booking['status'] ?? '') == 'seated' ? 'selected' : '' ?>>Seated</option>
                                                <option value="completed" <?= ($booking['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Completed</option>
                                                <option value="no_show" <?= ($booking['status'] ?? '') == 'no_show' ? 'selected' : '' ?>>No Show</option>
                                                <option value="cancelled" <?= ($booking['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="duration" class="form-label">Duration (hours)</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-hourglass"></i></span>
                                                <input type="number" class="form-control" id="duration" name="duration"
                                                       value="<?= htmlspecialchars($booking['duration'] ?? '2') ?>"
                                                       min="0.5" max="8" step="0.5">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="total_amount" class="form-label">Total Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                                <input type="number" class="form-control" id="total_amount" name="total_amount"
                                                       value="<?= htmlspecialchars($booking['total_amount'] ?? '') ?>"
                                                       step="0.01" min="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="special_requests" class="form-label">Special Requests</label>
                                        <textarea class="form-control" id="special_requests" name="special_requests" rows="3"
                                                  placeholder="Any special requests or notes..."><?= htmlspecialchars($booking['special_requests'] ?? '') ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="admin_notes" class="form-label">Admin Notes</label>
                                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"
                                                  placeholder="Internal notes for staff..."><?= htmlspecialchars($booking['admin_notes'] ?? '') ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_vip" name="is_vip" value="1"
                                                       <?= ($booking['is_vip'] ?? 0) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="is_vip">
                                                    VIP Customer
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="requires_confirmation" name="requires_confirmation" value="1"
                                                       <?= ($booking['requires_confirmation'] ?? 0) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="requires_confirmation">
                                                    Requires Confirmation Call
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                        <div>
                                            <button type="button" class="btn btn-outline-info me-2" onclick="sendConfirmation()">
                                                <i class="fas fa-envelope"></i> Send Confirmation
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Update Booking
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Booking Timeline -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-history"></i> Booking Timeline
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">Booking Created</h6>
                                            <p class="timeline-description"><?= date('M d, Y g:i A', strtotime($booking['created_at'] ?? 'now')) ?></p>
                                        </div>
                                    </div>
                                    <?php if (!empty($booking['confirmed_at'])): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-success"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Confirmed</h6>
                                                <p class="timeline-description"><?= date('M d, Y g:i A', strtotime($booking['confirmed_at'])) ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($booking['seated_at'])): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-info"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Seated</h6>
                                                <p class="timeline-description"><?= date('M d, Y g:i A', strtotime($booking['seated_at'])) ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-user"></i> Customer Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Total Bookings:</small>
                                    <small><?= $booking['customer_total_bookings'] ?? '0' ?></small>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Last Visit:</small>
                                    <small><?= !empty($booking['customer_last_visit']) ? date('M d, Y', strtotime($booking['customer_last_visit'])) : 'First time' ?></small>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Customer Since:</small>
                                    <small><?= !empty($booking['customer_since']) ? date('M Y', strtotime($booking['customer_since'])) : 'New customer' ?></small>
                                </div>
                                <?php if (!empty($booking['customer_preferences'])): ?>
                                    <hr>
                                    <small class="text-muted">Preferences:</small>
                                    <p class="small mt-1"><?= htmlspecialchars($booking['customer_preferences']) ?></p>
                                <?php endif; ?>
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
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="confirmBooking(<?= $booking['id'] ?>)">
                                        <i class="fas fa-check"></i> Confirm Booking
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="seatCustomer(<?= $booking['id'] ?>)">
                                        <i class="fas fa-chair"></i> Mark as Seated
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="printReservation(<?= $booking['id'] ?>)">
                                        <i class="fas fa-print"></i> Print Reservation
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="rescheduleBooking(<?= $booking['id'] ?>)">
                                        <i class="fas fa-calendar-alt"></i> Reschedule
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="markNoShow(<?= $booking['id'] ?>)">
                                        <i class="fas fa-user-times"></i> Mark No Show
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Cancel Booking Modal -->
    <div class="modal fade" id="cancelBookingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Cancel Booking
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this booking?</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Cancellation Reason</label>
                        <textarea class="form-control" id="cancellation_reason" rows="3"
                                  placeholder="Please provide a reason for cancellation..."></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notify_customer">
                        <label class="form-check-label" for="notify_customer">
                            Send cancellation notification to customer
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Booking</button>
                    <form action="<?= SITE_URL ?>/admin/bookings/cancel/<?= $booking['id'] ?>" method="POST" class="d-inline" id="cancelForm">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="cancellation_reason" id="hidden_reason">
                        <input type="hidden" name="notify_customer" id="hidden_notify">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times"></i> Cancel Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'views/admin/layouts/footer.php'; ?>

    <script>
        // Form validation
        document.getElementById('editBookingForm').addEventListener('submit', function(e) {
            const customerName = document.getElementById('customer_name').value.trim();
            const customerEmail = document.getElementById('customer_email').value.trim();
            const partySize = document.getElementById('party_size').value;
            const bookingDate = document.getElementById('booking_date').value;
            const bookingTime = document.getElementById('booking_time').value;

            if (!customerName || !customerEmail || !partySize || !bookingDate || !bookingTime) {
                e.preventDefault();
                alert('Please fill in all required fields!');
                return false;
            }

            if (partySize < 1) {
                e.preventDefault();
                alert('Party size must be at least 1!');
                return false;
            }
        });

        // Delete confirmation
        function confirmDelete() {
            new bootstrap.Modal(document.getElementById('cancelBookingModal')).show();
        }

        // Handle cancellation form
        document.getElementById('cancelForm').addEventListener('submit', function(e) {
            const reason = document.getElementById('cancellation_reason').value;
            const notify = document.getElementById('notify_customer').checked;

            document.getElementById('hidden_reason').value = reason;
            document.getElementById('hidden_notify').value = notify ? '1' : '0';
        });

        // Quick action functions
        function confirmBooking(bookingId) {
            if (confirm('Confirm this booking?')) {
                updateBookingStatus(bookingId, 'confirmed');
            }
        }

        function seatCustomer(bookingId) {
            if (confirm('Mark customer as seated?')) {
                updateBookingStatus(bookingId, 'seated');
            }
        }

        function markNoShow(bookingId) {
            if (confirm('Mark this booking as no show?')) {
                updateBookingStatus(bookingId, 'no_show');
            }
        }

        function updateBookingStatus(bookingId, status) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `<?= SITE_URL ?>/admin/bookings/${bookingId}/update-status`;

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = 'csrf_token';
            csrfToken.value = '<?= $_SESSION['csrf_token'] ?? '' ?>';

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;

            form.appendChild(csrfToken);
            form.appendChild(statusInput);
            document.body.appendChild(form);
            form.submit();
        }

        function sendConfirmation() {
            if (confirm('Send confirmation email to customer?')) {
                fetch(`<?= SITE_URL ?>/admin/bookings/<?= $booking['id'] ?>/send-confirmation`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?= $_SESSION['csrf_token'] ?? '' ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Confirmation email sent successfully!');
                    } else {
                        alert('Failed to send confirmation email.');
                    }
                })
                .catch(error => {
                    alert('Error sending confirmation email.');
                });
            }
        }

        function printReservation(bookingId) {
            window.open(`<?= SITE_URL ?>/admin/bookings/${bookingId}/print`, '_blank');
        }

        function rescheduleBooking(bookingId) {
            alert('Reschedule feature coming soon!');
        }

        // Auto-calculate total amount based on duration and table
        function calculateTotal() {
            const duration = parseFloat(document.getElementById('duration').value) || 0;
            const tableSelect = document.getElementById('table_id');
            const selectedTable = tableSelect.options[tableSelect.selectedIndex];

            if (selectedTable && selectedTable.dataset.pricePerHour) {
                const pricePerHour = parseFloat(selectedTable.dataset.pricePerHour) || 0;
                const total = duration * pricePerHour;
                document.getElementById('total_amount').value = total.toFixed(2);
            }
        }

        document.getElementById('duration').addEventListener('input', calculateTotal);
        document.getElementById('table_id').addEventListener('change', calculateTotal);
    </script>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -23px;
            top: 5px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }

        .timeline-title {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .timeline-description {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 0;
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
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="<?= SITE_URL ?>/admin/bookings" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Bookings
                        </a>
                    </div>
                </div>

                <!-- Booking Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Booking Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="bookingForm" action="<?= SITE_URL ?>/admin/bookings/update" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['id']) ?>">

                                    <!-- Customer Information -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h6 class="text-primary mb-3">
                                                <i class="fas fa-user me-2"></i>Customer Information
                                            </h6>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="customer_name" class="form-label">
                                                Customer Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="customer_name"
                                                   name="customer_name"
                                                   required
                                                   maxlength="100"
                                                   value="<?= htmlspecialchars($booking['customer_name'] ?? '') ?>">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="phone_number" class="form-label">
                                                Phone Number <span class="text-danger">*</span>
                                            </label>
                                            <input type="tel"
                                                   class="form-control"
                                                   id="phone_number"
                                                   name="phone_number"
                                                   required
                                                   pattern="[0-9\-\+\(\)\s]+"
                                                   value="<?= htmlspecialchars($booking['phone_number'] ?? '') ?>">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="customer_email" class="form-label">Email (Optional)</label>
                                            <input type="email"
                                                   class="form-control"
                                                   id="customer_email"
                                                   name="customer_email"
                                                   value="<?= htmlspecialchars($booking['email'] ?? '') ?>">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="number_of_guests" class="form-label">
                                                Number of Guests <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="number_of_guests" name="number_of_guests" required>
                                                <option value="">Select party size</option>
                                                <?php for ($i = 1; $i <= 20; $i++): ?>
                                                    <option value="<?= $i ?>" <?= ($booking['number_of_guests'] ?? '') == $i ? 'selected' : '' ?>>
                                                        <?= $i ?> <?= $i == 1 ? 'Guest' : 'Guests' ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Reservation Details -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h6 class="text-primary mb-3">
                                                <i class="fas fa-calendar-alt me-2"></i>Reservation Details
                                            </h6>
                                        </div>

                                        <?php
                                        // Extract date and time from reservation_time
                                        $reservationDateTime = new DateTime($booking['reservation_time']);
                                        $reservationDate = $reservationDateTime->format('Y-m-d');
                                        $reservationTime = $reservationDateTime->format('H:i');
                                        ?>

                                        <div class="col-md-6 mb-3">
                                            <label for="reservation_date" class="form-label">
                                                Reservation Date <span class="text-danger">*</span>
                                            </label>
                                            <input type="date"
                                                   class="form-control"
                                                   id="reservation_date"
                                                   name="reservation_date"
                                                   required
                                                   value="<?= htmlspecialchars($reservationDate) ?>">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="reservation_time" class="form-label">
                                                Reservation Time <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="reservation_time" name="reservation_time" required>
                                                <option value="">Select time</option>
                                                <?php
                                                $start_hour = 10; // 10 AM
                                                $end_hour = 22;   // 10 PM
                                                for ($hour = $start_hour; $hour <= $end_hour; $hour++) {
                                                    for ($minute = 0; $minute < 60; $minute += 30) {
                                                        $time_24 = sprintf('%02d:%02d', $hour, $minute);
                                                        $time_12 = date('g:i A', strtotime($time_24));
                                                        $selected = $reservationTime == $time_24 ? 'selected' : '';
                                                        echo "<option value=\"{$time_24}\" {$selected}>{$time_12}</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-12 mb-3">
                                            <label for="special_requests" class="form-label">Special Requests (Optional)</label>
                                            <textarea class="form-control"
                                                      id="special_requests"
                                                      name="special_requests"
                                                      rows="3"
                                                      maxlength="500"
                                                      placeholder="Any special dietary requirements, seating preferences, or other requests..."><?= htmlspecialchars($booking['special_requests'] ?? '') ?></textarea>
                                            <div class="form-text">Maximum 500 characters</div>
                                        </div>
                                    </div>

                                    <!-- Form Actions -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between">
                                                <a href="<?= SITE_URL ?>/admin/bookings" class="btn btn-secondary">
                                                    <i class="fas fa-times"></i> Cancel
                                                </a>
                                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                                    <i class="fas fa-save"></i> Update Booking
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Status Card -->
                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Booking Status
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Current Status:</strong>
                                    <span class="badge bg-<?=
                                        $booking['status'] === 'confirmed' ? 'success' :
                                        ($booking['status'] === 'pending' ? 'warning' :
                                        ($booking['status'] === 'cancelled' ? 'danger' : 'secondary'))
                                    ?> ms-2">
                                        <?= ucfirst($booking['status']) ?>
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <strong>Created:</strong><br>
                                    <small class="text-muted">
                                        <?= date('M d, Y g:i A', strtotime($booking['created_at'])) ?>
                                    </small>
                                </div>

                                <?php if (!empty($booking['table_number'])): ?>
                                <div class="mb-3">
                                    <strong>Assigned Table:</strong><br>
                                    <span class="badge bg-info">Table <?= htmlspecialchars($booking['table_number']) ?></span>
                                </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <strong>Booking ID:</strong><br>
                                    <code>#<?= htmlspecialchars($booking['id']) ?></code>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <small>To change the booking status or assign a table, please use the booking management page.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card shadow">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bolt me-2"></i>Quick Actions
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="<?= SITE_URL ?>/admin/bookings/details/<?= $booking['id'] ?>"
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    <a href="<?= SITE_URL ?>/admin/bookings"
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-list"></i> All Bookings
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('bookingForm');
            const submitBtn = document.getElementById('submitBtn');

            // Form validation
            form.addEventListener('submit', function(e) {
                const customerName = document.getElementById('customer_name').value.trim();
                const phoneNumber = document.getElementById('phone_number').value.trim();
                const numberOfGuests = document.getElementById('number_of_guests').value;
                const reservationDate = document.getElementById('reservation_date').value;
                const reservationTime = document.getElementById('reservation_time').value;

                if (!customerName || !phoneNumber || !numberOfGuests || !reservationDate || !reservationTime) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return false;
                }

                // Validate phone number format
                const phoneRegex = /^[0-9\-\+\(\)\s]+$/;
                if (!phoneRegex.test(phoneNumber)) {
                    e.preventDefault();
                    alert('Please enter a valid phone number.');
                    return false;
                }

                // Validate email if provided
                const email = document.getElementById('customer_email').value.trim();
                if (email) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        e.preventDefault();
                        alert('Please enter a valid email address.');
                        return false;
                    }
                }

                // Disable submit button to prevent double submission
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            });            // Character counter for special requests
            const specialRequests = document.getElementById('special_requests');
            const maxLength = 500;

            specialRequests.addEventListener('input', function() {
                const currentLength = this.value.length;
                const formText = this.nextElementSibling;
                formText.textContent = `${currentLength}/${maxLength} characters`;

                if (currentLength > maxLength * 0.9) {
                    formText.classList.add('text-warning');
                } else {
                    formText.classList.remove('text-warning');
                }
            });

            // Auto-save draft functionality
            let autoSaveTimer;
            const formElements = document.querySelectorAll('#editBookingForm input, #editBookingForm select, #editBookingForm textarea');

            formElements.forEach(element => {
                element.addEventListener('input', function() {
                    clearTimeout(autoSaveTimer);
                    autoSaveTimer = setTimeout(saveDraft, 2000); // Save after 2 seconds of inactivity
                });
            });

            function saveDraft() {
                const formData = new FormData(document.getElementById('editBookingForm'));
                const draftData = {};
                for (let [key, value] of formData.entries()) {
                    draftData[key] = value;
                }
                localStorage.setItem('booking_edit_draft_<?= $booking['id'] ?>', JSON.stringify(draftData));

                // Show subtle save indicator
                const saveIndicator = document.createElement('div');
                saveIndicator.className = 'position-fixed top-0 end-0 m-3 alert alert-info alert-dismissible fade show';
                saveIndicator.style.zIndex = '9999';
                saveIndicator.innerHTML = '<i class="fas fa-check"></i> Draft saved automatically';
                document.body.appendChild(saveIndicator);

                setTimeout(() => {
                    saveIndicator.remove();
                }, 2000);
            }

            // Load draft on page load
            function loadDraft() {
                const savedDraft = localStorage.getItem('booking_edit_draft_<?= $booking['id'] ?>');
                if (savedDraft) {
                    const draftData = JSON.parse(savedDraft);
                    Object.keys(draftData).forEach(key => {
                        const element = document.querySelector(`[name="${key}"]`);
                        if (element && element.value !== draftData[key]) {
                            element.value = draftData[key];
                            element.style.backgroundColor = '#fff3cd'; // Highlight changed fields
                        }
                    });
                }
            }

            // Clear draft after successful submission
            document.getElementById('editBookingForm').addEventListener('submit', function() {
                localStorage.removeItem('booking_edit_draft_<?= $booking['id'] ?>');
            });

            // Load draft when page loads
            loadDraft();

            // Enhanced date validation
            const bookingDate = document.getElementById('booking_date');
            const bookingTime = document.getElementById('booking_time');

            function validateDateTime() {
                const selectedDate = new Date(bookingDate.value);
                const selectedTime = bookingTime.value;
                const now = new Date();
                const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

                if (selectedDate < today) {
                    bookingDate.setCustomValidity('Booking date cannot be in the past');
                } else if (selectedDate.getTime() === today.getTime() && selectedTime) {
                    const [hours, minutes] = selectedTime.split(':');
                    const selectedDateTime = new Date();
                    selectedDateTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);

                    if (selectedDateTime <= now) {
                        bookingTime.setCustomValidity('Booking time must be in the future');
                    } else {
                        bookingTime.setCustomValidity('');
                    }
                } else {
                    bookingDate.setCustomValidity('');
                    bookingTime.setCustomValidity('');
                }
            }

            bookingDate.addEventListener('change', validateDateTime);
            bookingTime.addEventListener('change', validateDateTime);

            // Initial validation
            validateDateTime();
        });
    </script>
</body>
</html>
