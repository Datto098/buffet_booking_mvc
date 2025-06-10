<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token ?? ''; ?>">
    <title>Tables Management - Admin</title>
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
                        <h1 class="h2">Tables Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Tables</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="showUtilizationReport()">
                                <i class="fas fa-chart-bar"></i> Report
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportTables()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                        <a href="<?= SITE_URL ?>/admin/tables/create" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add Table
                        </a>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-primary">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Tables
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $stats['total_tables'] ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-table fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-success">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Available
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $stats['available_tables'] ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-info">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total Capacity
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $stats['total_capacity'] ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 card-gradient-warning">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Avg Capacity
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo round($stats['average_capacity'] ?? 0, 1); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Distribution -->
                <?php if (!empty($data['locationStats'])): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Tables by Location</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach ($data['locationStats'] as $location): ?>
                                        <div class="col-md-2 col-sm-6 mb-3">
                                            <div class="text-center p-3 border rounded">
                                                <div class="location-icon mb-2">
                                                    <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                                                </div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($location['location'] ?: 'Main Area'); ?></h6>
                                                <div class="text-primary font-weight-bold"><?php echo $location['table_count']; ?> tables</div>
                                                <small class="text-muted"><?php echo $location['total_capacity']; ?> seats</small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Tables Table -->
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">All Tables</h6>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control float-right"
                                       placeholder="Search tables..." id="searchInput"
                                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-default" onclick="searchTables()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($data['tables'])): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-table fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No tables found</h5>
                                <p class="text-muted">Start by adding your first table to the restaurant.</p>                                <a href="<?= SITE_URL ?>/admin/tables/create" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create Table
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes()">
                                            </th>
                                            <th width="15%">Table #</th>
                                            <th width="10%">Capacity</th>
                                            <th width="15%">Location</th>
                                            <th width="25%">Description</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">Created</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['tables'] as $table): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="table-checkbox" value="<?php echo $table['id']; ?>">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="table-icon me-2">
                                                            <i class="fas fa-table text-primary"></i>
                                                        </div>
                                                        <strong><?php echo htmlspecialchars($table['table_number']); ?></strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info text-white">
                                                        <i class="fas fa-users"></i> <?php echo $table['capacity']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($table['location'])): ?>
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                            <?php echo htmlspecialchars($table['location']); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">Main Area</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($table['description'])): ?>
                                                        <span class="text-muted">
                                                            <?php
                                                            $desc = htmlspecialchars($table['description']);
                                                            echo strlen($desc) > 50 ? substr($desc, 0, 50) . '...' : $desc;
                                                            ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">No description</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="status_<?php echo $table['id']; ?>"
                                                               <?php echo $table['is_available'] ? 'checked' : ''; ?>
                                                               onchange="toggleTableStatus(<?php echo $table['id']; ?>, this.checked)">
                                                        <label class="form-check-label" for="status_<?php echo $table['id']; ?>">
                                                            <span class="badge bg-<?php echo $table['is_available'] ? 'success' : 'secondary'; ?>">
                                                                <?php echo $table['is_available'] ? 'Available' : 'Unavailable'; ?>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small><?php echo date('M j, Y', strtotime($table['created_at'])); ?></small>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-info"
                                                                onclick="viewTableHistory(<?php echo $table['id']; ?>)"
                                                                title="View History">
                                                            <i class="fas fa-history"></i>
                                                        </button>                                                        <a href="<?= SITE_URL ?>/admin/tables/edit/<?php echo $table['id']; ?>"
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-success"
                                                                onclick="quickBooking(<?php echo $table['id']; ?>)"
                                                                title="Quick Booking">
                                                            <i class="fas fa-calendar-plus"></i>
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="deleteTable(<?php echo $table['id']; ?>)"
                                                                title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Bulk Actions -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <select class="form-select form-select-sm me-2" id="bulkAction" style="width: auto;">
                                            <option value="">Bulk Actions</option>
                                            <option value="enable">Make Available</option>
                                            <option value="disable">Make Unavailable</option>
                                            <option value="delete">Delete Selected</option>
                                        </select>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="executeBulkAction()">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <small class="text-muted">
                                        Showing <?php echo count($data['tables']); ?> tables
                                    </small>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <?php if (isset($data['totalPages']) && $data['totalPages'] > 1): ?>
                                <nav aria-label="Tables pagination" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($data['currentPage'] > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $data['currentPage'] - 1; ?>">Previous</a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
                                            <li class="page-item <?php echo $i == $data['currentPage'] ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($data['currentPage'] < $data['totalPages']): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $data['currentPage'] + 1; ?>">Next</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
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
        <div class="modal-dialog modal-lg">
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

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Tables</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" method="GET">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filterStatus" class="form-label">Status</label>
                                    <select class="form-select" id="filterStatus" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="available" <?php echo (isset($_GET['status']) && $_GET['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                                        <option value="unavailable" <?php echo (isset($_GET['status']) && $_GET['status'] == 'unavailable') ? 'selected' : ''; ?>>Unavailable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filterLocation" class="form-label">Location</label>
                                    <select class="form-select" id="filterLocation" name="location">
                                        <option value="">All Locations</option>
                                        <?php if (!empty($data['locationStats'])): ?>
                                            <?php foreach ($data['locationStats'] as $location): ?>
                                                <option value="<?php echo htmlspecialchars($location['location']); ?>"
                                                        <?php echo (isset($_GET['location']) && $_GET['location'] == $location['location']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($location['location'] ?: 'Main Area'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filterCapacityMin" class="form-label">Min Capacity</label>
                                    <input type="number" class="form-control" id="filterCapacityMin" name="capacity_min"
                                           value="<?php echo htmlspecialchars($_GET['capacity_min'] ?? ''); ?>" min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filterCapacityMax" class="form-label">Max Capacity</label>
                                    <input type="number" class="form-control" id="filterCapacityMax" name="capacity_max"
                                           value="<?php echo htmlspecialchars($_GET['capacity_max'] ?? ''); ?>" min="1">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear Filters</button>
                    <button type="button" class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'views/admin/layouts/footer.php'; ?>    <script>
        window.SITE_URL = '<?= SITE_URL ?>';

        // Search functionality
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = this.value;
                const url = new URL(window.location);
                if (searchTerm) {
                    url.searchParams.set('search', searchTerm);
                } else {
                    url.searchParams.delete('search');
                }
                window.location = url;
            }
        });
    </script>
</body>
</html>
