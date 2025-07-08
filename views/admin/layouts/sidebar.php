<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-3 sidebar-sticky">
        <?php $requestUri = $_SERVER['REQUEST_URI'] ?? ''; ?>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $requestUri === '/admin' || $requestUri === '/admin/' ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
        </ul>        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-uppercase">
            <span>Management</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link <?= strpos($requestUri, '/admin/users') !== false ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/users">
                    <i class="fas fa-users me-2"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($requestUri, '/admin/foods') !== false ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/foods">
                    <i class="fas fa-utensils me-2"></i> Food Items
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($requestUri, '/admin/categories') !== false ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/categories">
                    <i class="fas fa-tags me-2"></i> Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($requestUri, '/admin/news') !== false ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/news">
                    <i class="fas fa-newspaper me-2"></i> News
                </a>
            </li>
        </ul>        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-uppercase">
            <span>Operations</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link <?= strpos($requestUri, '/admin/orders') !== false ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/orders">
                    <i class="fas fa-shopping-cart me-2"></i> Orders
                    <?php if (isset($data['stats']['pending_orders']) && $data['stats']['pending_orders'] > 0): ?>
                        <span class="badge bg-warning text-dark ms-2"><?= $data['stats']['pending_orders'] ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($requestUri, '/admin/bookings') !== false ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/bookings">
                    <i class="fas fa-calendar-check me-2"></i> Bookings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($requestUri, '/admin/tables') !== false ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/tables">
                    <i class="fas fa-table me-2"></i> Tables
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($requestUri, '/admin/internal-messages') !== false ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/internal-messages">
                    <i class="fas fa-envelope me-2"></i> Internal Messages
                    <?php
                    // Get unread count for internal messages
                    if (isset($_SESSION['user']['id'])) {
                        require_once __DIR__ . '/../../../models/InternalMessage.php';
                        $internalMessageModel = new InternalMessage();
                        $unreadCount = $internalMessageModel->getUnreadCount($_SESSION['user']['id']);
                        if ($unreadCount > 0):
                    ?>
                        <span class="badge bg-danger ms-2"><?= $unreadCount ?></span>
                    <?php
                        endif;
                    }
                    ?>
                </a>
            </li>
        </ul> <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'super_admin'): ?>
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-uppercase">
                <span>Super Admin</span>
            </h6>
            <ul class="nav flex-column mb-2">
                <li class="nav-item">
                    <a class="nav-link <?= strpos($requestUri, '/admin/reports') !== false ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/reports">
                        <i class="fas fa-chart-bar me-2"></i> Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($requestUri, '/admin/settings') !== false ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/settings">
                        <i class="fas fa-cog me-2"></i> Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($requestUri, '/admin/backup') !== false ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/backup">
                        <i class="fas fa-database me-2"></i> Backup
                    </a>
                </li>
            </ul>
        <?php endif; ?> <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-uppercase">
            <span>System</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link <?= strpos($requestUri, '/admin/logs') !== false ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/logs">
                    <i class="fas fa-file-alt me-2"></i> System Logs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= SITE_URL ?>/" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i> View Website
                </a>
            </li>
        </ul>
    </div>
</nav>
