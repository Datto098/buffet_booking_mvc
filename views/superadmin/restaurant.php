<?php require_once 'views/layouts/superadmin_header.php'; ?>
<?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

<div class="main-content fade-in">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>
                    <i class="fas fa-store"></i>
                    Restaurant Information
                </h1>                <div class="btn-toolbar">
                    <div class="ms-2">
                        <small class="text-muted">You can modify restaurant details and click "Save Changes"</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Restaurant Information Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Restaurant Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= SITE_URL ?>/superadmin/restaurant" enctype="multipart/form-data" id="restaurantForm">
                            <?php echo csrf_token_field(); ?>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="restaurant_name" class="form-label">Restaurant Name <span class="text-danger">*</span></label>                                    <input type="text" class="form-control" id="restaurant_name" name="restaurant_name"
                                        value="<?php echo htmlspecialchars($info['restaurant_name'] ?? 'Buffet Restaurant'); ?>"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="<?php echo htmlspecialchars($info['phone'] ?? ''); ?>"
                                        required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?php echo htmlspecialchars($info['email'] ?? ''); ?>"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label for="website" class="form-label">Website</label>                                    <input type="url" class="form-control" id="website" name="website"
                                        value="<?php echo htmlspecialchars($info['website'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($info['address'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($info['description'] ?? ''); ?></textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="opening_hours" class="form-label">Opening Hours</label>                                    <input type="text" class="form-control" id="opening_hours" name="opening_hours"
                                        value="<?php echo htmlspecialchars($info['opening_hours'] ?? '09:00 - 22:00'); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="capacity" class="form-label">Total Capacity</label>                                    <input type="number" class="form-control" id="capacity" name="capacity"
                                        value="<?php echo htmlspecialchars($info['capacity'] ?? '100'); ?>"
                                        min="1">
                                </div>
                            </div>                            <div class="mb-3">
                                <label for="logo" class="form-label">Restaurant Logo</label>
                                <?php if (!empty($info['logo_url'])): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo $info['logo_url']; ?>" alt="Current Logo" class="img-thumbnail" style="max-width: 200px;">
                                        <small class="text-muted ms-2">Current logo</small>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                <div class="form-text">Supported formats: JPG, PNG, GIF (max 2MB)</div>
                            </div>

                            <div class="mb-3">
                                <label for="cover_image" class="form-label">Cover Image</label>
                                <?php if (!empty($info['cover_image'])): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo $info['cover_image']; ?>" alt="Current Cover" class="img-thumbnail" style="max-width: 300px;">
                                        <small class="text-muted ms-2">Current cover image</small>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                                <div class="form-text">Supported formats: JPG, PNG, GIF (max 5MB)</div>
                            </div>                            <div class="d-flex justify-content-end" id="formButtons">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Social Media -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Social Media</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="facebook" class="form-label">Facebook</label>                            <input type="url" class="form-control" id="facebook" name="facebook"
                                value="<?php echo htmlspecialchars($info['facebook'] ?? ''); ?>"
                                form="restaurantForm">
                        </div>
                        <div class="mb-3">
                            <label for="instagram" class="form-label">Instagram</label>                            <input type="url" class="form-control" id="instagram" name="instagram"
                                value="<?php echo htmlspecialchars($info['instagram'] ?? ''); ?>"
                                form="restaurantForm">
                        </div>
                        <div class="mb-3">
                            <label for="twitter" class="form-label">Twitter</label>                            <input type="url" class="form-control" id="twitter" name="twitter"
                                value="<?php echo htmlspecialchars($info['twitter'] ?? ''); ?>"
                                form="restaurantForm">
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Quick Stats</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-success"><?php echo $stats['total_orders'] ?? 0; ?></h4>
                                    <p class="small text-muted">Total Orders</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-info"><?php echo $stats['total_customers'] ?? 0; ?></h4>
                                <p class="small text-muted">Customers</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-warning"><?php echo $stats['total_tables'] ?? 0; ?></h4>
                                    <p class="small text-muted">Tables</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-primary">$<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></h4>
                                <p class="small text-muted">Revenue</p>
                            </div>
                        </div>
                        <hr>
                        <div class="small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Last Updated:</span>
                                <span><?php echo date('M d, Y H:i', strtotime($info['updated_at'] ?? 'now')); ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Status:</span>
                                <span class="badge bg-success">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple form submission with loading state
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('restaurantForm');

        if (form) {
            // Mark form to prevent global handler conflicts
            form.setAttribute('data-custom-handler', 'true');

            form.addEventListener('submit', function(e) {
                // Add loading state to submit button
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalHtml = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                    submitBtn.disabled = true;

                    // Re-enable after 10 seconds (fallback)
                    setTimeout(() => {
                        submitBtn.innerHTML = originalHtml;
                        submitBtn.disabled = false;
                    }, 10000);
                }
            });
        }
    });

    // File validation for logo
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('Logo file size must be less than 2MB');
                this.value = '';
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Only JPG, PNG, and GIF files are allowed for logo');
                this.value = '';
                return;
            }
        }
    });

    // File validation for cover image
    document.getElementById('cover_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('Cover image file size must be less than 5MB');
                this.value = '';
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Only JPG, PNG, and GIF files are allowed for cover image');
                this.value = '';
                return;
            }
        }
    });
</script>

<?php require_once 'views/layouts/superadmin_footer.php'; ?>
