<?php
/**
 * Booking History View
 */
$title = "Booking History - " . SITE_NAME;
$current_page = 'bookings';

$total_pages = (int)$total_pages;
$current_page = (int)$current_page;
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Booking History</h2>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                        <option value="">All Bookings</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="seated">Seated</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="no_show">No Show</option>
                    </select>
                    <select class="form-select form-select-sm" id="dateFilter" style="width: auto;">
                        <option value="">All Time</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="past">Past Bookings</option>
                    </select>
                    <a href="<?= SITE_NAME ?>/booking" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>New Booking
                    </a>
                </div>
            </div>

            <?php if (empty($bookings)): ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-calendar-alt fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">No Bookings Found</h4>
                <p class="text-muted mb-4">You haven't made any table reservations yet. Reserve a table to enjoy our buffet!</p>
                <a href="<?= SITE_NAME ?>/booking" class="btn btn-primary">
                    <i class="fas fa-calendar-plus me-2"></i>Make a Reservation
                </a>
            </div>
            <?php else: ?>

            <div class="row" id="bookingsContainer">
                <?php foreach ($bookings as $booking): ?>
                <div class="col-lg-6 col-xl-4 mb-4 booking-card"
                     data-status="<?= $booking['status'] ?>"
                     data-date="<?= $booking['booking_date'] ?>"
                     data-time="<?= $booking['booking_time'] ?>">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="card-title mb-1">Booking #<?= $booking['id'] ?></h6>
                                    <small class="text-muted">
                                        Created <?= date('M j, Y', strtotime($booking['created_at'])) ?>
                                    </small>
                                </div>
                                <span class="badge badge-status badge-<?= strtolower($booking['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $booking['status'])) ?>
                                </span>
                            </div>

                            <div class="booking-details mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar me-2 text-primary"></i>
                                    <span><?= date('l, F j, Y', strtotime($booking['booking_date'])) ?></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-clock me-2 text-primary"></i>
                                    <span><?= date('g:i A', strtotime($booking['booking_time'])) ?></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-users me-2 text-primary"></i>
                                    <span><?= $booking['guest_count'] ?> Guest<?= $booking['guest_count'] > 1 ? 's' : '' ?></span>
                                </div>
                                <?php if (!empty($booking['booking_location'])): ?>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                    <span><?= htmlspecialchars(substr($booking['booking_location'], 0, 50)) ?><?= strlen($booking['booking_location']) > 50 ? '...' : '' ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($booking['table_number'])): ?>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-chair me-2 text-primary"></i>
                                    <span>Table <?= $booking['table_number'] ?></span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($booking['special_requests'])): ?>
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Special Requests:</small>
                                <p class="small mb-0"><?= htmlspecialchars(substr($booking['special_requests'], 0, 100)) ?><?= strlen($booking['special_requests']) > 100 ? '...' : '' ?></p>
                            </div>
                            <?php endif; ?>

                            <!-- Booking Actions -->
                            <div class="d-flex gap-2 mt-auto">
                                <a href="index.php?page=booking&action=detail&id=<?= $booking['id'] ?>"
                                   class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>

                                <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                                    <?php if (strtotime($booking['booking_date'] . ' ' . $booking['booking_time']) > time() + 3600): // At least 1 hour before ?>
                                    <button class="btn btn-outline-warning btn-sm" onclick="modifyBooking(<?= $booking['id'] ?>)">
                                        <i class="fas fa-edit me-1"></i>Modify
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="cancelBooking(<?= $booking['id'] ?>)">
                                        <i class="fas fa-times me-1"></i>Cancel
                                    </button>
                                    <?php endif; ?>
                                <?php elseif ($booking['status'] === 'completed'): ?>
                                <button class="btn btn-success btn-sm" onclick="rebookTable(<?= $booking['id'] ?>)">
                                    <i class="fas fa-redo me-1"></i>Rebook
                                </button>
                                <?php endif; ?>
                            </div>

                            <!-- Countdown for upcoming bookings -->
                            <?php if (($booking['status'] === 'pending' || $booking['status'] === 'confirmed') &&
                                      strtotime($booking['booking_date'] . ' ' . $booking['booking_time']) > time()): ?>
                            <div class="mt-2 text-center">
                                <small class="text-muted">
                                    <i class="fas fa-hourglass-half me-1"></i>
                                    <span class="countdown" data-target="<?= strtotime($booking['booking_date'] . ' ' . $booking['booking_time']) ?>">
                                        Calculating...
                                    </span>
                                </small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Booking history pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($current_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?page=booking&action=myBookings&p=<?= $current_page - 1 ?><?= $filter_params ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                        <a class="page-link" href="index.php?page=booking&action=myBookings&p=<?= $i ?><?= $filter_params ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?page=booking&action=myBookings&p=<?= $current_page + 1 ?><?= $filter_params ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.badge-status {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}
.badge-pending { background-color: #ffc107; color: #000; }
.badge-confirmed { background-color: #28a745; color: #fff; }
.badge-seated { background-color: #17a2b8; color: #fff; }
.badge-completed { background-color: #6f42c1; color: #fff; }
.badge-cancelled { background-color: #dc3545; color: #fff; }
.badge-no-show { background-color: #6c757d; color: #fff; }

.booking-details i {
    width: 16px;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.booking-card.filtered-out {
    display: none !important;
}

.countdown {
    font-weight: 500;
}
</style>

<script>
// Filter functionality
document.getElementById('statusFilter').addEventListener('change', filterBookings);
document.getElementById('dateFilter').addEventListener('change', filterBookings);

function filterBookings() {
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const bookingCards = document.querySelectorAll('.booking-card');

    bookingCards.forEach(card => {
        let showCard = true;

        // Status filter
        if (statusFilter && card.dataset.status !== statusFilter) {
            showCard = false;
        }

        // Date filter
        if (dateFilter && showCard) {
            const bookingDateTime = new Date(card.dataset.date + ' ' + card.dataset.time);
            const now = new Date();

            switch (dateFilter) {
                case 'upcoming':
                    if (bookingDateTime <= now) showCard = false;
                    break;
                case 'today':
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    if (bookingDateTime < today || bookingDateTime >= tomorrow) showCard = false;
                    break;
                case 'week':
                    const weekStart = new Date();
                    weekStart.setDate(now.getDate() - now.getDay());
                    weekStart.setHours(0, 0, 0, 0);
                    const weekEnd = new Date(weekStart);
                    weekEnd.setDate(weekEnd.getDate() + 7);
                    if (bookingDateTime < weekStart || bookingDateTime >= weekEnd) showCard = false;
                    break;
                case 'month':
                    const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
                    const monthEnd = new Date(now.getFullYear(), now.getMonth() + 1, 1);
                    if (bookingDateTime < monthStart || bookingDateTime >= monthEnd) showCard = false;
                    break;
                case 'past':
                    if (bookingDateTime > now) showCard = false;
                    break;
            }
        }

        if (showCard) {
            card.classList.remove('filtered-out');
        } else {
            card.classList.add('filtered-out');
        }
    });

    // Show/hide no results message
    const visibleCards = document.querySelectorAll('.booking-card:not(.filtered-out)');
    const container = document.getElementById('bookingsContainer');

    if (visibleCards.length === 0) {
        if (!document.getElementById('noResultsMessage')) {
            const noResultsDiv = document.createElement('div');
            noResultsDiv.id = 'noResultsMessage';
            noResultsDiv.className = 'col-12 text-center py-5';
            noResultsDiv.innerHTML = `
                <div class="mb-4">
                    <i class="fas fa-search fa-3x text-muted"></i>
                </div>
                <h5 class="text-muted mb-3">No Bookings Found</h5>
                <p class="text-muted">No bookings match your current filters.</p>
            `;
            container.appendChild(noResultsDiv);
        }
    } else {
        const noResultsMessage = document.getElementById('noResultsMessage');
        if (noResultsMessage) {
            noResultsMessage.remove();
        }
    }
}

// Countdown functionality
function updateCountdowns() {
    document.querySelectorAll('.countdown').forEach(element => {
        const targetTime = parseInt(element.dataset.target) * 1000;
        const now = new Date().getTime();
        const distance = targetTime - now;

        if (distance > 0) {
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

            if (days > 0) {
                element.textContent = `${days} day${days > 1 ? 's' : ''} ${hours} hour${hours > 1 ? 's' : ''} remaining`;
            } else if (hours > 0) {
                element.textContent = `${hours} hour${hours > 1 ? 's' : ''} ${minutes} minute${minutes > 1 ? 's' : ''} remaining`;
            } else {
                element.textContent = `${minutes} minute${minutes > 1 ? 's' : ''} remaining`;
            }
        } else {
            element.textContent = 'Booking time has passed';
        }
    });
}

// Update countdowns every minute
setInterval(updateCountdowns, 60000);
updateCountdowns(); // Initial update

function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking?')) {
        fetch(`index.php?page=booking&action=cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
                // Nếu có CSRF token thì thêm dòng dưới:
                // ,'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ booking_id: bookingId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Booking cancelled successfully', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message || 'Failed to cancel booking', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while cancelling the booking', 'error');
        });
    }
}

function modifyBooking(bookingId) {
    window.location.href = `index.php?page=booking&action=modify&id=${bookingId}`;
}

function rebookTable(bookingId) {
    fetch(`index.php?page=booking&action=rebook&id=${bookingId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
            // ,'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Booking details copied. Redirecting to booking form...', 'success');
            setTimeout(() => window.location.href = 'index.php?page=booking', 1500);
        } else {
            showAlert(data.message || 'Failed to copy booking details', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while copying booking details', 'error');
    });
}
</script>
