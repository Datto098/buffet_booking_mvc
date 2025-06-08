<?php
// Header will be included by BaseController loadView method
// require_once 'views/customer/layouts/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb justify-content-center" style="background: rgba(255,255,255,0.1); border-radius: 30px; padding: 0.75rem 1.5rem;">
                    <li class="breadcrumb-item">
                        <a href="<?= SITE_URL ?>" style="color: rgba(255,255,255,0.8);">
                            <i class="fas fa-home"></i> Trang Chủ
                        </a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--primary-gold);">Giới Thiệu</li>
                </ol>
            </nav>

            <h1 class="hero-title">
                 <span  style="color: var(--text-primary);">Câu Chuyện</span> <br>
                <span class="text-white">Của Chúng Tôi</span>
            </h1>
            <p class="hero-subtitle">
                Hành trình 15 năm xây dựng và phát triển thương hiệu buffet
                cao cấp hàng đầu với tầm nhìn mang ẩm thực thế giới đến với mọi người
            </p>
        </div>
    </div>
</section>

<!-- Restaurant Story -->
<section class="section-luxury">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6">
                <div class="card-luxury p-5">
                    <div class="section-title text-start">
                        <h2>Khởi Nguồn Từ Đam Mê</h2>
                        <p class="section-subtitle text-start">
                            Được thành lập vào năm 2010 bởi đầu bếp Michelin Star,
                            <?= SITE_NAME ?> bắt đầu từ một ý tưởng đơn giản: mang đến
                            trải nghiệm ẩm thực buffet đẳng cấp quốc tế.
                        </p>
                    </div>

                    <div class="row text-center mb-4">
                        <div class="col-6">
                            <div class="p-3">
                                <h3 class="text-gold">15+</h3>
                                <p class="mb-0">Năm Kinh Nghiệm</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3">
                                <h3 class="text-gold">50+</h3>
                                <p class="mb-0">Đầu Bếp Chuyên Nghiệp</p>
                            </div>
                        </div>
                    </div>

                    <p class="mb-4">
                        Chúng tôi tin rằng ẩm thực không chỉ là thức ăn, mà còn là nghệ thuật,
                        là cầu nối văn hóa và là trải nghiệm đáng nhớ. Với đội ngũ đầu bếp
                        được đào tạo bài bản tại các trường ẩm thực hàng đầu thế giới.
                    </p>

                    <a href="#vision" class="btn btn-luxury">
                        <i class="fas fa-arrow-down"></i> Tìm Hiểu Thêm
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="<?= SITE_URL ?>/assets/images/nha-hang-buffet.jpg"
                         alt="Restaurant Interior"
                         class="img-fluid rounded shadow-luxury">
                    <div class="position-absolute top-0 end-0 m-3">
                        <div class="bg-white rounded-circle p-3 shadow">
                            <i class="fas fa-crown fa-2x text-gold"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vision & Mission -->
<section class="section-luxury bg-luxury" id="vision">
    <div class="container">
        <div class="section-title">
            <h2>Tầm Nhìn & Sứ Mệnh</h2>
            <p class="section-subtitle">
                Những giá trị cốt lõi định hướng mọi hoạt động của chúng tôi
            </p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card-luxury h-100 text-center p-4">
                    <div class="mb-4">
                        <i class="fas fa-eye fa-4x text-gold"></i>
                    </div>
                    <h3 class="card-title-luxury">Tầm Nhìn</h3>
                    <p class="card-text-luxury">
                        Trở thành chuỗi nhà hàng buffet cao cấp hàng đầu Việt Nam,
                        mang đến trải nghiệm ẩm thực đẳng cấp quốc tế với dịch vụ hoàn hảo.
                    </p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-luxury h-100 text-center p-4">
                    <div class="mb-4">
                        <i class="fas fa-heart fa-4x text-gold"></i>
                    </div>
                    <h3 class="card-title-luxury">Sứ Mệnh</h3>
                    <p class="card-text-luxury">
                        Tạo ra những khoảnh khắc đáng nhớ thông qua ẩm thực tinh tế,
                        kết nối mọi người và lan tỏa niềm vui trong từng bữa ăn.
                    </p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-luxury h-100 text-center p-4">
                    <div class="mb-4">
                        <i class="fas fa-gem fa-4x text-gold"></i>
                    </div>
                    <h3 class="card-title-luxury">Giá Trị</h3>
                    <p class="card-text-luxury">
                        Chất lượng, sáng tạo, trách nhiệm và tận tâm - những giá trị
                        làm nên sự khác biệt trong từng món ăn và dịch vụ.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Restaurant Information -->
<section class="section-luxury">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Our Story Continued -->
                                </div>
                                <div class="col-md-6 mt-3 mt-md-0">
                                    <p class="lead">
                                        <?= SITE_NAME ?> ra đời từ niềm đam mê mang đến những trải nghiệm ẩm thực buffet đáng nhớ cho mọi gia đình Việt Nam.
                                    </p>
                                    <p>
                                        Chúng tôi tin rằng bữa ăn không chỉ là việc no bụng, mà còn là khoảnh khắc kết nối, chia sẻ và tạo nên những kỷ niệm đẹp cùng người thân yêu.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Mission & Vision -->
                <article class="blog-post mb-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <span class="badge bg-success mb-2">Sứ Mệnh & Tầm Nhìn</span>
                                <h2 class="h1 fw-bold text-dark">Cam Kết Của Chúng Tôi</h2>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="text-center">
                                        <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-bullseye fa-2x text-white"></i>
                                        </div>
                                        <h4 class="fw-bold">Sứ Mệnh</h4>
                                        <p class="text-muted">
                                            Mang đến trải nghiệm ẩm thực buffet chất lượng cao với dịch vụ tận tâm,
                                            giúp mọi khách hàng có những khoảnh khắc đáng nhớ bên gia đình và bạn bè.
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="text-center">
                                        <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-eye fa-2x text-white"></i>
                                        </div>
                                        <h4 class="fw-bold">Tầm Nhìn</h4>
                                        <p class="text-muted">
                                            Trở thành chuỗi nhà hàng buffet hàng đầu Việt Nam,
                                            được khách hàng tin tưởng và lựa chọn cho những dịp đặc biệt.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Values -->
                <article class="blog-post mb-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <span class="badge bg-warning mb-2">Giá Trị Cốt Lõi</span>
                                <h2 class="h1 fw-bold text-dark">Những Điều Chúng Tôi Tin Tưởng</h2>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <div class="text-center">
                                        <i class="fas fa-heart fa-3x text-danger mb-3"></i>
                                        <h5 class="fw-bold">Tận Tâm</h5>
                                        <p class="text-muted small">
                                            Phục vụ khách hàng bằng tất cả sự chân thành và nhiệt tình
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="text-center">
                                        <i class="fas fa-star fa-3x text-warning mb-3"></i>
                                        <h5 class="fw-bold">Chất Lượng</h5>
                                        <p class="text-muted small">
                                            Cam kết về chất lượng thực phẩm và dịch vụ hàng đầu
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="text-center">
                                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                        <h5 class="fw-bold">Cộng Đồng</h5>
                                        <p class="text-muted small">
                                            Tạo nên không gian kết nối cho mọi gia đình
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Team Section -->
                <article class="blog-post mb-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <span class="badge bg-dark mb-2">Đội Ngũ Của Chúng Tôi</span>
                                <h2 class="h1 fw-bold text-dark">Những Người Tạo Nên Sự Khác Biệt</h2>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <div class="text-center">
                                        <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                            <i class="fas fa-user-tie fa-3x text-primary"></i>
                                        </div>
                                        <h5 class="fw-bold">Đầu Bếp Chuyên Nghiệp</h5>
                                        <p class="text-muted small">
                                            Đội ngũ đầu bếp với nhiều năm kinh nghiệm, đam mê sáng tạo món ăn
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="text-center">
                                        <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                            <i class="fas fa-concierge-bell fa-3x text-success"></i>
                                        </div>
                                        <h5 class="fw-bold">Nhân Viên Phục Vụ</h5>
                                        <p class="text-muted small">
                                            Đội ngũ phục vụ nhiệt tình, chu đáo và chuyên nghiệp
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="text-center">
                                        <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                            <i class="fas fa-clipboard-check fa-3x text-info"></i>
                                        </div>
                                        <h5 class="fw-bold">Quản Lý Chất Lượng</h5>
                                        <p class="text-muted small">
                                            Đảm bảo tiêu chuẩn vệ sinh và chất lượng thực phẩm cao nhất
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Sidebar -->
          <div class="row p-4">
  <div class="col-lg-4">
    <!-- Contact Info -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông Tin Liên Hệ</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <h6 class="fw-bold"><i class="fas fa-map-marker-alt text-danger me-2"></i>Địa Chỉ</h6>
          <p class="text-muted mb-0"><?= isset($restaurantInfo['address']) ? htmlspecialchars($restaurantInfo['address']) : '123 Đường ABC, Quận XYZ, TP.HCM' ?></p>
        </div>
        <div class="mb-3">
          <h6 class="fw-bold"><i class="fas fa-phone text-success me-2"></i>Điện Thoại</h6>
          <a href="tel:<?= isset($restaurantInfo['phone']) ? htmlspecialchars($restaurantInfo['phone']) : '0123456789' ?>" class="text-decoration-none">
            <?= isset($restaurantInfo['phone']) ? htmlspecialchars($restaurantInfo['phone']) : '0123-456-789' ?>
          </a>
        </div>
        <div class="mb-3">
          <h6 class="fw-bold"><i class="fas fa-envelope text-primary me-2"></i>Email</h6>
          <a href="mailto:<?= isset($restaurantInfo['email']) ? htmlspecialchars($restaurantInfo['email']) : ADMIN_EMAIL ?>" class="text-decoration-none">
            <?= isset($restaurantInfo['email']) ? htmlspecialchars($restaurantInfo['email']) : ADMIN_EMAIL ?>
          </a>
        </div>
        <div class="mb-3">
          <h6 class="fw-bold"><i class="fas fa-clock text-warning me-2"></i>Giờ Mở Cửa</h6>
          <p class="text-muted mb-1">Thứ 2 - Thứ 6: 10:00 - 22:00</p>
          <p class="text-muted mb-0">Thứ 7 - Chủ Nhật: 09:00 - 23:00</p>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <!-- Quick Stats -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0 text-white"><i class="fas fa-chart-bar me-2"></i>Thống Kê</h5>
      </div>
      <div class="card-body">
        <div class="row text-center">
          <div class="col-6 mb-3">
            <div class="bg-light rounded p-3">
              <h3 class="text-primary fw-bold mb-1">5+</h3>
              <small class="text-muted">Năm Kinh Nghiệm</small>
            </div>
          </div>
          <div class="col-6 mb-3">
            <div class="bg-light rounded p-3">
              <h3 class="text-success fw-bold mb-1">100+</h3>
              <small class="text-muted">Món Ăn Đa Dạng</small>
            </div>
          </div>
          <div class="col-6">
            <div class="bg-light rounded p-3">
              <h3 class="text-warning fw-bold mb-1">10K+</h3>
              <small class="text-muted">Khách Hàng Hài Lòng</small>
            </div>
          </div>
          <div class="col-6">
            <div class="bg-light rounded p-3">
              <h3 class="text-info fw-bold mb-1">300</h3>
              <small class="text-muted">Chỗ Ngồi</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <!-- Follow Us -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-dark text-white">
        <h5 class="mb-0 text-white"><i class="fas fa-share-alt me-2 text-white"></i>Theo Dõi Chúng Tôi</h5>
      </div>
      <div class="card-body text-center">
        <a href="#" class="btn btn-primary btn-sm me-2 mb-2">
          <i class="fab fa-facebook-f"></i> Facebook
        </a>
        <a href="#" class="btn btn-info btn-sm me-2 mb-2">
          <i class="fab fa-instagram"></i> Instagram
        </a>
        <a href="#" class="btn btn-danger btn-sm mb-2">
          <i class="fab fa-youtube"></i> YouTube
        </a>
      </div>
    </div>
  </div>
</div>

        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Sẵn Sàng Trải Nghiệm?</h3>
                <p class="mb-0">Đặt bàn ngay hôm nay để thưởng thức buffet chất lượng cao cùng gia đình!</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= SITE_URL ?>/booking" class="btn btn-warning btn-lg fw-bold">
                    <i class="fas fa-calendar-plus me-2"></i>Đặt Bàn Ngay
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.blog-post .card {
    transition: transform 0.3s ease;
}

.blog-post .card:hover {
    transform: translateY(-5px);
}

.hero-section {
    min-height: 400px;
    display: flex;
    align-items: center;
}

@media (max-width: 768px) {
    .hero-section {
        min-height: 300px;
    }

    .display-3 {
        font-size: 2.5rem;
    }
}
</style>

<?php
// Footer will be included by BaseController loadView method
// require_once 'views/customer/layouts/footer.php';
?>
