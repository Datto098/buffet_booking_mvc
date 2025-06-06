<?php
define('SITE_NAME', 'Buffet Booking');
define('SITE_URL', 'http://localhost:8080');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct CSS Test - <?php echo SITE_NAME; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Luxury CSS -->
    <link href="<?php echo SITE_URL; ?>/assets/css/luxury-style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                <i class="fas fa-crown me-2"></i>
                <span class="brand-text"><?php echo SITE_NAME; ?></span>
            </a>

            <!-- Mobile Menu Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>">
                            <i class="fas fa-home"></i>
                            <span class="nav-text">Trang Chủ</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#menu">
                            <i class="fas fa-utensils"></i>
                            <span class="nav-text">Thực Đơn</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="infoDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-info-circle"></i>
                            <span class="nav-text">Thông Tin</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#about">Về Chúng Tôi</a></li>
                            <li><a class="dropdown-item" href="#news">Tin Tức</a></li>
                            <li><a class="dropdown-item" href="#contact">Liên Hệ</a></li>
                        </ul>
                    </li>
                </ul>

                <!-- Right Side Navigation -->
                <ul class="navbar-nav">
                    <!-- Search -->
                    <li class="nav-item">
                        <button class="nav-link btn-search" data-bs-toggle="modal" data-bs-target="#searchModal">
                            <i class="fas fa-search"></i>
                        </button>
                    </li>

                    <!-- Cart -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="#cart">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-badge">0</span>
                        </a>
                    </li>

                    <!-- Booking Button -->
                    <li class="nav-item ms-2">
                        <a href="#booking" class="btn btn-booking">
                            <i class="fas fa-concierge-bell"></i>
                            <span>Đặt Bàn</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main style="margin-top: 100px;">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="hero-content">
                    <h1 class="hero-title text-luxury">
                        Trải Nghiệm Ẩm Thực <br>
                        <span class="text-white">Đẳng Cấp Quốc Tế</span>
                    </h1>
                    <p class="hero-subtitle">
                        Chào mừng bạn đến với không gian buffet sang trọng nhất thành phố.
                        Hơn 200 món ăn tinh tế từ 5 châu lục, phục vụ trong không gian đẳng cấp
                        với dịch vụ chuyên nghiệp và tận tâm.
                    </p>
                    <div class="hero-cta">
                        <a href="#menu" class="btn btn-luxury">
                            <i class="fas fa-crown"></i> Khám Phá Thực Đơn
                        </a>
                        <a href="#booking" class="btn btn-outline-luxury">
                            <i class="fas fa-concierge-bell"></i> Đặt Bàn VIP
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="section-luxury bg-luxury">
            <div class="container">
                <div class="row text-center">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card-luxury p-4">
                            <div class="luxury-icon-wrapper mb-3">
                                <i class="fas fa-crown luxury-icon"></i>
                            </div>
                            <h4 class="luxury-title">Dịch Vụ VIP</h4>
                            <p class="text-muted">Trải nghiệm dịch vụ 5 sao với đội ngũ nhân viên chuyên nghiệp</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card-luxury p-4">
                            <div class="luxury-icon-wrapper mb-3">
                                <i class="fas fa-utensils luxury-icon"></i>
                            </div>
                            <h4 class="luxury-title">Ẩm Thực Đa Dạng</h4>
                            <p class="text-muted">Hơn 200 món ăn từ khắp năm châu với hương vị độc đáo</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card-luxury p-4">
                            <div class="luxury-icon-wrapper mb-3">
                                <i class="fas fa-gem luxury-icon"></i>
                            </div>
                            <h4 class="luxury-title">Không Gian Sang Trọng</h4>
                            <p class="text-muted">Thiết kế hiện đại, sang trọng với tầm nhìn panorama tuyệt đẹp</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card-luxury p-4">
                            <div class="luxury-icon-wrapper mb-3">
                                <i class="fas fa-star luxury-icon"></i>
                            </div>
                            <h4 class="luxury-title">Chất Lượng Premium</h4>
                            <p class="text-muted">Nguyên liệu tươi ngon nhập khẩu cao cấp từ các nước trên thế giới</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Test Section -->
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="card luxury-card">
                            <div class="card-body">
                                <h2 class="luxury-title text-center mb-4">CSS Test Results</h2>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>✅ Should Work:</h5>
                                        <ul>
                                            <li>Gold (#D4AF37) and Navy (#1B2951) colors</li>
                                            <li>Playfair Display font for headings</li>
                                            <li>Inter font for body text</li>
                                            <li>Smooth animations and hover effects</li>
                                            <li>Luxury buttons with gradients</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>🎯 Test Buttons:</h5>
                                        <button class="btn btn-luxury me-2 mb-2">Luxury Button</button>
                                        <button class="btn btn-outline-luxury me-2 mb-2">Outline Button</button>
                                        <button class="btn btn-booking mb-2">Booking Button</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Luxury Effects JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/luxury-effects.js"></script>
</body>
</html>
