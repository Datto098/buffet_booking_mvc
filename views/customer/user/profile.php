<?php
$title = "My Profile - " . APP_NAME;
$current_page = 'profile';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-4">
            <!-- Profile Sidebar -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="profile-avatar mb-3">
                        <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= ASSETS_URL ?>/images/avatars/<?= $user['avatar'] ?>"
                             alt="Profile Picture" class="rounded-circle" width="100" height="100">
                        <?php else: ?>
                        <div class="avatar-placeholder rounded-circle mx-auto d-flex align-items-center justify-content-center">
                            <i class="fas fa-user fa-2x text-muted"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <h5 class="mb-1"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h5>
                    <p class="text-muted mb-3"><?= htmlspecialchars($user['email']) ?></p>
                    <span class="badge bg-success">Active Member</span>
                </div>
            </div>

            <!-- Profile Menu -->
            <div class="card border-0 shadow-sm">
                <div class="list-group list-group-flush">
                    <a href="#profile-info" class="list-group-item list-group-item-action active" data-tab="profile-info">
                        <i class="fas fa-user me-2"></i>Profile Information
                    </a>
                    <a href="#security" class="list-group-item list-group-item-action" data-tab="security">
                        <i class="fas fa-lock me-2"></i>Security & Password
                    </a>
                    <a href="#preferences" class="list-group-item list-group-item-action" data-tab="preferences">
                        <i class="fas fa-cog me-2"></i>Preferences
                    </a>
                    <a href="#order-history" class="list-group-item list-group-item-action" data-tab="order-history">
                        <i class="fas fa-shopping-bag me-2"></i>Order History
                    </a>
                    <a href="#booking-history" class="list-group-item list-group-item-action" data-tab="booking-history">
                        <i class="fas fa-calendar me-2"></i>Booking History
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Profile Information Tab -->
            <div id="profile-info" class="tab-content active">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Profile Information</h5>
                    </div>                    <div class="card-body">
                        <form id="profileForm" method="POST" action="<?= SITE_URL ?>/user/update-profile" enctype="multipart/form-data">
                            <?= csrf_token() ?>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                           value="<?= htmlspecialchars($user['first_name']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                           value="<?= htmlspecialchars($user['last_name']) ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                       value="<?= $user['date_of_birth'] ?? '' ?>">
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="avatar" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                <small class="form-text text-muted">Max file size: 2MB. Supported formats: JPG, PNG, GIF</small>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security Tab -->
            <div id="security" class="tab-content">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Security & Password</h5>
                    </div>                    <div class="card-body">
                        <form id="passwordForm" method="POST" action="<?= SITE_URL ?>/user/change-password">
                            <?= csrf_token() ?>

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <div class="form-text">Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number.</div>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                        </form>

                        <hr class="my-4">

                        <h6 class="mb-3">Two-Factor Authentication</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1">Add an extra layer of security to your account</p>
                                <small class="text-muted">Protect your account with SMS or app-based authentication</small>
                            </div>
                            <button class="btn btn-outline-primary btn-sm" onclick="enable2FA()">
                                <i class="fas fa-shield-alt me-2"></i>Enable 2FA
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferences Tab -->
            <div id="preferences" class="tab-content">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Preferences</h5>
                    </div>
                    <div class="card-body">
                        <form id="preferencesForm" method="POST" action="<?= BASE_URL ?>/user/update-preferences">
                            <?= csrf_token() ?>

                            <h6 class="mb-3">Email Notifications</h6>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="order_updates" name="notifications[]"
                                           value="order_updates" <?= in_array('order_updates', $user['notifications'] ?? []) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="order_updates">
                                        Order status updates
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="promotional" name="notifications[]"
                                           value="promotional" <?= in_array('promotional', $user['notifications'] ?? []) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="promotional">
                                        Promotional offers and discounts
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="menu_updates" name="notifications[]"
                                           value="menu_updates" <?= in_array('menu_updates', $user['notifications'] ?? []) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="menu_updates">
                                        New menu items and specials
                                    </label>
                                </div>
                            </div>

                            <h6 class="mb-3">Dietary Preferences</h6>
                            <div class="mb-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="vegetarian" name="dietary[]"
                                           value="vegetarian" <?= in_array('vegetarian', $user['dietary_preferences'] ?? []) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="vegetarian">Vegetarian</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="vegan" name="dietary[]"
                                           value="vegan" <?= in_array('vegan', $user['dietary_preferences'] ?? []) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="vegan">Vegan</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="gluten_free" name="dietary[]"
                                           value="gluten_free" <?= in_array('gluten_free', $user['dietary_preferences'] ?? []) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="gluten_free">Gluten-Free</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="dairy_free" name="dietary[]"
                                           value="dairy_free" <?= in_array('dairy_free', $user['dietary_preferences'] ?? []) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="dairy_free">Dairy-Free</label>
                                </div>
                            </div>

                            <h6 class="mb-3">Default Order Preferences</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="default_order_type" class="form-label">Default Order Type</label>
                                    <select class="form-select" id="default_order_type" name="default_order_type">
                                        <option value="">No Preference</option>
                                        <option value="dine_in" <?= ($user['default_order_type'] ?? '') == 'dine_in' ? 'selected' : '' ?>>Dine In</option>
                                        <option value="takeout" <?= ($user['default_order_type'] ?? '') == 'takeout' ? 'selected' : '' ?>>Takeout</option>
                                        <option value="delivery" <?= ($user['default_order_type'] ?? '') == 'delivery' ? 'selected' : '' ?>>Delivery</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="default_payment_method" class="form-label">Preferred Payment Method</label>
                                    <select class="form-select" id="default_payment_method" name="default_payment_method">
                                        <option value="">No Preference</option>
                                        <option value="credit_card" <?= ($user['default_payment_method'] ?? '') == 'credit_card' ? 'selected' : '' ?>>Credit Card</option>
                                        <option value="debit_card" <?= ($user['default_payment_method'] ?? '') == 'debit_card' ? 'selected' : '' ?>>Debit Card</option>
                                        <option value="cash" <?= ($user['default_payment_method'] ?? '') == 'cash' ? 'selected' : '' ?>>Cash</option>
                                        <option value="digital_wallet" <?= ($user['default_payment_method'] ?? '') == 'digital_wallet' ? 'selected' : '' ?>>Digital Wallet</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Preferences
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order History Tab -->
            <div id="order-history" class="tab-content">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Orders</h5>
                        <a href="<?= BASE_URL ?>/order/history" class="btn btn-outline-primary btn-sm">
                            View All Orders
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_orders)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No orders found</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td>#<?= $order['id'] ?></td>
                                        <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                        <td><?= $order['total_items'] ?></td>
                                        <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                        <td>
                                            <span class="badge badge-<?= strtolower($order['status']) ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/order/detail/<?= $order['id'] ?>"
                                               class="btn btn-outline-primary btn-sm">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Booking History Tab -->
            <div id="booking-history" class="tab-content">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Bookings</h5>
                        <a href="<?= BASE_URL ?>/booking/history" class="btn btn-outline-primary btn-sm">
                            View All Bookings
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_bookings)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No bookings found</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Booking #</th>
                                        <th>Date & Time</th>
                                        <th>Guests</th>
                                        <th>Table</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_bookings as $booking): ?>
                                    <tr>
                                        <td>#<?= $booking['id'] ?></td>
                                        <td>
                                            <?= date('M j, Y', strtotime($booking['booking_date'])) ?><br>
                                            <small class="text-muted"><?= date('g:i A', strtotime($booking['booking_time'])) ?></small>
                                        </td>
                                        <td><?= $booking['guest_count'] ?></td>
                                        <td><?= $booking['table_number'] ?? 'TBD' ?></td>
                                        <td>
                                            <span class="badge badge-<?= strtolower($booking['status']) ?>">
                                                <?= ucfirst($booking['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/booking/detail/<?= $booking['id'] ?>"
                                               class="btn btn-outline-primary btn-sm">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-placeholder {
    width: 100px;
    height: 100px;
    background-color: #f8f9fa;
    border: 2px solid #e9ecef;
}

.list-group-item-action.active {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.badge-pending { background-color: #ffc107; color: #000; }
.badge-confirmed { background-color: #17a2b8; color: #fff; }
.badge-preparing { background-color: #fd7e14; color: #fff; }
.badge-ready { background-color: #20c997; color: #fff; }
.badge-completed { background-color: #28a745; color: #fff; }
.badge-cancelled { background-color: #dc3545; color: #fff; }
</style>

<script>
// Tab switching functionality
document.querySelectorAll('[data-tab]').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();

        // Remove active class from all tabs and content
        document.querySelectorAll('[data-tab]').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

        // Add active class to clicked tab and corresponding content
        this.classList.add('active');
        document.getElementById(this.dataset.tab).classList.add('active');
    });
});

// Profile form submission
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-Token': formData.get('csrf_token')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Profile updated successfully', 'success');
        } else {
            showAlert(data.message || 'Failed to update profile', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while updating profile', 'error');
    });
});

// Password form submission
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    if (newPassword !== confirmPassword) {
        showAlert('Passwords do not match', 'error');
        return;
    }

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-Token': formData.get('csrf_token')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Password changed successfully', 'success');
            this.reset();
        } else {
            showAlert(data.message || 'Failed to change password', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while changing password', 'error');
    });
});

// Preferences form submission
document.getElementById('preferencesForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-Token': formData.get('csrf_token')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Preferences saved successfully', 'success');
        } else {
            showAlert(data.message || 'Failed to save preferences', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while saving preferences', 'error');
    });
});

function enable2FA() {
    // This would typically open a modal or redirect to 2FA setup
    showAlert('Two-factor authentication setup coming soon!', 'info');
}
</script>
