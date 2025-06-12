<?php require_once 'views/layouts/superadmin_header.php'; ?>
<?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

<div class="main-content fade-in">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>
                    <i class="fas fa-tachometer-alt"></i>
                    Super Admin Dashboard
                </h1>
                <div class="btn-toolbar">
                    <button type="button" class="btn btn-outline-primary" onclick="refreshStats()">
                        <i class="fas fa-sync-alt"></i> Refresh Data
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-primary me-3">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">
                                    Monthly Revenue
                                </div>
                                <div class="h4 mb-0 fw-bold text-dark">
                                    $<?php echo number_format($stats['monthly_revenue'] ?? 0, 2); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-success me-3">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">
                                    Total Orders
                                </div>
                                <div class="h4 mb-0 fw-bold text-dark">
                                    <?php echo number_format($stats['total_orders'] ?? 0); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-warning me-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">
                                    Total Users
                                </div>
                                <div class="h4 mb-0 fw-bold text-dark">
                                    <?php echo number_format($stats['total_users'] ?? 0); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-gradient-info me-3">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase small fw-bold mb-1">
                                    Total Bookings
                                </div>
                                <div class="h4 mb-0 fw-bold text-dark">
                                    <?php echo number_format($stats['total_bookings'] ?? 0); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Revenue Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Revenue Overview
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Growth Chart -->
            <div class="col-xl-4 col-lg-5">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            User Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="userGrowthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Recent Orders
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($stats['recent_orders']) && !empty($stats['recent_orders'])): ?>
                                        <?php foreach ($stats['recent_orders'] as $order): ?>
                                            <tr>
                                                <td><strong>#<?php echo $order['id']; ?></strong></td>
                                                <td><?php echo htmlspecialchars($order['customer_name'] ?? 'N/A'); ?></td>
                                                <td><strong>$<?php echo number_format($order['total_amount'] ?? 0, 2); ?></strong></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $order['status'] === 'completed' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'info'); ?>">
                                                        <?php echo ucfirst($order['status'] ?? 'pending'); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="fas fa-info-circle me-2"></i>
                                                No recent orders
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-check me-2"></i>
                            Recent Bookings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($stats['recent_bookings']) && !empty($stats['recent_bookings'])): ?>
                                        <?php foreach ($stats['recent_bookings'] as $booking): ?>
                                            <tr>
                                                <td><strong>#<?php echo $booking['id']; ?></strong></td>
                                                <td><?php echo htmlspecialchars($booking['customer_name'] ?? 'N/A'); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($booking['booking_date'] ?? 'now')); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $booking['status'] === 'confirmed' ? 'success' : ($booking['status'] === 'pending' ? 'warning' : 'info'); ?>">
                                                        <?php echo ucfirst($booking['status'] ?? 'pending'); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="fas fa-info-circle me-2"></i>
                                                No recent bookings
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="<?= SITE_URL ?>/superadmin/users/create" class="btn btn-primary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-user-plus fa-2x mb-2"></i>
                                    <span>Add New User</span>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?= SITE_URL ?>/superadmin/promotions" class="btn btn-success w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-tags fa-2x mb-2"></i>
                                    <span>Manage Promotions</span>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?= SITE_URL ?>/superadmin/restaurant" class="btn btn-info w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-store fa-2x mb-2"></i>
                                    <span>Restaurant Settings</span>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?= SITE_URL ?>/superadmin/statistics" class="btn btn-warning w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                    <span>View Reports</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/superadmin_footer.php'; ?>

<script>
// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue ($)',
                    data: <?php echo json_encode($stats['revenue_chart_data'] ?? [1200, 1900, 3000, 5000, 2300, 3200]); ?>,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart');
    if (userGrowthCtx) {
        new Chart(userGrowthCtx, {
            type: 'doughnut',
            data: {
                labels: ['Customers', 'Managers', 'Super Admins'],
                datasets: [{
                    data: <?php echo json_encode($stats['user_distribution'] ?? [85, 12, 3]); ?>,
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    }
});

function refreshStats() {
    const refreshBtn = document.querySelector('button[onclick="refreshStats()"]');
    const originalHtml = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
    refreshBtn.disabled = true;

    // Simulate API call (replace with actual endpoint)
    fetch('<?= SITE_URL ?>/superadmin/dashboardStats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the page to show updated data
                location.reload();
            } else {
                console.error('Failed to refresh stats:', data.message);
            }
        })
        .catch(error => {
            console.error('Error refreshing stats:', error);
        })
        .finally(() => {
            refreshBtn.innerHTML = originalHtml;
            refreshBtn.disabled = false;
        });
}
</script>
