<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '' ?>" href="/admin">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
            <span>Management</span>
        </h6>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : '' ?>" href="/admin/users">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/foods') !== false ? 'active' : '' ?>" href="/admin/foods">
                    <i class="fas fa-utensils"></i> Food Items
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/categories') !== false ? 'active' : '' ?>" href="/admin/categories">
                    <i class="fas fa-tags"></i> Categories
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
            <span>Operations</span>
        </h6>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/orders') !== false ? 'active' : '' ?>" href="/admin/orders">
                    <i class="fas fa-shopping-cart"></i> Orders
                    <?php if (isset($data['stats']['pending_orders']) && $data['stats']['pending_orders'] > 0): ?>
                        <span class="badge bg-warning text-dark ms-2"><?= $data['stats']['pending_orders'] ?></span>
                    <?php endif; ?>
                </a>
            </li>            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/bookings') !== false ? 'active' : '' ?>" href="/admin/bookings">
                    <i class="fas fa-calendar-check"></i> Bookings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/tables') !== false ? 'active' : '' ?>" href="/admin/tables">
                    <i class="fas fa-table"></i> Tables
                </a>
            </li>
        </ul>

        <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'super_admin'): ?>
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
            <span>Super Admin</span>
        </h6>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="/admin/reports">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/admin/settings">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/admin/backup">
                    <i class="fas fa-database"></i> Backup
                </a>
            </li>
        </ul>
        <?php endif; ?>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
            <span>System</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="/admin/logs">
                    <i class="fas fa-file-alt"></i> System Logs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Website
                </a>
            </li>
        </ul>
    </div>
</nav>
