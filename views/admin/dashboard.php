<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'views/admin/layouts/header.php'; ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php require_once 'views/admin/layouts/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Flash Messages -->
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show mt-3" role="alert">
                        <?= htmlspecialchars($_SESSION['flash_message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php
                    unset($_SESSION['flash_message']);
                    unset($_SESSION['flash_type']);
                    ?>
                <?php endif; ?>

                <!-- Page Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshDashboard()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-gradient-primary h-100 shadow">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold  text-uppercase mb-1">
                                            Total Orders
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold ">
                                            <?= number_format($stats['total_orders'] ?? 0) ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shopping-cart fa-2x -50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-gradient-success h-100 shadow">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold  text-uppercase mb-1">
                                            Revenue (This Month)
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold ">
                                            $<?= number_format($stats['monthly_revenue'] ?? 0, 2) ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-dollar-sign fa-2x -50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-gradient-info h-100 shadow">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold  text-uppercase mb-1">
                                            Active Bookings
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold ">
                                            <?= number_format($stats['active_bookings'] ?? 0) ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-check fa-2x -50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-gradient-warning h-100 shadow">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold  text-uppercase mb-1">
                                            Total Users
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold ">
                                            <?= number_format($stats['total_users'] ?? 0) ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x -50"></i>
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
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-chart-line me-2"></i>Revenue Trend
                                </h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow">
                                        <a class="dropdown-item" href="#" onclick="exportChart('revenue')">
                                            <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                                            Export Chart
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueChart" height="80"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Status Pie Chart -->
                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-chart-pie me-2"></i>Booking Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <canvas id="bookingStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Row -->
                <div class="row">
                    <!-- Recent Orders -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-shopping-cart me-2"></i>Recent Orders
                                </h6>
                                <a href="<?= SITE_URL ?>/admin/orders" class="btn btn-sm btn-primary">
                                    View All <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (isset($stats['recent_orders']) && !empty($stats['recent_orders'])): ?>
                                                <?php foreach ($stats['recent_orders'] as $order): ?>
                                                    <tr>
                                                        <td>#<?= $order['id'] ?></td>
                                                        <td><?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></td>
                                                        <td>$<?= number_format($order['total_amount'] ?? 0, 2) ?></td>
                                                        <td>
                                                            <span class="badge bg-<?= $order['status'] === 'completed' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                                <?= ucfirst($order['status'] ?? 'unknown') ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">
                                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
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

                    <!-- Recent Bookings -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-calendar-check me-2"></i>Recent Bookings
                                </h6>
                                <a href="<?= SITE_URL ?>/admin/bookings" class="btn btn-sm btn-primary">
                                    View All <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Booking #</th>
                                                <th>Customer</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (isset($stats['recent_bookings']) && !empty($stats['recent_bookings'])): ?>
                                                <?php foreach ($stats['recent_bookings'] as $booking): ?>
                                                    <tr>
                                                        <td>#<?= $booking['id'] ?></td>
                                                        <td><?= htmlspecialchars($booking['customer_name'] ?? 'N/A') ?></td>
                                                        <td><?= date('M j, Y', strtotime($booking['booking_date'] ?? 'now')) ?></td>
                                                        <td>
                                                            <span class="badge bg-<?= $booking['status'] === 'confirmed' ? 'success' : ($booking['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                                <?= ucfirst($booking['status'] ?? 'unknown') ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">
                                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
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
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-bolt me-2"></i>Quick Actions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <a href="<?= SITE_URL ?>/admin/foods/create" class="btn btn-outline-primary btn-block">
                                            <i class="fas fa-plus-circle"></i> Add New Food
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="<?= SITE_URL ?>/admin/categories/create" class="btn btn-outline-success btn-block">
                                            <i class="fas fa-tags"></i> Add Category
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="<?= SITE_URL ?>/admin/tables/create" class="btn btn-outline-info btn-block">
                                            <i class="fas fa-chair"></i> Add Table
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="<?= SITE_URL ?>/admin/users" class="btn btn-outline-warning btn-block">
                                            <i class="fas fa-users-cog"></i> Manage Users
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>    <?php require_once 'views/admin/layouts/footer.php'; ?>    <script>
        window.SITE_URL = '<?= SITE_URL ?>';
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = {
                monthly_revenue_data: <?= json_encode($stats['monthly_revenue_data'] ?? [0,0,0,0,0,0,0,0,0,0,0,0]) ?>,
                booking_stats: {
                    confirmed: <?= $stats['confirmed_bookings'] ?? 0 ?>,
                    pending: <?= $stats['pending_bookings'] ?? 0 ?>,
                    cancelled: <?= $stats['cancelled_bookings'] ?? 0 ?>
                }
            };
            initializeCharts(chartData);
        });
    </script>
    </script>
</body>
</html>
