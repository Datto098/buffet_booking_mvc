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
                <h1 class="h2">Food Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="/admin/foods/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Food Item
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

            <!-- Filter Bar -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($data['categories'] as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= isset($_GET['category']) && $_GET['category'] == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="available" <?= isset($_GET['status']) && $_GET['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                                <option value="unavailable" <?= isset($_GET['status']) && $_GET['status'] == 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="/admin/foods" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stats -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="border-left-primary py-2">
                                        <div class="text-primary font-weight-bold">Total Items</div>
                                        <div class="h4"><?= number_format($data['totalFoods']) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border-left-success py-2">
                                        <div class="text-success font-weight-bold">Available</div>
                                        <div class="h4"><?= count(array_filter($data['foods'], fn($f) => $f['is_available'])) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border-left-info py-2">
                                        <div class="text-info font-weight-bold">Featured</div>
                                        <div class="h4"><?= count(array_filter($data['foods'], fn($f) => $f['is_featured'])) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border-left-warning py-2">
                                        <div class="text-warning font-weight-bold">Categories</div>
                                        <div class="h4"><?= count($data['categories']) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Food Items Grid -->
            <div class="row">
                <?php if (empty($data['foods'])): ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No food items found</h5>
                                <p class="text-muted">Start by adding your first food item.</p>
                                <a href="/admin/foods/create" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Food Item
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($data['foods'] as $food): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="position-relative">
                                    <?php if ($food['image']): ?>
                                        <img src="/assets/images/foods/<?= $food['image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($food['name']) ?>" style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                            <i class="fas fa-utensils fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Status Badges -->
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <?php if ($food['is_featured']): ?>
                                            <span class="badge bg-warning text-dark mb-1 d-block">Featured</span>
                                        <?php endif; ?>
                                        <?php if ($food['is_available']): ?>
                                            <span class="badge bg-success">Available</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Unavailable</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= htmlspecialchars($food['name']) ?></h5>
                                    <p class="card-text text-muted small flex-grow-1">
                                        <?= htmlspecialchars(substr($food['description'], 0, 100)) ?>
                                        <?= strlen($food['description']) > 100 ? '...' : '' ?>
                                    </p>

                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-info"><?= htmlspecialchars($food['category_name']) ?></span>
                                            <span class="h5 mb-0 text-primary">$<?= number_format($food['price'], 2) ?></span>
                                        </div>

                                        <div class="btn-group w-100" role="group">
                                            <a href="/admin/foods/edit/<?= $food['id'] ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" action="/admin/foods/delete/<?= $food['id'] ?>" class="d-inline flex-fill">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm btn-delete w-100">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($data['totalPages'] > 1): ?>
                <nav aria-label="Food items pagination">
                    <ul class="pagination justify-content-center">
                        <?php if ($data['currentPage'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="/admin/foods?page=<?= $data['currentPage'] - 1 ?>">Previous</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
                            <li class="page-item <?= $i === $data['currentPage'] ? 'active' : '' ?>">
                                <a class="page-link" href="/admin/foods?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($data['currentPage'] < $data['totalPages']): ?>
                            <li class="page-item">
                                <a class="page-link" href="/admin/foods?page=<?= $data['currentPage'] + 1 ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </main>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>

<?php require_once 'views/admin/layouts/footer.php'; ?>
