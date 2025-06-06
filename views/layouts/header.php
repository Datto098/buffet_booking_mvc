<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? SITE_NAME; ?></title>

    <!-- Meta Tags for SEO -->
    <meta name="description" content="<?php echo $meta_description ?? 'Nhà hàng buffet sang trọng với không gian đẳng cấp và thực đơn phong phú'; ?>">
    <meta name="keywords" content="nhà hàng buffet, buffet cao cấp, ẩm thực, đặt bàn, khuyến mãi">    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Luxury CSS -->
    <link href="<?php echo SITE_URL; ?>/assets/css/luxury-style.css" rel="stylesheet">
    <!-- Promotion Styles -->
    <link href="<?php echo SITE_URL; ?>/assets/css/promotion-styles.css" rel="stylesheet">
    <!-- Custom CSS (fallback) -->
    <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                <i class="fas fa-crown me-2"></i>
                <span class="brand-text"><?php echo SITE_NAME; ?></span>
            </a>

            <!-- Mobile Menu Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Main Navigation -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>">
                            <i class="fas fa-home"></i>
                            <span class="nav-text">Trang Chủ</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/menu">
                            <i class="fas fa-utensils"></i>
                            <span class="nav-text">Thực Đơn</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/promotions">
                            <i class="fas fa-tags"></i>
                            <span class="nav-text">Khuyến Mãi</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarInfo" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-info-circle"></i>
                            <span class="nav-text">Thông Tin</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-luxury">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/about">
                                <i class="fas fa-building me-2"></i>Giới Thiệu
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/news">
                                <i class="fas fa-newspaper me-2"></i>Tin Tức
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/contact">
                                <i class="fas fa-phone me-2"></i>Liên Hệ
                            </a></li>
                        </ul>
                    </li>
                </ul>

                <!-- Right Side Actions -->
                <ul class="navbar-nav navbar-actions">
                    <!-- Search -->
                    <li class="nav-item">
                        <button class="nav-link btn-search" type="button" data-bs-toggle="modal" data-bs-target="#searchModal">
                            <i class="fas fa-search"></i>
                            <span class="nav-text d-lg-none">Tìm Kiếm</span>
                        </button>
                    </li>

                    <!-- Cart -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?php echo SITE_URL; ?>/cart">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="nav-text d-lg-none">Giỏ Hàng</span>
                            <span id="cart-count" class="cart-badge">
                                <?php echo $_SESSION['cart_count'] ?? 0; ?>
                            </span>
                        </a>
                    </li>

                    <!-- Booking Button -->
                    <li class="nav-item">
                        <a class="btn btn-booking" href="<?php echo SITE_URL; ?>/booking">
                            <i class="fas fa-calendar-check me-1"></i>
                            <span>Đặt Bàn</span>
                        </a>
                    </li>

                    <?php if (isLoggedIn()): ?>
                        <!-- User Menu -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle user-menu" href="#" id="navbarUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i>
                                <span class="nav-text"><?php echo $_SESSION['user_name'] ?? 'Tài Khoản'; ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-luxury">
                                <li class="dropdown-header">
                                    <i class="fas fa-user me-2"></i>
                                    <?php echo $_SESSION['user_name'] ?? 'Tài Khoản'; ?>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/profile">
                                    <i class="fas fa-user-edit me-2"></i>Hồ Sơ Cá Nhân
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/order/history">
                                    <i class="fas fa-history me-2"></i>Lịch Sử Đơn Hàng
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/addresses">
                                    <i class="fas fa-map-marker-alt me-2"></i>Địa Chỉ Giao Hàng
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <?php if (isAdmin() || isManager()): ?>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/">
                                        <i class="fas fa-cogs me-2"></i>Bảng Điều Khiển
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>/auth/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng Xuất
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Login/Register -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarAuth" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i>
                                <span class="nav-text">Tài Khoản</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-luxury">
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/auth/login">
                                    <i class="fas fa-sign-in-alt me-2"></i>Đăng Nhập
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/auth/register">
                                    <i class="fas fa-user-plus me-2"></i>Đăng Ký Tài Khoản
                                </a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="searchModalLabel">
                        <i class="fas fa-search me-2 text-gold"></i>Tìm Kiếm Món Ăn
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo SITE_URL; ?>/search" method="GET">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" name="q" placeholder="Nhập tên món ăn hoặc danh mục..." autocomplete="off" id="searchInput">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search me-2"></i>Tìm Kiếm
                            </button>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-lightbulb me-1"></i>
                                Gợi ý: pizza, burger, sushi, tráng miệng, đồ uống...
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
