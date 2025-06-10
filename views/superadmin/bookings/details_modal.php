<?php

/**
 * SuperAdmin Booking Details Modal Content
 */
?>

<div class="booking-details" style="z-index: 2000">
    <!-- Booking Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="fw-bold mb-2">Booking Information</h6>
            <p class="mb-1"><strong>Booking ID:</strong> #<?= str_pad($booking['id'], 6, '0', STR_PAD_LEFT) ?></p>
            <p class="mb-1"><strong>Status:</strong>
                <span class="badge bg-<?=
                                        $booking['status'] === 'pending' ? 'warning' : ($booking['status'] === 'confirmed' ? 'success' : ($booking['status'] === 'seated' ? 'info' : ($booking['status'] === 'completed' ? 'success' : ($booking['status'] === 'cancelled' ? 'danger' : 'secondary'))))
                                        ?>">
                    <?= ucfirst($booking['status']) ?>
                </span>
            </p>
            <p class="mb-1"><strong>Reservation Date:</strong>
                <?= !empty($booking['reservation_time']) ? date('M j, Y', strtotime($booking['reservation_time'])) : 'N/A' ?>
            </p>
            <p class="mb-1"><strong>Reservation Time:</strong>
                <?= !empty($booking['reservation_time']) ? date('g:i A', strtotime($booking['reservation_time'])) : 'N/A' ?>
            </p>
            <p class="mb-1"><strong>Party Size:</strong> <?= htmlspecialchars($booking['number_of_guests'] ?? 'N/A') ?> guests</p>
            <p class="mb-1"><strong>Table:</strong>
                <?php if (!empty($booking['table_number'])): ?>
                    Table <?= htmlspecialchars($booking['table_number']) ?>
                    <small class="text-muted">(Capacity: <?= htmlspecialchars($booking['capacity'] ?? 'N/A') ?>)</small>
                <?php else: ?>
                    <span class="text-muted">Not assigned</span>
                <?php endif; ?>
            </p>
            <p class="mb-1"><strong>Created:</strong>
                <?= !empty($booking['created_at']) ? date('M j, Y \a\t g:i A', strtotime($booking['created_at'])) : 'N/A' ?>
            </p>
        </div>
        <div class="col-md-6">
            <h6 class="fw-bold mb-2">Customer Information</h6>
            <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($booking['customer_name'] ?? 'N/A') ?></p>
            <p class="mb-1"><strong>Phone:</strong>
                <?php if (!empty($booking['phone_number'])): ?>
                    <a href="tel:<?= htmlspecialchars($booking['phone_number']) ?>"><?= htmlspecialchars($booking['phone_number']) ?></a>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </p>
            <?php if (!empty($booking['user_id'])): ?>
                <p class="mb-1"><strong>User ID:</strong> #<?= htmlspecialchars($booking['user_id']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Special Requests / Notes -->
    <?php if (!empty($booking['notes'])): ?>
        <div class="row mb-3">
            <div class="col-12">
                <h6 class="fw-bold mb-2">Special Requests / Notes</h6>
                <div class="alert alert-info">
                    <?= nl2br(htmlspecialchars($booking['notes'])) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Booking Timeline -->
    <div class="row">
        <div class="col-12">
            <h6 class="fw-bold mb-2">Booking Timeline</h6>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker bg-primary"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Booking Created</h6>
                        <p class="text-muted mb-0">
                            <?= !empty($booking['created_at']) ? date('M j, Y \a\t g:i A', strtotime($booking['created_at'])) : 'N/A' ?>
                        </p>
                    </div>
                </div>

                <?php if ($booking['status'] !== 'pending'): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-<?=
                                                        $booking['status'] === 'confirmed' ? 'success' : ($booking['status'] === 'cancelled' ? 'danger' : 'info')
                                                        ?>"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Status: <?= ucfirst($booking['status']) ?></h6>
                            <p class="text-muted mb-0">
                                <?= !empty($booking['updated_at']) ? date('M j, Y \a\t g:i A', strtotime($booking['updated_at'])) : 'Status updated' ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .booking-details .timeline {
        position: relative;
        padding-left: 20px;
    }

    .booking-details .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }

    .booking-details .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: -16px;
        top: 20px;
        bottom: -8px;
        width: 2px;
        background-color: #dee2e6;
    }

    .booking-details .timeline-marker {
        position: absolute;
        left: -20px;
        top: 4px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .booking-details .timeline-content h6 {
        font-size: 0.9rem;
        font-weight: 600;
    }

    .booking-details .timeline-content p {
        font-size: 0.8rem;
    }
</style>
