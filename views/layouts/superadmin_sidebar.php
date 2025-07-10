<nav id="sidebar" class="sidebar">
    <div class="sidebar-sticky">
        <div class="sidebar-header">
            <h4>
                <i class="fas fa-crown"></i>
                Super Admin
            </h4>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="<?= SITE_URL ?>/superadmin/">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
        </ul>

        <div class="sidebar-heading">
            Management
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="<?= SITE_URL ?>/superadmin/users">
                    <i class="fas fa-users"></i>
                    User Management
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#ordersSubmenu" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="fas fa-shopping-cart"></i>
                    Orders & Bookings
                    <i class="fas fa-caret-down ms-auto"></i>
                </a>
                <div class="collapse" id="ordersSubmenu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= SITE_URL ?>/superadmin/orders">
                                <i class="fas fa-receipt"></i>
                                Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= SITE_URL ?>/superadmin/bookings">
                                <i class="fas fa-calendar-alt"></i>
                                Bookings
                            </a>
                        </li>
                    </ul>
                </div>
            </li>            <li class="nav-item">
                <a class="nav-link" href="<?= SITE_URL ?>/superadmin/tables">
                    <i class="fas fa-chair"></i>
                    Table Management
                </a>
            </li>            <li class="nav-item">
                <a class="nav-link" href="<?= SITE_URL ?>/superadmin/reviews">
                    <i class="fas fa-star"></i>
                    Reviews Management
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?= SITE_URL ?>/superadmin/notifications">
                    <i class="fas fa-bell"></i>
                    Notifications
                    <span class="badge bg-danger notification-badge" style="display: none; margin-left: 10px;"></span>
                </a>
            </li>
        </ul><div class="sidebar-heading">
            Configuration
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="#restaurantSubmenu" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="fas fa-utensils"></i>
                    Restaurant
                    <i class="fas fa-caret-down ms-auto"></i>
                </a>
                <div class="collapse" id="restaurantSubmenu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= SITE_URL ?>/superadmin/restaurant">
                                <i class="fas fa-info-circle"></i>
                                Restaurant Info
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= SITE_URL ?>/superadmin/promotions">
                                <i class="fas fa-tags"></i>
                                Promotions
                            </a>
                        </li>
                         <li class="nav-item">
                    <a class="nav-link" href="<?= SITE_URL ?>/superadmin/address">
                        <i class="fas fa-map-marker-alt"></i>
                        Address
                    </a>
                </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?= SITE_URL ?>/superadmin/statistics">
                    <i class="fas fa-chart-line"></i>
                    Statistics & Reports
                </a>
            </li>
        </ul>

        <div class="sidebar-heading">
            System
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="<?= SITE_URL ?>/" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    View Website
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?= SITE_URL ?>/admin" target="_blank">
                    <i class="fas fa-cog"></i>
                    Regular Admin
                </a>
            </li>
        </ul>        <div class="user-info">
            <div class="d-flex align-items-center">
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['user_name'] ?? 'SA', 0, 2)) ?>
                </div>
                <div class="flex-grow-1">
                    <div class="text-white fw-semibold"><?= $_SESSION['user_name'] ?? 'Super Admin' ?></div>
                    <div class="text-white-50 small"><?= $_SESSION['user_email'] ?? 'superadmin@buffet.com' ?></div>
                </div>
            </div>
            <div class="mt-3">
                <a href="<?= SITE_URL ?>/auth/logout" class="btn btn-outline-light btn-sm w-100">
                    <i class="fas fa-sign-out-alt me-1"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>
</nav>
