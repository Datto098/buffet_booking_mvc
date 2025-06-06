<?php
// Demo Page - Showcase hoàn chỉnh luxury design
define('SITE_NAME', 'Buffet Booking');
define('SITE_URL', 'http://localhost:8080');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Design Demo - <?php echo SITE_NAME; ?></title>

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

    <style>
        body {
            padding-top: 80px;
            background: var(--bg-secondary, #FAF7F0);
        }
        .demo-section {
            padding: 4rem 0;
            margin: 2rem 0;
        }
        .demo-title {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            color: var(--primary-navy, #1B2951);
            text-align: center;
            margin-bottom: 3rem;
        }
    </style>
</head>
<body>
    <!-- Luxury Navigation -->
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

            <!-- Navigation -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">
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
                    <li class="nav-item">
                        <a class="nav-link" href="#promotions">
                            <i class="fas fa-percent"></i>
                            <span class="nav-text">Khuyến Mãi</span>
                        </a>
                    </li>
                </ul>

                <!-- Right Side Navigation -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button class="nav-link btn-search" data-bs-toggle="modal" data-bs-target="#searchModal">
                            <i class="fas fa-search"></i>
                        </button>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="#cart">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-badge">3</span>
                        </a>
                    </li>
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title text-luxury">
                    Luxury Design Demo <br>
                    <span class="text-white">Trải Nghiệm Đẳng Cấp</span>
                </h1>
                <p class="hero-subtitle">
                    Chào mừng bạn đến với trang demo thiết kế luxury buffet restaurant.
                    Trang này showcase tất cả các element CSS đã được thiết kế với gold & navy theme.
                </p>
                <div class="hero-cta">
                    <a href="#features" class="btn btn-luxury">
                        <i class="fas fa-crown"></i> Khám Phá Features
                    </a>
                    <a href="#components" class="btn btn-outline-luxury">
                        <i class="fas fa-palette"></i> Xem Components
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="demo-section bg-luxury">
        <div class="container">
            <h2 class="demo-title">🎨 Luxury Features</h2>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card-luxury p-4 text-center">
                        <div class="luxury-icon-wrapper mb-3">
                            <i class="fas fa-crown luxury-icon"></i>
                        </div>
                        <h4 class="luxury-title">Premium Design</h4>
                        <p class="text-muted">Gold & Navy color scheme với gradient effects</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card-luxury p-4 text-center">
                        <div class="luxury-icon-wrapper mb-3">
                            <i class="fas fa-palette luxury-icon"></i>
                        </div>
                        <h4 class="luxury-title">Modern Typography</h4>
                        <p class="text-muted">Playfair Display + Inter fonts combination</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card-luxury p-4 text-center">
                        <div class="luxury-icon-wrapper mb-3">
                            <i class="fas fa-mobile-alt luxury-icon"></i>
                        </div>
                        <h4 class="luxury-title">Responsive Layout</h4>
                        <p class="text-muted">Mobile-first design với smooth breakpoints</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card-luxury p-4 text-center">
                        <div class="luxury-icon-wrapper mb-3">
                            <i class="fas fa-magic luxury-icon"></i>
                        </div>
                        <h4 class="luxury-title">Interactive Effects</h4>
                        <p class="text-muted">Hover effects, animations, và transitions</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Components Demo -->
    <section id="components" class="demo-section">
        <div class="container">
            <h2 class="demo-title">🧩 UI Components</h2>

            <!-- Buttons -->
            <div class="row mb-5">
                <div class="col-12">
                    <h3 class="luxury-title mb-4">Buttons</h3>
                    <div class="d-flex flex-wrap gap-3">
                        <button class="btn btn-luxury">
                            <i class="fas fa-star"></i> Primary Luxury
                        </button>
                        <button class="btn btn-outline-luxury">
                            <i class="fas fa-heart"></i> Outline Luxury
                        </button>
                        <button class="btn btn-booking">
                            <i class="fas fa-concierge-bell"></i> Booking Button
                        </button>
                        <button class="btn btn-luxury btn-lg">
                            <i class="fas fa-crown"></i> Large Button
                        </button>
                        <button class="btn btn-outline-luxury btn-sm">
                            <i class="fas fa-gem"></i> Small Button
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cards -->
            <div class="row mb-5">
                <div class="col-12">
                    <h3 class="luxury-title mb-4">Cards</h3>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card luxury-card">
                        <div class="card-body">
                            <h5 class="luxury-title">Standard Luxury Card</h5>
                            <p class="text-muted">Basic luxury card với subtle shadows và border radius.</p>
                            <button class="btn btn-luxury btn-sm">Action</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card luxury-card border-luxury">
                        <div class="card-body text-center">
                            <i class="fas fa-utensils luxury-icon mb-3"></i>
                            <h5 class="luxury-title">Icon Card</h5>
                            <p class="text-muted">Card với icon và center alignment.</p>
                            <button class="btn btn-outline-luxury btn-sm">Learn More</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card luxury-card card-hover">
                        <div class="card-body">
                            <h5 class="luxury-title">Hover Effect Card</h5>
                            <p class="text-muted">Card với interactive hover animations.</p>
                            <button class="btn btn-booking btn-sm">Book Now</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Typography -->
            <div class="row mb-5">
                <div class="col-12">
                    <h3 class="luxury-title mb-4">Typography</h3>
                    <h1 class="luxury-title">H1 Luxury Heading</h1>
                    <h2 class="luxury-title">H2 Luxury Heading</h2>
                    <h3 class="luxury-title">H3 Luxury Heading</h3>
                    <p class="lead">Lead paragraph với Inter font family - elegant và modern.</p>
                    <p>Regular paragraph text với perfect line spacing và readability.</p>
                    <p class="text-muted">Muted text cho secondary information.</p>
                </div>
            </div>

            <!-- Colors Demo -->
            <div class="row">
                <div class="col-12">
                    <h3 class="luxury-title mb-4">Color Palette</h3>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="p-3 text-center" style="background: var(--primary-gold); color: white; border-radius: var(--radius-md);">
                        <strong>Primary Gold</strong><br>
                        <small>#D4AF37</small>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="p-3 text-center" style="background: var(--primary-navy); color: white; border-radius: var(--radius-md);">
                        <strong>Primary Navy</strong><br>
                        <small>#1B2951</small>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="p-3 text-center" style="background: var(--accent-copper); color: white; border-radius: var(--radius-md);">
                        <strong>Accent Copper</strong><br>
                        <small>#CD7F32</small>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="p-3 text-center" style="background: var(--neutral-cream); color: var(--text-primary); border-radius: var(--radius-md); border: 1px solid var(--primary-gold);">
                        <strong>Neutral Cream</strong><br>
                        <small>#FAF7F0</small>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="p-3 text-center" style="background: var(--neutral-pearl); color: var(--text-primary); border-radius: var(--radius-md); border: 1px solid var(--primary-gold);">
                        <strong>Neutral Pearl</strong><br>
                        <small>#F5F2E8</small>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="p-3 text-center" style="background: var(--neutral-charcoal); color: white; border-radius: var(--radius-md);">
                        <strong>Neutral Charcoal</strong><br>
                        <small>#36454F</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="luxury-title text-white">
                        <i class="fas fa-crown me-2"></i>
                        <?php echo SITE_NAME; ?>
                    </h5>
                    <p>Luxury Design Demo - Showcase thiết kế nhà hàng buffet cao cấp với gold & navy theme.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>© 2025 Luxury Buffet Restaurant Design</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Luxury Effects JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/luxury-effects.js"></script>

    <script>
        // Add some demo interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add ripple effect to buttons
            document.querySelectorAll('.btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);

                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
                    ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
                    ripple.classList.add('ripple');

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        });
    </script>
</body>
</html>
