<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'views/layouts/superadmin_header.php'; ?>
    <title>Notification Management - Super Admin</title>
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
                            <i class="fas fa-bell me-2 text-info"></i>Notification Management
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/superadmin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Notifications</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-outline-primary" onclick="markAllAsRead()">
                                <i class="fas fa-check-double me-1"></i>Mark All Read
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="refreshNotifications()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-danger" onclick="bulkDelete()" disabled id="bulkDeleteBtn">
                                <i class="fas fa-trash me-1"></i>Delete Selected
                            </button>
                        </div>
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
                ?>                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Notifications</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['total_notifications'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-bell fa-2x opacity-75"></i>
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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Unread</div>
                                        <div class="h4 mb-0 font-weight-bold" id="unreadCount"><?= number_format($stats['unread_count'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle fa-2x opacity-75"></i>
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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Order Notifications</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['order_notifications'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Booking Notifications</div>
                                        <div class="h4 mb-0 font-weight-bold"><?= number_format($stats['booking_notifications'] ?? 0) ?></div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-calendar-check fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>                    </div>
                </div>

                <!-- Filter Bar -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-filter me-2 text-info"></i>Filters
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="type" class="form-label fw-bold">Notification Type</label>
                                <select class="form-select" id="type" name="type" onchange="this.form.submit()">
                                    <option value="">All Types</option>
                                    <option value="new_order" <?php echo ($currentType === 'new_order') ? 'selected' : ''; ?>>New Orders</option>
                                    <option value="new_booking" <?php echo ($currentType === 'new_booking') ? 'selected' : ''; ?>>New Bookings</option>
                                    <option value="booking_status_update" <?php echo ($currentType === 'booking_status_update') ? 'selected' : ''; ?>>Booking Status Updates</option>
                                    <option value="system" <?php echo ($currentType === 'system') ? 'selected' : ''; ?>>System</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch pt-4">
                                    <input class="form-check-input" type="checkbox" id="unread" name="unread" value="1"
                                        <?php echo $unreadOnly ? 'checked' : ''; ?> onchange="this.form.submit()">
                                    <label class="form-check-label fw-bold" for="unread">
                                        <i class="fas fa-eye-slash me-1"></i>Show Unread Only
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <a href="<?= SITE_URL ?>/superadmin/notifications" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i>Clear Filters
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-bold">
                                <i class="fas fa-bell me-2 text-info"></i>All Notifications
                            </h6>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleAllCheckboxes()">
                                    <label class="form-check-label fw-bold" for="selectAll">
                                        Select All
                                    </label>
                                </div>
                                <div class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <?php echo count($notifications ?? []); ?> notifications displayed
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                            <i class="fas fa-times"></i> Clear Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list"></i> Notifications
                    </h5>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleAllCheckboxes()">
                        <label class="form-check-label" for="selectAll">
                            Select All
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($notifications)): ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-bell-slash fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">No notifications found</h5>
                        <p class="text-muted">You're all caught up! No notifications to display.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($notifications as $notification): ?>
                            <?php
                            $data = json_decode($notification['data'], true);
                            $isUnread = !$notification['is_read'];
                            $typeClass = match($notification['type']) {
                                'new_order' => 'success',
                                'new_booking' => 'info',
                                'booking_status_update' => 'warning',
                                'system' => 'warning',
                                default => 'secondary'
                            };
                            $typeIcon = match($notification['type']) {
                                'new_order' => 'shopping-cart',
                                'new_booking' => 'calendar-alt',
                                'booking_status_update' => 'calendar-check',
                                'system' => 'cog',
                                default => 'bell'
                            };
                            ?>
                            <div class="list-group-item notification-item <?php echo $isUnread ? 'unread' : ''; ?>"
                                 data-id="<?php echo $notification['id']; ?>">
                                <div class="d-flex align-items-start">
                                    <div class="form-check me-3 mt-1">
                                        <input class="form-check-input notification-checkbox" type="checkbox"
                                               value="<?php echo $notification['id']; ?>" onchange="updateBulkActions()">
                                    </div>

                                    <div class="notification-icon me-3">
                                        <div class="avatar-sm bg-<?php echo $typeClass; ?> text-white rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="fas fa-<?php echo $typeIcon; ?>"></i>
                                        </div>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                                <p class="mb-1 text-muted"><?php echo htmlspecialchars($notification['message']); ?></p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo date('M j, Y \a\t g:i A', strtotime($notification['created_at'])); ?>
                                                </small>
                                            </div>
                                            <div class="notification-actions">
                                                <?php if ($isUnread): ?>
                                                    <span class="badge bg-primary me-2">New</span>
                                                <?php endif; ?>
                                                <span class="badge bg-<?php echo $typeClass; ?> me-2">
                                                    <?php echo ucfirst(str_replace('_', ' ', $notification['type'])); ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mt-2">
                                            <div class="btn-group btn-group-sm">
                                                <?php if ($notification['type'] === 'new_order' && isset($data['url'])): ?>
                                                    <a href="<?php echo SITE_URL . $data['url']; ?>"
                                                       class="btn btn-outline-primary btn-sm"
                                                       onclick="markAsRead(<?php echo $notification['id']; ?>)">
                                                        <i class="fas fa-eye"></i> View Order
                                                    </a>
                                                <?php elseif ($notification['type'] === 'new_booking' && isset($data['url'])): ?>
                                                    <a href="<?php echo SITE_URL . $data['url']; ?>"
                                                       class="btn btn-outline-primary btn-sm"
                                                       onclick="markAsRead(<?php echo $notification['id']; ?>)">
                                                        <i class="fas fa-calendar-check"></i> View Booking
                                                    </a>
                                                <?php elseif ($notification['type'] === 'booking_status_update' && isset($data['url'])): ?>
                                                    <a href="<?php echo SITE_URL . $data['url']; ?>"
                                                       class="btn btn-outline-warning btn-sm"
                                                       onclick="markAsRead(<?php echo $notification['id']; ?>)">
                                                        <i class="fas fa-calendar-check"></i> View Status
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($isUnread): ?>
                                                    <button type="button" class="btn btn-outline-success btn-sm"
                                                            onclick="markAsRead(<?php echo $notification['id']; ?>)">
                                                        <i class="fas fa-check"></i> Mark Read
                                                    </button>
                                                <?php endif; ?>

                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                        onclick="deleteNotification(<?php echo $notification['id']; ?>)">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="card-footer">
                    <nav aria-label="Notifications pagination">
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&type=<?php echo $currentType; ?>&unread=<?php echo $unreadOnly ? '1' : '0'; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>

                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <li class="page-item <?php echo ($i === $currentPage) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&type=<?php echo $currentType; ?>&unread=<?php echo $unreadOnly ? '1' : '0'; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&type=<?php echo $currentType; ?>&unread=<?php echo $unreadOnly ? '1' : '0'; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/superadmin_footer.php'; ?>

<style>
.notification-item {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.notification-item.unread {
    background-color: #f8f9ff;
    border-left-color: #007bff;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 16px;
}

.stats-card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: transform 0.15s ease-in-out;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
}

.bg-gradient-success {
    background: linear-gradient(45deg, #28a745, #1e7e34);
}

.bg-gradient-info {
    background: linear-gradient(45deg, #17a2b8, #117a8b);
}

.bg-gradient-warning {
    background: linear-gradient(45deg, #ffc107, #d39e00);
}
</style>

<script>
// Real-time notification checking
let notificationInterval;

document.addEventListener('DOMContentLoaded', function() {
    // Start real-time checking
    startNotificationPolling();

    // Update bulk actions on page load
    updateBulkActions();
});

function startNotificationPolling() {
    // Check for new notifications every 30 seconds
    notificationInterval = setInterval(checkNewNotifications, 30000);
}

function stopNotificationPolling() {
    if (notificationInterval) {
        clearInterval(notificationInterval);
    }
}

function checkNewNotifications() {
    fetch('<?= SITE_URL ?>/superadmin/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            if (data.unread_count > 0) {
                // Update unread count display
                document.getElementById('unreadCount').textContent = data.unread_count;

                // Show notification badge in sidebar if exists
                updateSidebarNotificationBadge(data.unread_count);

                // Play notification sound (optional)
                // playNotificationSound();
            }
        })
        .catch(error => console.error('Error checking notifications:', error));
}

function updateSidebarNotificationBadge(count) {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline' : 'none';
    }
}

function markAsRead(notificationId) {
    fetch(`<?= SITE_URL ?>/superadmin/notifications/mark-read/${notificationId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove unread styling
            const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('unread');
                const badge = notificationItem.querySelector('.badge.bg-primary');
                if (badge) badge.remove();
            }

            // Update unread count
            updateUnreadCount(-1);
            showNotification('Notification marked as read', 'success');
        } else {
            showNotification('Failed to mark notification as read', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error marking notification as read', 'error');
    });
}

function markAllAsRead() {
    if (!confirm('Mark all notifications as read?')) return;

    fetch('<?= SITE_URL ?>/superadmin/notifications/mark-all-read', {
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
            showNotification('Failed to mark all notifications as read', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error marking notifications as read', 'error');
    });
}

function deleteNotification(notificationId) {
    if (!confirm('Delete this notification?')) return;

    fetch(`<?= SITE_URL ?>/superadmin/notifications/delete/${notificationId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.remove();
            }
            showNotification('Notification deleted successfully', 'success');
        } else {
            showNotification('Failed to delete notification', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error deleting notification', 'error');
    });
}

function toggleAllCheckboxes() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.notification-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });

    updateBulkActions();
}

function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.notification-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    bulkDeleteBtn.disabled = checkedBoxes.length === 0;
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.notification-checkbox:checked');
    if (checkedBoxes.length === 0) return;

    if (!confirm(`Delete ${checkedBoxes.length} selected notification(s)?`)) return;

    const notificationIds = Array.from(checkedBoxes).map(cb => cb.value);

    fetch('<?= SITE_URL ?>/superadmin/notifications/bulk-action', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            action: 'delete',
            notification_ids: notificationIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            checkedBoxes.forEach(checkbox => {
                const notificationItem = checkbox.closest('.notification-item');
                if (notificationItem) notificationItem.remove();
            });
            showNotification('Selected notifications deleted successfully', 'success');
            updateBulkActions();
        } else {
            showNotification('Failed to delete notifications', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error deleting notifications', 'error');
    });
}

function refreshNotifications() {
    location.reload();
}

function clearFilters() {
    window.location.href = '<?= SITE_URL ?>/superadmin/notifications';
}

function updateUnreadCount(change) {
    const unreadCountEl = document.getElementById('unreadCount');
    if (unreadCountEl) {
        const currentCount = parseInt(unreadCountEl.textContent.replace(/,/g, '')) || 0;
        const newCount = Math.max(0, currentCount + change);
        unreadCountEl.textContent = newCount.toLocaleString();
    }
}

function showNotification(message, type) {
    // Create notification toast
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(toast);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    stopNotificationPolling();
});
</script>

            </main>
        </div>
    </div>
</body>
</html>
