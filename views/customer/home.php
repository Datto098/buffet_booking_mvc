<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    Chào Mừng Đến Với <?php echo SITE_NAME; ?>
                </h1>
                <p class="lead mb-4">
                    Trải nghiệm ẩm thực buffet đa dạng với hơn 100 món ăn từ khắp nơi trên thế giới.
                    Đặt bàn ngay hôm nay để có những khoảnh khắc tuyệt vời cùng gia đình và bạn bè.
                </p>                <div class="d-flex gap-3">
                    <a href="<?php echo SITE_URL; ?>/menu" class="btn btn-warning btn-lg">
                        <i class="fas fa-book-open"></i> Xem Thực Đơn
                    </a>
                    <a href="<?php echo SITE_URL; ?>/booking" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-calendar-check"></i> Đặt Bàn Ngay
                    </a>
                </div></div>
            <div class="col-lg-6">
                <img src="<?php echo SITE_URL; ?>/assets/images/hero-buffet.svg"
                     alt="Buffet Restaurant"
                     class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Danh Mục Món Ăn</h2>
            <p class="lead">Khám phá đa dạng các loại món ăn tại nhà hàng</p>
        </div>

        <div class="row g-4">
            <?php if (!empty($categories)): ?>
                <?php foreach (array_slice($categories, 0, 6) as $category): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="card h-100 shadow-sm border-0 category-card">
                            <div class="card-body text-center p-4">
                                <div class="category-icon mb-3">
                                    <i class="fas fa-utensils fa-3x text-primary"></i>
                                </div>
                                <h5 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h5>
                                <p class="card-text text-muted">
                                    <?php echo htmlspecialchars($category['description'] ?? 'Khám phá các món ăn trong danh mục này'); ?>
                                </p>                                <a href="<?php echo SITE_URL; ?>/menu/category/<?php echo $category['id']; ?>"
                                   class="btn btn-outline-primary">
                                    Xem Món Ăn
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
<section class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Món Ăn Nổi Bật</h2>
            <p class="lead">Những món ăn được yêu thích nhất tại nhà hàng</p>
        </div>

        <div class="row g-4">
            <?php if (!empty($featuredFoods)): ?>
                <?php foreach ($featuredFoods as $food): ?>                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card h-100 shadow-sm border-0 food-card">
                            <img src="<?php echo $food['image_url'] ? SITE_URL . '/uploads/food_images/' . $food['image_url'] : SITE_URL . '/assets/images/food-placeholder.svg'; ?>"
                                 class="card-img-top"
                                 alt="<?php echo htmlspecialchars($food['name']); ?>"
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title"><?php echo htmlspecialchars($food['name']); ?></h6>
                                <p class="card-text text-muted small flex-grow-1">
                                    <?php echo htmlspecialchars(substr($food['description'] ?? '', 0, 80)) . '...'; ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 text-primary mb-0">
                                        <?php echo number_format($food['price'], 0, ',', '.'); ?>đ
                                    </span>
                                    <div class="btn-group">                                        <a href="<?php echo SITE_URL; ?>/food/detail/<?php echo $food['id']; ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            Chi Tiết
                                        </a>
                                        <button class="btn btn-sm btn-primary add-to-cart"
                                                data-food-id="<?php echo $food['id']; ?>"
                                                data-food-name="<?php echo htmlspecialchars($food['name']); ?>"
                                                data-food-price="<?php echo $food['price']; ?>">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Chưa có món ăn nào được thêm vào hệ thống.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-5">            <a href="<?php echo SITE_URL; ?>/menu" class="btn btn-primary btn-lg">
                Xem Tất Cả Món Ăn <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Latest News -->
<?php if (!empty($latestNews)): ?>
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Tin Tức Mới Nhất</h2>
            <p class="lead">Cập nhật những thông tin mới nhất từ nhà hàng</p>
        </div>

        <div class="row g-4">
            <?php foreach ($latestNews as $news): ?>                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?php echo $news['image_url'] ? SITE_URL . '/uploads/news_images/' . $news['image_url'] : SITE_URL . '/assets/images/news-placeholder.svg'; ?>"
                             class="card-img-top"
                             alt="<?php echo htmlspecialchars($news['title']); ?>"
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars($news['title']); ?></h6>
                            <p class="card-text text-muted">
                                <?php echo htmlspecialchars(substr(strip_tags($news['content']), 0, 100)) . '...'; ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('d/m/Y', strtotime($news['created_at'])); ?>
                                </small>                                <a href="<?php echo SITE_URL; ?>/news/detail/<?php echo $news['id']; ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    Đọc Thêm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Call to Action -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-3">Sẵn Sàng Trải Nghiệm Buffet Tuyệt Vời?</h3>
                <p class="mb-0">Đặt bàn ngay hôm nay để nhận ưu đãi đặc biệt cho lần đầu tiên!</p>
            </div>
            <div class="col-lg-4 text-lg-end">                <a href="<?php echo SITE_URL; ?>/booking" class="btn btn-warning btn-lg">
                    <i class="fas fa-phone"></i> Đặt Bàn Ngay
                </a>
            </div>
        </div>
    </div>
</section>
