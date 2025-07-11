<?php
/**
 * Booking Details View
 */
$title = "Booking Details - " . SITE_NAME;
$current_page = 'bookings';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Booking Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h2 class="h4 mb-1">Booking #<?= $booking['id'] ?></h2>
                            <p class="text-muted mb-0">Created on <?= date('F j, Y \a\t g:i A', strtotime($booking['created_at'])) ?></p>
                        </div>
                        <span class="badge badge-status badge-<?= strtolower($booking['status']) ?> fs-6">
                            <?= ucfirst(str_replace('_', ' ', $booking['status'])) ?>
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">Customer Information</h6>
                            <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($booking['customer_name']) ?></p>
                            <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($booking['customer_email']) ?></p>
                            <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($booking['customer_phone']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">Booking Details</h6>
                            <p class="mb-1"><strong>Date:</strong> <?= date('l, F j, Y', strtotime($booking['booking_date'])) ?></p>
                            <p class="mb-1"><strong>Time:</strong> <?= date('g:i A', strtotime($booking['booking_time'])) ?></p>
                            <p class="mb-1"><strong>Guests:</strong> <?= $booking['guest_count'] ?></p>
                            <?php if (!empty($booking['booking_location'])): ?>
                            <p class="mb-1"><strong>Location:</strong> <?= htmlspecialchars($booking['booking_location']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($booking['table_number'])): ?>
                            <p class="mb-1"><strong>Table:</strong> <?= $booking['table_number'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Timeline -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Booking Status Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php
                        $timeline_items = [
                            ['status' => 'pending', 'label' => 'Booking Requested', 'icon' => 'clock'],
                            ['status' => 'confirmed', 'label' => 'Booking Confirmed', 'icon' => 'check-circle'],
                            ['status' => 'seated', 'label' => 'Table Ready', 'icon' => 'chair'],
                            ['status' => 'completed', 'label' => 'Dining Completed', 'icon' => 'check-double']
                        ];

                        $current_status_index = array_search($booking['status'], array_column($timeline_items, 'status'));
                        if ($booking['status'] === 'cancelled') {
                            $timeline_items = [
                                ['status' => 'pending', 'label' => 'Booking Requested', 'icon' => 'clock'],
                                ['status' => 'cancelled', 'label' => 'Booking Cancelled', 'icon' => 'times-circle']
                            ];
                            $current_status_index = 1;
                        } elseif ($booking['status'] === 'no_show') {
                            $timeline_items[] = ['status' => 'no_show', 'label' => 'No Show', 'icon' => 'exclamation-triangle'];
                            $current_status_index = count($timeline_items) - 1;
                        }
                        ?>

                        <?php foreach ($timeline_items as $index => $item): ?>
                        <div class="timeline-item <?= $index <= $current_status_index ? 'active' : '' ?> <?= $item['status'] === 'cancelled' || $item['status'] === 'no_show' ? 'cancelled' : '' ?>">
                            <div class="timeline-marker">
                                <i class="fas fa-<?= $item['icon'] ?>"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1"><?= $item['label'] ?></h6>
                                <?php if ($index <= $current_status_index): ?>
                                <small class="text-muted">
                                    <?= $index == $current_status_index ? 'Current Status' : 'Completed' ?>
                                </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Special Requests -->
            <?php if (!empty($booking['special_requests'])): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Special Requests</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(htmlspecialchars($booking['special_requests'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Restaurant Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Restaurant Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">Address</h6>
                            <p class="mb-3">
                                123 Buffet Street<br>
                                Food District, FD 12345<br>
                                United States
                            </p>
                            <h6 class="fw-bold mb-2">Contact</h6>
                            <p class="mb-1"><i class="fas fa-phone me-2"></i>(555) 123-4567</p>
                            <p class="mb-1"><i class="fas fa-envelope me-2"></i>info@buffetbooking.com</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">Business Hours</h6>
                            <div class="mb-3">
                                <p class="mb-1"><strong>Monday - Thursday:</strong> 11:00 AM - 9:00 PM</p>
                                <p class="mb-1"><strong>Friday - Saturday:</strong> 11:00 AM - 10:00 PM</p>
                                <p class="mb-1"><strong>Sunday:</strong> 12:00 PM - 8:00 PM</p>
                            </div>
                            <h6 class="fw-bold mb-2">Policies</h6>
                            <ul class="small mb-0">
                                <li>Cancellations must be made at least 2 hours in advance</li>
                                <li>Late arrivals (15+ minutes) may result in table reassignment</li>
                                <li>No-shows will be noted in your booking history</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Countdown for upcoming bookings -->
            <?php if (($booking['status'] === 'pending' || $booking['status'] === 'confirmed') &&
                      strtotime($booking['booking_date'] . ' ' . $booking['booking_time']) > time()): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <h5 class="mb-3">Time Until Your Reservation</h5>
                    <div class="countdown-display" data-target="<?= strtotime($booking['booking_date'] . ' ' . $booking['booking_time']) ?>">
                        <div class="row">
                            <div class="col-3">
                                <div class="countdown-item">
                                    <span class="countdown-number" id="days">--</span>
                                    <span class="countdown-label">Days</span>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="countdown-item">
                                    <span class="countdown-number" id="hours">--</span>
                                    <span class="countdown-label">Hours</span>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="countdown-item">
                                    <span class="countdown-number" id="minutes">--</span>
                                    <span class="countdown-label">Minutes</span>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="countdown-item">
                                    <span class="countdown-number" id="seconds">--</span>
                                    <span class="countdown-label">Seconds</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="text-center">
                <a href="index.php?page=booking" class="btn btn-outline-primary me-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Bookings
                </a>

                <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                    <?php if (strtotime($booking['booking_date'] . ' ' . $booking['booking_time']) > time() + 7200): // At least 2 hours before ?>
                    <a href="index.php?page=booking&action=modify&id=<?= $booking['id'] ?>" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-2"></i>Modify Booking
                    </a>
                    <button class="btn btn-danger me-2" onclick="cancelBooking(<?= $booking['id'] ?>)">
                        <i class="fas fa-times me-2"></i>Cancel Booking
                    </button>
                    <?php endif; ?>
                <?php elseif ($booking['status'] === 'completed'): ?>
                <button class="btn btn-success me-2" onclick="rebookTable(<?= $booking['id'] ?>)">
                    <i class="fas fa-redo me-2"></i>Book Again
                </button>
                <?php endif; ?>

                <button class="btn btn-info" onclick="printBooking()">
                    <i class="fas fa-print me-2"></i>Print Details
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.badge-status {
    padding: 0.5rem 1rem;
}
.badge-pending { background-color: #ffc107; color: #000; }
.badge-confirmed { background-color: #28a745; color: #fff; }
.badge-seated { background-color: #17a2b8; color: #fff; }
.badge-completed { background-color: #6f42c1; color: #fff; }
.badge-cancelled { background-color: #dc3545; color: #fff; }
.badge-no-show { background-color: #6c757d; color: #fff; }

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-item.active .timeline-marker {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.timeline-item.cancelled .timeline-marker {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
}

.timeline-marker {
    position: absolute;
    left: -1.5rem;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
}

.timeline-content {
    margin-left: 1rem;
}

.countdown-display {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 15px;
    padding: 2rem 1rem;
    color: white;
}

.countdown-item {
    text-align: center;
}

.countdown-number {
    display: block;
    font-size: 2rem;
    font-weight: bold;
    line-height: 1;
}

.countdown-label {
    display: block;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.5rem;
}

@media (max-width: 768px) {
    .countdown-number {
        font-size: 1.5rem;
    }
    .countdown-label {
        font-size: 0.75rem;
    }
}
</style>

<script>
// Countdown functionality
function updateCountdown() {
    const countdownElement = document.querySelector('.countdown-display');
    if (!countdownElement) return;

    const targetTime = parseInt(countdownElement.dataset.target) * 1000;
    const now = new Date().getTime();
    const distance = targetTime - now;

    if (distance > 0) {
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById('days').textContent = days.toString().padStart(2, '0');
        document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
        document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
        document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
    } else {
        document.getElementById('days').textContent = '00';
        document.getElementById('hours').textContent = '00';
        document.getElementById('minutes').textContent = '00';
        document.getElementById('seconds').textContent = '00';
    }
}

// Update countdown every second
const countdownInterval = setInterval(updateCountdown, 1000);
updateCountdown(); // Initial update

function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
        fetch(`index.php?page=booking&action=cancel`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
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

function printBooking() {
    window.print();
}

// Print styles
window.addEventListener('beforeprint', function() {
    document.body.classList.add('printing');
});

window.addEventListener('afterprint', function() {
    document.body.classList.remove('printing');
});
</script>

<style media="print">
@media print {
    .btn, .navbar, .footer, .card-header {
        display: none !important;
    }

    .container {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        break-inside: avoid;
    }

    .countdown-display {
        background: #f8f9fa !important;
        color: #000 !important;
    }    body.printing .countdown-display {
        display: none !important;
    }
}
</style>
