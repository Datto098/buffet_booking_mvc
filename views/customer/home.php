
<!-- Hero Section -->
<section class="hero-section hero-section--slider position-relative" id="hero-section">
    <div class="hero-bg" id="hero-bg"></div>
    <div class="container position-relative" style="z-index:2;">
        <div class="hero-content">
            <h1 class="hero-title " style="color: #fff">
                Trải Nghiệm Ẩm Thực <br>
                <span class="text-white">Đẳng Cấp Quốc Tế</span>
            </h1>
            <p class="hero-subtitle">
                Chào mừng bạn đến với không gian buffet sang trọng nhất thành phố.
                Hơn 200 món ăn tinh tế từ 5 châu lục, phục vụ trong không gian đẳng cấp
                với dịch vụ chuyên nghiệp và tận tâm.
            </p>
            <div class="hero-cta">
                <a href="<?php echo SITE_URL; ?>/menu" class="btn btn-luxury">
                    <i class="fas fa-crown"></i> Khám Phá Thực Đơn
                </a>
                <a href="<?php echo SITE_URL; ?>/booking" class="btn btn-outline-luxury" style="color: #fff;">
                    <i class="fas fa-concierge-bell"></i> Đặt Bàn VIP
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="section-luxury bg-luxury">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card-luxury p-4">
                    <i class="fas fa-utensils fa-3x text-gold mb-3"></i>
                    <h3 class="text-gold">200+</h3>
                    <p>Món Ăn Đa Dạng</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card-luxury p-4">
                    <i class="fas fa-users fa-3x text-gold mb-3"></i>
                    <h3 class="text-gold">10,000+</h3>
                    <p>Khách Hàng Hài Lòng</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card-luxury p-4">
                    <i class="fas fa-star fa-3x text-gold mb-3"></i>
                    <h3 class="text-gold">15+</h3>
                    <p>Năm Kinh Nghiệm</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card-luxury p-4">
                    <i class="fas fa-award fa-3x text-gold mb-3"></i>
                    <h3 class="text-gold">5 Sao</h3>
                    <p>Đánh Giá Chất Lượng</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="section-luxury">
    <div class="container">
        <div class="section-title">
            <h2>Ẩm Thực Đa Dạng</h2>
            <p class="section-subtitle">
                Từ các món Á đến Âu, từ hải sản tươi sống đến tráng miệng tinh tế,
                chúng tôi mang đến hành trình ẩm thực vòng quanh thế giới
            </p>
        </div>

        <div class="row g-4">
            <?php if (!empty($categories)): ?>
                <?php foreach (array_slice($categories, 0, 6) as $index => $category): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-luxury h-100" style="animation-delay: <?php echo $index * 0.1; ?>s">
                            <div class="card-body-luxury text-center">
                                <div class="mb-4">
                                    <i class="fas fa-<?php
                                        $icons = ['fish', 'pizza-slice', 'hamburger', 'ice-cream', 'wine-glass', 'cookie'];
                                        echo $icons[$index % count($icons)];
                                    ?> fa-4x text-gold"></i>
                                </div>
                                <h3 class="card-title-luxury"><?php echo htmlspecialchars($category['name']); ?></h3>
                                <p class="card-text-luxury">
                                    <?php echo htmlspecialchars($category['description'] ?? 'Khám phá hương vị độc đáo và tinh tế của danh mục này với các món ăn được chế biến bởi đầu bếp chuyên nghiệp'); ?>
                                </p>
                                <a href="<?php echo SITE_URL; ?>/menu/category/<?php echo $category['id']; ?>"
                                   class="btn btn-luxury">
                                    <i class="fas fa-arrow-right"></i> Khám Phá Ngay
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Featured Foods -->
<section class="section-luxury bg-luxury">
    <div class="container">
        <div class="section-title">
            <h2>Món Ăn Đặc Sắc</h2>
            <p class="section-subtitle">
                Những tinh hoa ẩm thực được tuyển chọn kỹ lưỡng, chế biến bởi
                đội ngũ đầu bếp hàng đầu với nguyên liệu cao cấp nhất
            </p>
        </div>

        <div class="food-grid">
            <?php if (!empty($featuredFoods)): ?>
                <?php foreach ($featuredFoods as $index => $food): ?>
                    <div class="food-item" style="animation-delay: <?php echo $index * 0.1; ?>s">
                        <div class="food-image">
                            <img src="<?php echo $food['image'] ? SITE_URL . '/uploads/food_images/' . $food['image'] : SITE_URL . '/assets/images/food-placeholder.svg'; ?>"
                                 alt="<?php echo htmlspecialchars($food['name']); ?>"
                                 class="card-img-luxury">
                            <div class="food-badge">
                                <i class="fas fa-star"></i> Đặc Biệt
                            </div>
                        </div>
                        <div class="food-content">
                            <div class="food-category">
                                <?php echo htmlspecialchars($food['category_name'] ?? 'Đặc sản'); ?>
                            </div>
                            <h3 class="food-title"><?php echo htmlspecialchars($food['name']); ?></h3>
                            <p class="food-description">
                                <?php echo htmlspecialchars(substr($food['description'] ?? 'Món ăn được chế biến tinh tế với nguyên liệu tươi ngon, mang đến hương vị khó quên.', 0, 120)) . '...'; ?>
                            </p>
                            <div class="food-price">
                                <span class="price-current">
                                    <?php echo number_format($food['price'], 0, ',', '.'); ?>đ
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?php echo SITE_URL; ?>/food/detail/<?php echo $food['id']; ?>"
                                   class="btn btn-outline-luxury flex-fill">
                                    <i class="fas fa-eye"></i> Chi Tiết
                                </a>
                                <button class="btn btn-luxury add-to-cart"
                                        data-food-id="<?php echo $food['id']; ?>"
                                        data-food-name="<?php echo htmlspecialchars($food['name']); ?>"
                                        data-food-price="<?php echo $food['price']; ?>"
                                        onclick="this.classList.add('pulse'); setTimeout(()=>this.classList.remove('pulse'),300)">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
  
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <div class="card-luxury p-5">
                        <i class="fas fa-utensils fa-3x text-gold mb-3"></i>
                        <h3>Đang Cập Nhật Thực Đơn</h3>
                        <p class="text-muted">Chúng tôi đang hoàn thiện những món ăn tuyệt vời nhất để phục vụ quý khách.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?php echo SITE_URL; ?>/menu" class="btn btn-luxury btn-lg">
                <i class="fas fa-crown"></i> Khám Phá Toàn Bộ Thực Đơn
            </a>
        </div>    </div>
</section>

<!-- Latest News -->
<?php if (!empty($latestNews)): ?>
<section class="section-luxury">
    <div class="container">
        <div class="section-title">
            <h2>Tin Tức & Sự Kiện</h2>
            <p class="section-subtitle">
                Cập nhật những thông tin mới nhất, sự kiện đặc biệt và
                các hoạt động thú vị tại nhà hàng
            </p>
        </div>

        <div class="row g-4">
            <?php foreach ($latestNews as $index => $news): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card-luxury h-100" style="animation-delay: <?php echo $index * 0.1; ?>s">
                        <div class="position-relative overflow-hidden" style="height: 250px;">
                            <?php if (!empty($news['image_url'])): ?>
                                <img src="<?php echo SITE_URL; ?>/uploads/news_images/<?php echo htmlspecialchars($news['image_url']); ?>"
                                     class="card-img-luxury" alt="<?php echo htmlspecialchars($news['title']); ?>">
                            <?php else: ?>
                                <img src="<?php echo SITE_URL; ?>/assets/images/news-placeholder.svg"
                                     class="card-img-luxury" alt="News Placeholder">
                            <?php endif; ?>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge" style="background: var(--primary-gold); color: white; padding: 0.5rem 1rem; border-radius: 20px;">
                                    <i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($news['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body-luxury">
                            <h3 class="card-title-luxury">
                                <a href="<?php echo SITE_URL; ?>/news/detail?id=<?php echo $news['id']; ?>"
                                   class="text-decoration-none" style="color: var(--text-primary);">
                                    <?php echo htmlspecialchars($news['title']); ?>
                                </a>
                            </h3>
                            <p class="card-text-luxury">
                                <?php
                                $excerpt = !empty($news['excerpt']) ? $news['excerpt'] : substr(strip_tags($news['content']), 0, 120) . '...';
                                echo htmlspecialchars($excerpt);
                                ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?php echo SITE_URL; ?>/news/detail?id=<?php echo $news['id']; ?>"
                                   class="btn btn-outline-luxury">
                                    <i class="fas fa-book-open"></i> Đọc Thêm
                                </a>
                                <div class="text-muted small">
                                    <i class="fas fa-user"></i> Admin
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?php echo SITE_URL; ?>/news" class="btn btn-luxury btn-lg">
                <i class="fas fa-newspaper"></i> Xem Tất Cả Tin Tức
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Call to Action Section -->
<section class="section-luxury" style="background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-navy-light) 100%); color: white;">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="text-luxury mb-4">Sẵn Sàng Trải Nghiệm?</h2>
                <p class="lead mb-5" style="color: rgba(255,255,255,0.9);">
                    Hãy đặt bàn ngay hôm nay để thưởng thức bữa tiệc buffet đẳng cấp
                    trong không gian sang trọng với dịch vụ chuyên nghiệp nhất.
                </p>
                <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                    <a href="<?php echo SITE_URL; ?>/booking" class="btn btn-luxury btn-lg">
                        <i class="fas fa-concierge-bell"></i> Đặt Bàn Ngay
                    </a>
                    <a href="<?php echo SITE_URL; ?>/menu" class="btn btn-outline-luxury btn-lg">
                        <i class="fas fa-crown"></i> Xem Thực Đơn
                    </a>
                    <a href="<?php echo SITE_URL; ?>/about" class="btn btn-outline-luxury btn-lg">
                        <i class="fas fa-info-circle"></i> Về Chúng Tôi
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
