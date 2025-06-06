<?php
/**
 * Admin Dashboard View
 */

$title = $data['title'] ?? 'Admin Dashboard';
$stats = $data['stats'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Buffet Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
    <style>
        .dashboard-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
        .quick-actions {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .activity-item {
            border-left: 3px solid #007bff;
            padding-left: 1rem;
            margin-bottom: 1rem;
        }
        .sidebar {
            background: #2c3e50;
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: #34495e;
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: #3498db;
            color: #fff;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 p-0">
                <div class="sidebar">
                    <div class="p-4">
                        <h5 class="text-white mb-4">
                            <i class="bi bi-shield-check"></i>
                            Admin Panel
                        </h5>
                        <nav class="nav flex-column">
                            <a class="nav-link active" href="/admin/dashboard">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard
                            </a>
                            <a class="nav-link" href="/admin/users">
                                <i class="bi bi-people me-2"></i>
                                Users
                            </a>
                            <a class="nav-link" href="/admin/foods">
                                <i class="bi bi-egg-fried me-2"></i>
                                Food Items
                            </a>
                            <a class="nav-link" href="/admin/categories">
                                <i class="bi bi-grid me-2"></i>
                                Categories
                            </a>
                            <a class="nav-link" href="/admin/bookings">
                                <i class="bi bi-calendar-check me-2"></i>
                                Bookings
                            </a>
                            <a class="nav-link" href="/admin/orders">
                                <i class="bi bi-receipt me-2"></i>
                                Orders
                            </a>
                            <a class="nav-link" href="/admin/reports">
                                <i class="bi bi-graph-up me-2"></i>
                                Reports
                            </a>
                            <hr class="text-light">
                            <a class="nav-link" href="/logout">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Logout
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10">
                <!-- Header -->
                <div class="bg-white shadow-sm p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">Welcome back, Admin!</h3>
                            <p class="text-muted mb-0">Here's what's happening with your restaurant today.</p>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small">Last updated: <?= date('M d, Y H:i') ?></div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card dashboard-card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?= number_format($stats['total_users'] ?? 0) ?></h3>
                                        <p class="text-muted mb-0 small">Total Users</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card dashboard-card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                                        <i class="bi bi-egg-fried"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?= number_format($stats['total_foods'] ?? 0) ?></h3>
                                        <p class="text-muted mb-0 small">Food Items</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card dashboard-card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?= number_format($stats['total_bookings'] ?? 0) ?></h3>
                                        <p class="text-muted mb-0 small">Total Bookings</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card dashboard-card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                                        <i class="bi bi-receipt"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?= number_format($stats['total_orders'] ?? 0) ?></h3>
                                        <p class="text-muted mb-0 small">Total Orders</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Overview -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-primary">
                                    <i class="bi bi-calendar-day me-2"></i>
                                    Today's Activity
                                </h6>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Orders</span>
                                        <span class="fw-bold"><?= $stats['today_orders'] ?? 0 ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Revenue</span>
                                        <span class="fw-bold text-success">₹<?= number_format($stats['today_revenue'] ?? 0, 2) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Pending Orders</span>
                                        <span class="fw-bold text-warning"><?= $stats['pending_orders'] ?? 0 ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-success">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Booking Status
                                </h6>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Confirmed</span>
                                        <span class="fw-bold text-success"><?= $stats['confirmed_bookings'] ?? 0 ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Pending</span>
                                        <span class="fw-bold text-warning"><?= ($stats['total_bookings'] ?? 0) - ($stats['confirmed_bookings'] ?? 0) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Completion Rate</span>
                                        <span class="fw-bold text-info">
                                            <?= $stats['total_bookings'] > 0 ? round(($stats['confirmed_bookings'] / $stats['total_bookings']) * 100, 1) : 0 ?>%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card quick-actions text-white border-0">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-lightning me-2"></i>
                                    Quick Actions
                                </h6>
                                <div class="mt-3">
                                    <a href="/admin/foods/create" class="btn btn-light btn-sm d-block mb-2">
                                        <i class="bi bi-plus-circle me-2"></i>Add Food Item
                                    </a>                                    <a href="/admin/bookings" class="btn btn-light btn-sm d-block mb-2">
                                        <i class="bi bi-calendar-plus me-2"></i>View Bookings
                                    </a>
                                    <a href="/admin/tables" class="btn btn-light btn-sm d-block mb-2">
                                        <i class="bi bi-table me-2"></i>Manage Tables
                                    </a>
                                    <a href="/admin/users/create" class="btn btn-light btn-sm d-block">
                                        <i class="bi bi-person-plus me-2"></i>Add User
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Analytics -->
                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-graph-up me-2"></i>
                                    Revenue Overview (Last 7 Days)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="revenueChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-pie-chart me-2"></i>
                                    Order Status Distribution
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="statusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-clock-history me-2"></i>
                                    Recent Orders
                                </h6>
                            </div>
                            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                <div id="recentOrders">
                                    <!-- Orders will be loaded here via AJAX -->
                                    <div class="text-center py-3">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="small text-muted mt-2">Loading recent orders...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    Upcoming Bookings
                                </h6>
                            </div>
                            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                <div id="upcomingBookings">
                                    <!-- Bookings will be loaded here via AJAX -->
                                    <div class="text-center py-3">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="small text-muted mt-2">Loading upcoming bookings...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Revenue (₹)',
                    data: [12000, 19000, 3000, 5000, 20000, 30000, 45000],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Pending', 'Cancelled'],
                datasets: [{
                    data: [<?= $stats['total_orders'] - $stats['pending_orders'] - 5 ?>, <?= $stats['pending_orders'] ?? 0 ?>, 5],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Load recent activity
        function loadRecentOrders() {
            fetch('/admin/api/recent-orders')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('recentOrders');
                    if (data.success && data.orders.length > 0) {
                        container.innerHTML = data.orders.map(order => `
                            <div class="activity-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">${order.customer_name || 'Guest'}</h6>
                                        <p class="text-muted small mb-0">Order #${order.id} • ₹${parseFloat(order.total_amount).toFixed(2)}</p>
                                    </div>
                                    <span class="badge bg-${order.status === 'completed' ? 'success' : 'warning'}">${order.status}</span>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<div class="text-center text-muted py-3">No recent orders</div>';
                    }
                })
                .catch(() => {
                    document.getElementById('recentOrders').innerHTML = '<div class="text-center text-muted py-3">Failed to load orders</div>';
                });
        }

        function loadUpcomingBookings() {
            fetch('/admin/api/upcoming-bookings')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('upcomingBookings');
                    if (data.success && data.bookings.length > 0) {
                        container.innerHTML = data.bookings.map(booking => `
                            <div class="activity-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">${booking.customer_name}</h6>
                                        <p class="text-muted small mb-0">${new Date(booking.reservation_time).toLocaleString()} • ${booking.number_of_guests} guests</p>
                                    </div>
                                    <span class="badge bg-${booking.status === 'confirmed' ? 'success' : 'warning'}">${booking.status}</span>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<div class="text-center text-muted py-3">No upcoming bookings</div>';
                    }
                })
                .catch(() => {
                    document.getElementById('upcomingBookings').innerHTML = '<div class="text-center text-muted py-3">Failed to load bookings</div>';
                });
        }

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadRecentOrders();
            loadUpcomingBookings();

            // Auto-refresh every 30 seconds
            setInterval(() => {
                loadRecentOrders();
                loadUpcomingBookings();
            }, 30000);
        });
    </script>
</body>
</html>
