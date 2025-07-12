<?php
$title = "My Profile - " . " " . SITE_NAME;
$current_page = 'profile';
$active_tab = $active_tab ?? ($_GET['tab'] ?? 'profile-info');

?>

<div class="container my-5">
    <!-- Alert -->
    <div class="row">
        <div class="col-lg-4">
            <!-- Profile Sidebar -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="profile-avatar mb-3">
                        <?php if (!empty($data['user']['avatar'])): ?>
                            <img src="<?= SITE_URL ?>/assets/images/<?= $data['user']['avatar'] ?>"
                                alt="Profile Picture" class="rounded-circle" width="100" height="100" style="object-fit:contain;padding: 2px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
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
                    <a href="#profile-info" data-tab="profile-info" class="list-group-item list-group-item-action<?= $active_tab == 'profile-info' ? ' active' : '' ?>">
                        <i class="fas fa-user me-2"></i>Profile Information
                    </a>
                    <a href="#security" data-tab="security" class="list-group-item list-group-item-action<?= $active_tab == 'security' ? ' active' : '' ?>">
                        <i class="fas fa-lock me-2"></i>Security & Password
                    </a>
                    <a href="#order-history" data-tab="order-history" class="list-group-item list-group-item-action <?= $active_tab == 'order-history' ? 'active' : '' ?>">
                        <i class="fas fa-shopping-bag me-2"></i>Order History
                    </a>
                    <a href="#booking-history" data-tab="booking-history" class="list-group-item list-group-item-action <?= $active_tab == 'booking-history' ? 'active' : '' ?>">
                        <i class="fas fa-calendar me-2"></i>Booking History
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Profile Information Tab -->
            <div id="profile-info" class="tab-content<?= $active_tab == 'profile-info' ? ' active' : '' ?>">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form id="profileForm" method="POST" action="<?= SITE_URL ?>/user/update-profile" enctype="multipart/form-data">
                            <?= csrf_token_field() ?>
                            <div class="row g-3">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name"
                                            value="<?= htmlspecialchars($data['user']['first_name']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name"
                                            value="<?= htmlspecialchars($data['user']['last_name']) ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?= htmlspecialchars($data['user']['email']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="<?= htmlspecialchars($data['user']['phone'] ?? '') ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                        value="<?= $data['user']['date_of_birth'] ?? '' ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="address">Address</label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            id="address"
                                            name="address"
                                            class="form-control"
                                            placeholder="Nhập địa chỉ hoặc chọn trên bản đồ..."
                                            value="<?= htmlspecialchars($data['user']['address'] ?? '') ?>"
                                            autocomplete="off" />
                                        <button class="btn btn-outline-secondary" type="button" id="search-address">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                    <div id="map" style="width: 100%; height: 300px; margin-top: 10px;"></div>
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
        </div>



        <!-- Security Tab -->
        <div id="security" class="tab-content<?= $active_tab == 'security' ? ' active' : '' ?>">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>Security & Password
                    </h5>
                    <small class="text-muted">Quản lý mật khẩu và bảo mật tài khoản</small>
                </div>
                <div class="card-body">
                    <!-- Password Change Form -->
                    <div class="password-change-form">
                        <h6 class="mb-3">
                            <i class="fas fa-key me-2 text-primary"></i>Đổi mật khẩu
                        </h6>

                        <form id="passwordChangeForm" action="<?= SITE_URL ?>/user/change-password" method="post">
                            <?= csrf_token_field() ?>

                            <div class="mb-3">
                                <label for="currentPassword" class="form-label fw-bold">
                                    <i class="fas fa-lock me-2"></i>Mật khẩu hiện tại
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                                    <button class="btn btn-outline-secondary btn-toggle-password" type="button" onclick="togglePassword('currentPassword')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="newPassword" class="form-label fw-bold">
                                    <i class="fas fa-key me-2"></i>Mật khẩu mới
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                    <button class="btn btn-outline-secondary btn-toggle-password" type="button" onclick="togglePassword('newPassword')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1 text-primary"></i>
                                    Mật khẩu phải có ít nhất <?= PASSWORD_MIN_LENGTH ?> ký tự
                                </div>
                                <div class="password-strength mt-2" id="password-strength" style="display: none;">
                                    <div class="progress" style="height: 8px; border-radius: 4px;">
                                        <div class="progress-bar transition-all" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted mt-1 d-block"></small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="confirmPassword" class="form-label fw-bold">
                                    <i class="fas fa-check-circle me-2"></i>Xác nhận mật khẩu mới
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                                    <button class="btn btn-outline-secondary btn-toggle-password" type="button" onclick="togglePassword('confirmPassword')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-match mt-2" id="password-match" style="display: none;">
                                    <small class="text-danger">
                                        <i class="fas fa-times-circle me-1"></i>Mật khẩu xác nhận không khớp
                                    </small>
                                </div>
                            </div>

                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary btn-lg flex-fill" id="changePasswordBtn">
                                    <i class="fas fa-key me-2"></i>Đổi mật khẩu
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-lg" onclick="resetPasswordForm()">
                                    <i class="fas fa-undo me-2"></i>Đặt lại
                                </button>
                            </div>
                        </form>
                    </div>

                    <hr class="my-4">

                    <!-- Security Information -->
                    <div class="security-info">
                        <h6 class="mb-3">
                            <i class="fas fa-shield-alt me-2"></i>Thông tin bảo mật
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="security-icon me-3">
                                        <i class="fas fa-clock text-info"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted">Lần đăng nhập cuối</small>
                                        <div class="fw-bold"><?= date('d/m/Y H:i', strtotime($user['last_login'] ?? $user['created_at'] ?? '2025-01-01')) ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="security-icon me-3">
                                        <i class="fas fa-calendar text-success"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted">Ngày tạo tài khoản</small>
                                        <div class="fw-bold"><?= date('d/m/Y', strtotime($user['created_at'] ?? '2025-01-01')) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Tips -->
                    <div class="security-tips mt-4">
                        <h6 class="mb-3">
                            <i class="fas fa-lightbulb me-2"></i>Mẹo bảo mật
                        </h6>
                        <div class="alert alert-info">
                            <ul class="mb-0">
                                <li>Sử dụng mật khẩu mạnh và duy nhất cho tài khoản này</li>
                                <li>Không chia sẻ thông tin đăng nhập với người khác</li>
                                <li>Đăng xuất sau khi sử dụng trên thiết bị chung</li>
                                <li>Thay đổi mật khẩu định kỳ để đảm bảo an toàn</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order History Tab -->
        <div id="order-history" class="tab-content <?= $active_tab == 'order-history' ? 'active' : '' ?>">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="<?= SITE_URL ?>/index.php?page=order&action=history" class="btn btn-outline-primary btn-sm">
                        View All Orders
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($data['recent_orders'])): ?>
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
                                    <?php foreach ($data['recent_orders'] as $order): ?>
                                        <tr>
                                            <td>#<?= $order['id'] ?></td>
                                            <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                            <td><?= $order['total_items'] ?? $order['item_count'] ?? 0 ?></td>
                                            <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                            <td>
                                                <span class="badge badge-<?= strtolower($order['status']) ?>">
                                                    <?= ucfirst($order['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= SITE_URL ?>/index.php?page=order&action=detail&id=<?= $order['id'] ?>"
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
        <div id="booking-history" class="tab-content <?= $active_tab == 'booking-history' ? 'active' : '' ?>">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Bookings</h5>
                    <a href="<?= SITE_URL ?>/index.php?page=booking&action=history" class="btn btn-outline-primary btn-sm">
                        View All Bookings
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($data['recent_bookings'])): ?>
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
                                    <?php foreach ($data['recent_bookings'] as $booking): ?>
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
                                                <a href="<?= SITE_URL ?>/index.php?page=booking&action=detail&id=<?= $booking['id'] ?>"
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

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success'];
                                        unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error'];
                                    unset($_SESSION['error']); ?></div>
<?php endif; ?>

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
        display: none !important;
    }

    .tab-content.active {
        display: block !important;
    }

    .badge-pending {
        background-color: #ffc107;
        color: #000;
    }

    .badge-confirmed {
        background-color: #17a2b8;
        color: #fff;
    }

    .badge-preparing {
        background-color: #fd7e14;
        color: #fff;
    }

    .badge-ready {
        background-color: #20c997;
        color: #fff;
    }

    .badge-completed {
        background-color: #28a745;
        color: #fff;
    }

    .badge-cancelled {
        background-color: #dc3545;
        color: #fff;
    }

    /* Security Tab Styles */
    .security-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .password-strength {
        transition: all 0.3s ease;
    }

    .password-strength .progress {
        border-radius: 10px;
        overflow: hidden;
    }

    .password-strength .progress-bar {
        transition: all 0.3s ease;
    }

    .security-info .d-flex {
        padding: 15px;
        border-radius: 8px;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .security-info .d-flex:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .security-tips .alert {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border: 1px solid #bee5eb;
    }

    .input-group .btn-outline-secondary {
        border-left: none;
    }

    .input-group .form-control:focus+.btn-outline-secondary {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    #changePasswordForm .form-control {
        transition: all 0.3s ease;
    }

    #changePasswordForm .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        border-color: #86b7fe;
    }

    .password-match {
        transition: all 0.3s ease;
    }

    /* Button animations */
    #changePasswordBtn {
        transition: all 0.3s ease;
    }

    #changePasswordBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    /* Alert animations */
    .alert {
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<script>
    let map, marker;

    // Hàm khởi tạo map
    function initMap() {
        if (map) return; // Đã khởi tạo rồi thì thôi
        map = L.map('map').setView([10.762622, 106.660172], 13); // Tọa độ mặc định: Sài Gòn

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Nếu có địa chỉ cũ, geocode và đặt marker
        const address = document.getElementById('address').value;
        if (address) {
            fetch(`<?= SITE_URL ?>/controllers/GeoController.php?q=${encodeURIComponent(address)}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data[0]) {
                        const lat = data[0].lat,
                            lon = data[0].lon;
                        map.setView([lat, lon], 16);
                        marker = L.marker([lat, lon]).addTo(map);
                    }
                });
        }

        // Click trên bản đồ để chọn vị trí
        map.on('click', function(e) {
            if (marker) map.removeLayer(marker);
            marker = L.marker(e.latlng).addTo(map);
            // Reverse geocode
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.display_name) {
                        document.getElementById('address').value = data.display_name;
                    }
                });
        });

        // Tìm địa chỉ
        document.getElementById('search-address').onclick = function() {
            const addr = document.getElementById('address').value;
            if (!addr) return;
            fetch(`<?= SITE_URL ?>/controllers/GeoController.php?q=${encodeURIComponent(addr)}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data[0]) {
                        const lat = data[0].lat,
                            lon = data[0].lon;
                        map.setView([lat, lon], 16);
                        if (marker) map.removeLayer(marker);
                        marker = L.marker([lat, lon]).addTo(map);
                    }
                });
        };
    } // Khởi tạo map khi tab profile-info active
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded');

        // Activate tab based on URL parameter when page loads
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'profile-info';
        console.log('Active tab from URL:', activeTab);

        // Debug: Check if elements exist
        const tabLink = document.querySelector(`[data-tab="${activeTab}"]`);
        const tabContent = document.getElementById(activeTab);
        console.log('Tab link found:', tabLink);
        console.log('Tab content found:', tabContent);

        // Remove all active classes first
        document.querySelectorAll('[data-tab]').forEach(t => {
            console.log('Removing active from:', t.getAttribute('data-tab'));
            t.classList.remove('active');
        });
        document.querySelectorAll('.tab-content').forEach(c => {
            console.log('Removing active from content:', c.id);
            c.classList.remove('active');
        });

        // Activate the correct tab and content
        if (tabLink && tabContent) {
            console.log('Activating tab:', activeTab);
            tabLink.classList.add('active');
            tabContent.classList.add('active');
            tabContent.style.display = 'block'; // Force display
        } else {
            console.error('Tab link or content not found for:', activeTab);
        }

        // Initialize map if profile-info tab is active
        if (activeTab === 'profile-info') {
            setTimeout(initMap, 200);
        }

        // Add click handlers for tab switching
        document.querySelectorAll('[data-tab]').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('[data-tab]').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const tabId = this.getAttribute('data-tab');
                const tabContent = document.getElementById(tabId);
                if (tabContent) tabContent.classList.add('active');

                // Initialize map if switching to profile-info tab
                if (tabId === 'profile-info') {
                    setTimeout(initMap, 200);
                }

                // Update URL to maintain tab state on reload
                if (history.pushState) {
                    const url = new URL(window.location);
                    url.searchParams.set('tab', tabId);
                    history.replaceState(null, '', url);
                }
            });
        });
    });

    // Password change functionality
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const button = field.nextElementSibling;
        const icon = button.querySelector('i');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function checkPasswordStrength(password) {
        let score = 0;
        let feedback = '';

        if (password.length >= 8) score += 1;
        if (password.match(/[a-z]/)) score += 1;
        if (password.match(/[A-Z]/)) score += 1;
        if (password.match(/[0-9]/)) score += 1;
        if (password.match(/[^a-zA-Z0-9]/)) score += 1;

        const strengthElement = document.getElementById('password-strength');
        const progressBar = strengthElement.querySelector('.progress-bar');
        const feedbackElement = strengthElement.querySelector('small');

        strengthElement.style.display = 'block';

        switch (score) {
            case 0:
            case 1:
                progressBar.style.width = '20%';
                progressBar.className = 'progress-bar bg-danger';
                feedback = 'Rất yếu';
                break;
            case 2:
                progressBar.style.width = '40%';
                progressBar.className = 'progress-bar bg-warning';
                feedback = 'Yếu';
                break;
            case 3:
                progressBar.style.width = '60%';
                progressBar.className = 'progress-bar bg-info';
                feedback = 'Trung bình';
                break;
            case 4:
                progressBar.style.width = '80%';
                progressBar.className = 'progress-bar bg-primary';
                feedback = 'Mạnh';
                break;
            case 5:
                progressBar.style.width = '100%';
                progressBar.className = 'progress-bar bg-success';
                feedback = 'Rất mạnh';
                break;
        }

        feedbackElement.textContent = feedback;
        return score;
    }

    function checkPasswordMatch() {
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const matchElement = document.getElementById('password-match');

        if (confirmPassword.length > 0) {
            if (newPassword === confirmPassword) {
                matchElement.style.display = 'none';
                matchElement.innerHTML = '<small class="text-success"><i class="fas fa-check-circle me-1"></i>Mật khẩu khớp</small>';
                matchElement.style.display = 'block';
                return true;
            } else {
                matchElement.innerHTML = '<small class="text-danger"><i class="fas fa-times-circle me-1"></i>Mật khẩu xác nhận không khớp</small>';
                matchElement.style.display = 'block';
                return false;
            }
        } else {
            matchElement.style.display = 'none';
            return false;
        }
    }

    function resetPasswordForm() {
        document.getElementById('passwordChangeForm').reset();
        document.getElementById('password-strength').style.display = 'none';
        document.getElementById('password-match').style.display = 'none';
    }

    // Form validation and submission
    document.addEventListener('DOMContentLoaded', function() {
        const newPasswordField = document.getElementById('newPassword');
        const confirmPasswordField = document.getElementById('confirmPassword');
        const changePasswordForm = document.getElementById('passwordChangeForm');

        if (newPasswordField) {
            newPasswordField.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                if (confirmPasswordField.value) {
                    checkPasswordMatch();
                }
            });
        }

        if (confirmPasswordField) {
            confirmPasswordField.addEventListener('input', checkPasswordMatch);
        }

        if (changePasswordForm) {
            changePasswordForm.addEventListener('submit', function(e) {
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;

                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    showAlert('Mật khẩu xác nhận không khớp!', 'danger');
                    return false;
                }

                if (checkPasswordStrength(newPassword) < 3) {
                    e.preventDefault();
                    showAlert('Mật khẩu quá yếu! Vui lòng chọn mật khẩu mạnh hơn.', 'warning');
                    return false;
                }

                // Show loading state
                const submitBtn = document.getElementById('changePasswordBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
            });
        }
    });

    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        const container = document.querySelector('.container');
        container.insertBefore(alertDiv, container.firstChild);

        // Auto dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
</script>

<!-- CSRF token for forms -->
<input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

<script>
    // Enhanced form submission with better error handling
    document.addEventListener('DOMContentLoaded', function() {
        const passwordForm = document.getElementById('passwordChangeForm');

        if (passwordForm) {
            passwordForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const currentPassword = document.getElementById('currentPassword').value;
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;

                // Clear previous messages
                const alertContainer = document.getElementById('password-alert');
                if (alertContainer) {
                    alertContainer.remove();
                }

                // Validate form
                if (!currentPassword || !newPassword || !confirmPassword) {
                    showPasswordAlert('Vui lòng điền đầy đủ thông tin!', 'error');
                    return;
                }

                if (newPassword !== confirmPassword) {
                    showPasswordAlert('Mật khẩu xác nhận không khớp!', 'error');
                    return;
                }

                if (newPassword.length < 6) {
                    showPasswordAlert('Mật khẩu mới phải có ít nhất 6 ký tự!', 'error');
                    return;
                }

                // Submit form
                const formData = new FormData(passwordForm);

                fetch('<?= SITE_URL ?>/user/change-password', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (response.ok) {
                            showPasswordAlert('Đổi mật khẩu thành công! Đang chuyển hướng...', 'success');
                            setTimeout(() => {
                                window.location.href = '<?= SITE_URL ?>/user/profile?tab=security';
                            }, 2000);
                        } else {
                            return response.text().then(text => {
                                throw new Error('Server error: ' + response.status);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showPasswordAlert('Có lỗi xảy ra. Vui lòng thử lại!', 'error');
                    });
            });
        }

        function showPasswordAlert(message, type) {
            const alertHTML = `
                <div id="password-alert" class="alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show">
                    <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            passwordForm.insertAdjacentHTML('beforebegin', alertHTML);
        }
    });
</script>

<!-- Enhanced styling for password change form -->
<style>
    .password-change-form {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .password-strength {
        transition: all 0.3s ease;
    }

    .btn-toggle-password {
        transition: color 0.3s ease;
    }

    .btn-toggle-password:hover {
        color: #0d6efd !important;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }

    .alert {
        border: none;
        border-radius: 10px;
    }
</style>

<?php require_once 'views/layouts/footer.php'; ?>
