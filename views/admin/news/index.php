<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token ?? ''; ?>">
    <title>News Management - Admin</title>    <?php require_once 'views/admin/layouts/header.php'; ?>
</head>
<body class="admin-page news-index">
    <div class="container-fluid">
        <div class="row">
            <?php require_once 'views/admin/layouts/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Page Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2">News Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">News</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportNews()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                        <a href="<?= SITE_URL ?>/admin/news/create" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add Article
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
                                            Total Articles
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $stats['total_news'] ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-newspaper fa-2x text-gray-300"></i>
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
                                            Published
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $stats['published_news'] ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-eye fa-2x text-gray-300"></i>
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
                                            Draft Articles
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $stats['draft_news'] ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-edit fa-2x text-gray-300"></i>
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
                                            This Month
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $stats['month_news'] ?? 0; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- News Table -->
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">News Articles</h6>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control float-right"
                                       placeholder="Search articles..." id="searchInput"
                                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-default" onclick="searchNews()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($data['news'])): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No news articles found</h5>
                                <p class="text-muted">Start by creating your first news article.</p>                                <a href="<?= SITE_URL ?>/admin/news/create" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create Article
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
                                            <th width="10%">Image</th>
                                            <th width="30%">Title</th>
                                            <th width="15%">Category</th>
                                            <th width="10%">Author</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">Created</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['news'] as $article): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="news-checkbox" value="<?php echo $article['id']; ?>">
                                                </td>
                                                <td>
                                                    <?php if (!empty($article['image'])): ?>                                                        <img src="<?php echo SITE_URL; ?>/uploads/news_images/<?php echo $article['image']; ?>"
                                                             alt="Article image" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-light d-flex align-items-center justify-content-center"
                                                             style="width: 60px; height: 60px; border-radius: 4px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($article['title']); ?></strong>
                                                        <?php if (!empty($article['summary'])): ?>
                                                            <br><small class="text-muted">
                                                                <?php echo htmlspecialchars(substr($article['summary'], 0, 80)); ?>...
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if (!empty($article['category'])): ?>
                                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($article['category']); ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">Uncategorized</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small><?php echo htmlspecialchars($article['author'] ?? 'Admin'); ?></small>
                                                </td>
                                                <td>
                                                    <?php                                    switch($article['status']) {
                                        case 'published':
                                            $statusClass = 'bg-success';
                                            break;
                                        case 'draft':
                                            $statusClass = 'bg-warning';
                                            break;
                                        case 'archived':
                                            $statusClass = 'bg-secondary';
                                            break;
                                        default:
                                            $statusClass = 'bg-secondary';
                                            break;
                                    }
                                                    ?>
                                                    <span class="badge <?php echo $statusClass; ?>">
                                                        <?php echo ucfirst($article['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small><?php echo date('M j, Y', strtotime($article['created_at'])); ?></small>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="/news/<?php echo $article['id']; ?>"
                                                           class="btn btn-sm btn-outline-info"
                                                           title="Preview" target="_blank">
                                                            <i class="fas fa-eye"></i>
                                                        </a>                                                        <a href="<?= SITE_URL ?>/admin/news/edit/<?php echo $article['id']; ?>"
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-<?php echo $article['status'] === 'published' ? 'warning' : 'success'; ?>"
                                                                onclick="toggleStatus(<?php echo $article['id']; ?>, '<?php echo $article['status'] === 'published' ? 'draft' : 'published'; ?>')"
                                                                title="<?php echo $article['status'] === 'published' ? 'Unpublish' : 'Publish'; ?>">
                                                            <i class="fas fa-<?php echo $article['status'] === 'published' ? 'eye-slash' : 'eye'; ?>"></i>
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="deleteNews(<?php echo $article['id']; ?>)"
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
                                            <option value="publish">Publish Selected</option>
                                            <option value="unpublish">Unpublish Selected</option>
                                            <option value="delete">Delete Selected</option>
                                        </select>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="executeBulkAction()">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <small class="text-muted">
                                        Showing <?php echo count($data['news']); ?> articles
                                    </small>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <?php if (isset($data['totalPages']) && $data['totalPages'] > 1): ?>
                                <nav aria-label="News pagination" class="mt-4">
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

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Articles</h5>
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
                                        <option value="published" <?php echo (isset($_GET['status']) && $_GET['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                                        <option value="draft" <?php echo (isset($_GET['status']) && $_GET['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                                        <option value="archived" <?php echo (isset($_GET['status']) && $_GET['status'] == 'archived') ? 'selected' : ''; ?>>Archived</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filterCategory" class="form-label">Category</label>
                                    <select class="form-select" id="filterCategory" name="category">
                                        <option value="">All Categories</option>
                                        <option value="promotions">Promotions</option>
                                        <option value="events">Events</option>
                                        <option value="announcements">Announcements</option>
                                        <option value="menu">Menu Updates</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filterDateFrom" class="form-label">Date From</label>
                                    <input type="date" class="form-control" id="filterDateFrom" name="date_from"
                                           value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filterDateTo" class="form-label">Date To</label>
                                    <input type="date" class="form-control" id="filterDateTo" name="date_to"
                                           value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
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
    </div>    <?php require_once 'views/admin/layouts/footer.php'; ?>    <script>
    // Set SITE_URL for admin functions
    window.SITE_URL = '<?= SITE_URL ?>';
    </script>
</body>
</html>
