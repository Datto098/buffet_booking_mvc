<?php
$pageTitle = $data['title'];
require_once 'views/admin/layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php require_once 'views/admin/layouts/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Table Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-outline-primary me-2" onclick="loadUtilizationReport()">
                        <i class="fas fa-chart-bar"></i> Utilization Report
                    </button>
                    <a href="/admin/tables/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Table
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash'])): ?>
                <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center border-left-primary">
                        <div class="card-body">
                            <div class="text-primary font-weight-bold">Total Tables</div>
                            <div class="h4"><?= number_format($data['stats']['total_tables']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-left-success">
                        <div class="card-body">
                            <div class="text-success font-weight-bold">Available</div>
                            <div class="h4"><?= number_format($data['stats']['available_tables']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-left-info">
                        <div class="card-body">
                            <div class="text-info font-weight-bold">Total Capacity</div>
                            <div class="h4"><?= number_format($data['stats']['total_capacity']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-left-warning">
                        <div class="card-body">
                            <div class="text-warning font-weight-bold">Avg Capacity</div>
                            <div class="h4"><?= number_format($data['stats']['average_capacity'], 1) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Distribution -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Tables by Location</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($data['locationStats'] as $location): ?>
                                    <div class="col-md-2 text-center">
                                        <div class="border p-3 rounded">
                                            <strong><?= htmlspecialchars($location['location'] ?: 'No Location') ?></strong>
                                            <div class="text-primary"><?= $location['table_count'] ?> tables</div>
                                            <div class="text-muted small"><?= $location['total_capacity'] ?> seats</div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tables Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">All Tables</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Table #</th>
                                    <th>Capacity</th>
                                    <th>Location</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($data['tables'])): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-table fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No tables found.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($data['tables'] as $table): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($table['table_number']) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <i class="fas fa-users"></i> <?= $table['capacity'] ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($table['location'] ?: 'N/A') ?></td>
                                            <td>
                                                <span class="text-muted">
                                                    <?= $table['description'] ? htmlspecialchars(substr($table['description'], 0, 50)) . (strlen($table['description']) > 50 ? '...' : '') : 'N/A' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($table['is_available']): ?>
                                                    <span class="badge bg-success">Available</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Unavailable</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('M j, Y', strtotime($table['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="/admin/tables/edit/<?= $table['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="viewTableHistory(<?= $table['id'] ?>)" title="View History">
                                                        <i class="fas fa-history"></i>
                                                    </button>
                                                    <form method="POST" action="/admin/tables/delete/<?= $table['id'] ?>" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-delete" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($data['totalPages'] > 1): ?>
                        <nav aria-label="Tables pagination">
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
                                    <li class="page-item <?= $i == $data['currentPage'] ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Table History Modal -->
<div class="modal fade" id="tableHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Table Booking History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="tableHistoryContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Utilization Report Modal -->
<div class="modal fade" id="utilizationModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Table Utilization Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="utilizationContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this table? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});

function viewTableHistory(tableId) {
    fetch(`/admin/tables/${tableId}/history`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<div class="table-responsive"><table class="table table-sm">';
                html += '<thead><tr><th>Date</th><th>Time</th><th>Customer</th><th>Guests</th><th>Status</th></tr></thead><tbody>';

                if (data.history && data.history.length > 0) {
                    data.history.forEach(booking => {
                        html += `<tr>
                            <td>${new Date(booking.booking_date).toLocaleDateString()}</td>
                            <td>${booking.booking_time}</td>
                            <td>${booking.customer_name}</td>
                            <td>${booking.guest_count}</td>
                            <td><span class="badge bg-${getStatusColor(booking.status)}">${booking.status}</span></td>
                        </tr>`;
                    });
                } else {
                    html += '<tr><td colspan="5" class="text-center">No booking history found</td></tr>';
                }

                html += '</tbody></table></div>';
                document.getElementById('tableHistoryContent').innerHTML = html;

                const modal = new bootstrap.Modal(document.getElementById('tableHistoryModal'));
                modal.show();
            }
        })
        .catch(error => {
            console.error('Error loading table history:', error);
        });
}

function loadUtilizationReport() {
    fetch('/admin/tables/utilization?days=30')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<div class="mb-3"><h6>Table Utilization (Last 30 Days)</h6></div>';
                html += '<div class="table-responsive"><table class="table table-striped">';
                html += '<thead><tr><th>Table</th><th>Capacity</th><th>Total Bookings</th><th>Completed</th><th>Avg/Day</th><th>Utilization</th></tr></thead><tbody>';

                data.utilization.forEach(table => {
                    const utilizationRate = table.capacity > 0 ? (table.completed_bookings / (table.capacity * data.period_days)) * 100 : 0;
                    html += `<tr>
                        <td><strong>${table.table_number}</strong></td>
                        <td>${table.capacity}</td>
                        <td>${table.total_bookings}</td>
                        <td>${table.completed_bookings}</td>
                        <td>${parseFloat(table.avg_bookings_per_day).toFixed(1)}</td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-${utilizationRate > 70 ? 'success' : utilizationRate > 40 ? 'warning' : 'danger'}"
                                     style="width: ${Math.min(utilizationRate, 100)}%">
                                    ${utilizationRate.toFixed(1)}%
                                </div>
                            </div>
                        </td>
                    </tr>`;
                });

                html += '</tbody></table></div>';
                document.getElementById('utilizationContent').innerHTML = html;

                const modal = new bootstrap.Modal(document.getElementById('utilizationModal'));
                modal.show();
            }
        })
        .catch(error => {
            console.error('Error loading utilization report:', error);
        });
}

function getStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'confirmed': 'success',
        'completed': 'primary',
        'cancelled': 'danger',
        'no_show': 'secondary'
    };
    return colors[status] || 'secondary';
}
</script>

<?php require_once 'views/admin/layouts/footer.php'; ?>
