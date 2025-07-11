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
                <h1 class="h2">Add New Table</h1>
                <div class="btn-toolbar mb-2 mb-md-0">                    <a href="<?= SITE_URL ?>/admin/tables" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Tables
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

            <div class="row">
                <div class="col-lg-8">
                    <!-- Table Form -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Table Information</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= SITE_URL ?>/admin/tables/create" class="needs-validation" novalidate>
                                <input type="hidden" name="csrf_token" value="<?= $data['csrf_token'] ?>">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="table_number" class="form-label">Table Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="table_number" name="table_number" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid table number.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="capacity" class="form-label">Capacity (Guests) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" max="20" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid capacity between 1 and 20.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="location" class="form-label">Location (Address)</label>
                                            <select class="form-select" id="location" name="location">
                                                <option value="">Select Address</option>
                                                <?php if (!empty($data['addresses'])): ?>
                                                    <?php foreach ($data['addresses'] as $addr): ?>
                                                        <option value="<?= htmlspecialchars($addr['address']) ?>">
                                                            <?= htmlspecialchars($addr['address']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label d-block">Status</label>
                                        <div class="form-check form-check-inline mt-4">
                                            <input class="form-check-input" type="radio" name="is_available" id="available" value="1" checked>
                                            <label class="form-check-label" for="available">Available</label>
                                        </div>
                                        <div class="form-check form-check-inline mt-4">
                                            <input class="form-check-input" type="radio" name="is_available" id="unavailable" value="0">
                                            <label class="form-check-label" for="unavailable">Unavailable</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Optional description about the table (special features, location details, etc.)"></textarea>
                                </div>

                                <div class="d-flex justify-content-between">                                    <a href="<?= SITE_URL ?>/admin/tables" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Table
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Table Preview -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Preview</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="table-preview">
                                <div class="table-icon mb-3">
                                    <i class="fas fa-table fa-4x text-muted"></i>
                                </div>
                                <h5 id="preview-number">Table #</h5>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-users"></i>
                                    <span id="preview-capacity">0</span> guests
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span id="preview-location">No location</span>
                                </p>
                                <span class="badge bg-success" id="preview-status">Available</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Tips</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success"></i>
                                    Use unique table numbers
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success"></i>
                                    Set realistic capacity
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success"></i>
                                    Choose appropriate location
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success"></i>
                                    Add helpful descriptions
                                </li>
                                <li>
                                    <i class="fas fa-check text-success"></i>
                                    Consider table layout
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Set up site URL for admin functions
window.SITE_URL = '<?= SITE_URL ?>';

// Page initialization
document.addEventListener('DOMContentLoaded', function() {
    initializeTableCreateForm();
});
</script>

<?php require_once 'views/admin/layouts/footer.php'; ?>
