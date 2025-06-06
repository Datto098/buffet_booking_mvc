<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? SITE_NAME; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                <i class="fas fa-utensils"></i> <?php echo SITE_NAME; ?>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>">
                            <i class="fas fa-home"></i> Trang Chủ
                        </a>
                    </li>                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/menu">
                            <i class="fas fa-book-open"></i> Thực Đơn
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/booking">
                            <i class="fas fa-calendar-check"></i> Đặt Bàn
                        </a>
                    </li>                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/promotions">
                            <i class="fas fa-percent"></i> Khuyến Mãi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/news">
                            <i class="fas fa-newspaper"></i> Tin Tức
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/about">
                            <i class="fas fa-info-circle"></i> Giới Thiệu
                        </a>
                    </li>
                </ul>                <ul class="navbar-nav">
                    <!-- Search Form -->
                    <li class="nav-item">
                        <form class="d-flex me-3" action="<?php echo SITE_URL; ?>/search" method="GET">
                            <input class="form-control me-2" type="search" name="q" placeholder="Tìm kiếm món ăn...">
                            <button class="btn btn-outline-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </li><!-- Cart -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?php echo SITE_URL; ?>/cart">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $_SESSION['cart_count'] ?? 0; ?>
                            </span>
                        </a>
                    </li>

                    <?php if (isLoggedIn()): ?>
                        <!-- User Menu -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo $_SESSION['user_name'] ?? 'Tài Khoản'; ?>
                            </a>
                            <ul class="dropdown-menu">                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/profile">
                                    <i class="fas fa-user-edit"></i> Hồ Sơ
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/order/history">
                                    <i class="fas fa-history"></i> Lịch Sử Đơn Hàng
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/addresses">
                                    <i class="fas fa-map-marker-alt"></i> Địa Chỉ
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <?php if (isAdmin() || isManager()): ?>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/">
                                        <i class="fas fa-cogs"></i> Quản Lý
                                    </a></li>
                                <?php endif; ?>                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/auth/logout">
                                    <i class="fas fa-sign-out-alt"></i> Đăng Xuất
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Login/Register -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/auth/login">
                                <i class="fas fa-sign-in-alt"></i> Đăng Nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/auth/register">
                                <i class="fas fa-user-plus"></i> Đăng Ký
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
