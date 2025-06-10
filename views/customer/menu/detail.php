<!-- Breadcrumb -->
<section class="breadcrumb-luxury" style="margin-top: 35px;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-luxury mb-0">
                <li class="breadcrumb-item">
                    <a href="<?= SITE_URL ?>" class="breadcrumb-link">
                        <i class="fas fa-home"></i> Trang Chủ
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= SITE_URL ?>/menu" class="breadcrumb-link">
                        <i class="fas fa-utensils"></i> Thực Đơn
                    </a>
                </li>
                <?php if (!empty($category)): ?>
                    <li class="breadcrumb-item">
                        <a href="<?= SITE_URL ?>/menu/category/<?= $category['id'] ?>" class="breadcrumb-link">
                            <?= htmlspecialchars($category['name']) ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (!empty($food)): ?>
                    <li class="breadcrumb-item active text-gold" aria-current="page">
                        <?= htmlspecialchars($food['name']) ?>
                    </li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
</section>

<!-- Food Detail Section -->
<?php if (!empty($food)): ?>
<section class="section-luxury">
    <div class="container">
        <div class="row align-items-start">
            <!-- Food Image and Gallery -->
            <div class="col-lg-6 mb-5">
                <div class="food-detail-image">
                    <img src="<?= !empty($food['image']) ? htmlspecialchars($food['image']) : SITE_URL . '/assets/images/food-placeholder.svg' ?>"
                         class="card-img-luxury food-main-image" alt="<?= htmlspecialchars($food['name']) ?>">

                    <!-- Luxury Badges -->
                    <div class="food-badges">
                        <?php if (!empty($food['is_popular'])): ?>
                            <span class="food-badge popular-badge">
                                <i class="fas fa-star"></i> Phổ Biến
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($food['is_new'])): ?>
                            <span class="food-badge new-badge">
                                <i class="fas fa-sparkles"></i> Mới
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($food['is_seasonal'])): ?>
                            <span class="food-badge seasonal-badge">
                                <i class="fas fa-leaf"></i> Theo Mùa
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Favorite Button -->
                    <button class="btn btn-luxury-circle favorite-btn"
                            data-food-id="<?= $food['id'] ?>">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
            </div>

            <!-- Food Information -->
            <div class="col-lg-6">
                <div class="food-detail-content">
                    <!-- Category Badge -->
                    <?php if (!empty($category)): ?>
                        <div class="food-category mb-3">
                            <i class="fas fa-tag text-gold"></i> <?= htmlspecialchars($category['name']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Food Name -->
                    <h1 class="food-detail-title"><?= htmlspecialchars($food['name']) ?></h1>

                    <!-- Rating Section -->
                    <div class="rating-section mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rating-stars">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="fas fa-star text-gold"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-text">(4.8/5 - 156 đánh giá)</span>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="food-price-detail">
                        <span class="price-current-large">
                            <?= number_format($food['price'], 0, ',', '.') ?>đ
                        </span>
                        <div class="price-note">
                            <i class="fas fa-info-circle text-gold"></i>
                            Giá đã bao gồm VAT và phí phục vụ
                        </div>
                    </div>

                    <!-- Description -->
                    <?php if (!empty($food['description'])): ?>
                        <div class="food-description-detail">
                            <h5 class="text-luxury">
                                <i class="fas fa-scroll text-gold"></i> Mô Tả Món Ăn
                            </h5>
                            <p class="description-text"><?= nl2br(htmlspecialchars($food['description'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Food Details Grid -->
                    <div class="row g-3 mb-4">
                        <!-- Spice Level -->
                        <?php if (!empty($food['spice_level']) && $food['spice_level'] !== 'none'): ?>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <strong><i class="fas fa-pepper-hot text-danger"></i> Độ Cay:</strong>
                                    <span class="ms-2">
                                        <?php
                                        $spiceLevels = [
                                            'mild' => 'Nhẹ',
                                            'medium' => 'Vừa',
                                            'hot' => 'Cay',
                                            'very_hot' => 'Rất Cay'
                                        ];
                                        echo $spiceLevels[$food['spice_level']] ?? 'Không xác định';
                                        ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Preparation Time -->
                        <?php if (!empty($food['preparation_time'])): ?>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <strong><i class="fas fa-clock text-info"></i> Thời Gian:</strong>
                                    <span class="ms-2"><?= $food['preparation_time'] ?> phút</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Dietary Information -->
                    <?php if (!empty($food['dietary_info'])): ?>
                        <?php $dietaryInfo = json_decode($food['dietary_info'], true); ?>
                        <?php if (is_array($dietaryInfo)): ?>
                            <div class="dietary-info mb-4">
                                <h6>Thông Tin Dinh Dưỡng:</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php if (!empty($dietaryInfo['vegetarian'])): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-leaf"></i> Chay
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($dietaryInfo['vegan'])): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-seedling"></i> Thuần Chay
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($dietaryInfo['gluten_free'])): ?>
                                        <span class="badge bg-info">
                                            <i class="fas fa-wheat"></i> Không Gluten
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Ingredients -->
                    <?php if (!empty($food['ingredients'])): ?>
                        <div class="ingredients mb-4">
                            <h6>Thành Phần:</h6>
                            <p class="text-muted small"><?= htmlspecialchars($food['ingredients']) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Allergens -->
                    <?php if (!empty($food['allergens'])): ?>
                        <div class="allergens mb-4">
                            <h6 class="text-warning">
                                <i class="fas fa-exclamation-triangle"></i> Cảnh Báo Dị Ứng:
                            </h6>
                            <p class="text-muted small"><?= htmlspecialchars($food['allergens']) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <div class="row g-2">
                            <div class="col-md-8">
                                <button class="btn btn-primary btn-lg w-100 add-to-cart-btn"
                                        data-food-id="<?= $food['id'] ?>"
                                        data-food-name="<?= htmlspecialchars($food['name']) ?>"
                                        data-food-price="<?= $food['price'] ?>">
                                    <i class="fas fa-shopping-cart"></i> Thêm Vào Giỏ Hàng
                                </button>
                            </div>
                            <div class="col-md-4">
                                <a href="<?= SITE_URL ?>/menu" class="btn btn-outline-secondary btn-lg w-100">
                                    <i class="fas fa-arrow-left"></i> Quay Lại
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Đánh giá & bình luận của khách hàng -->
<section class="mt-5">
    <div class="container">
        <h3 class="mb-4"><i class="fas fa-comments text-gold"></i> Đánh giá & Bình luận</h3>

        <!-- Form gửi bình luận mới -->
        <?php if (isset($_SESSION['user'])): ?>
        <form method="POST" action="<?= SITE_URL ?>/food/comment/<?= $food['id'] ?>" enctype="multipart/form-data" class="mb-4 card card-body shadow-sm">
            <div class="mb-2">
                <label class="form-label mb-1">Đánh giá của bạn:</label>
                <div class="star-rating" style="font-size: 1.5rem;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <input type="radio" id="star<?= $i ?>" name="rate" value="<?= $i ?>" required style="display:none;">
                        <label for="star<?= $i ?>" style="cursor:pointer;">
                            <i class="fa-star far text-warning"></i>
                        </label>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="mb-2">
                <textarea name="content" class="form-control" rows="2" placeholder="Nhận xét của bạn..." required></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">Ảnh (tùy chọn):</label>
                 <!-- Vùng hiển thị nhiều ảnh xem trước -->
                <div id="photo-preview-list" style="display:flex; gap:10px; margin-top:10px; flex-wrap:wrap;"></div>
                <!-- Cho phép chọn nhiều ảnh -->
                <input type="file" name="photo[]" accept="image/*" class="form-control" id="photo-input" multiple>
               
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Gửi đánh giá
            </button>
        </form>
        <script>
        // Đổi màu sao khi chọn
        document.querySelectorAll('.star-rating input[type=radio]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                let val = parseInt(this.value);
                document.querySelectorAll('.star-rating label i').forEach((star, idx) => {
                    if (idx < val) {
                        star.classList.remove('far');
                        star.classList.add('fas');
                    } else {
                        star.classList.remove('fas');
                        star.classList.add('far');
                    }
                });
            });
        });

        // Xem trước ảnh khi chọn file
        document.getElementById('photo-input').addEventListener('change', function(e) {
            const previewList = document.getElementById('photo-preview-list');
            previewList.innerHTML = ''; // Xóa các ảnh cũ
            const files = this.files;
            if (files.length > 0) {
                Array.from(files).forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            const img = document.createElement('img');
                            img.src = ev.target.result;
                            img.style.maxWidth = '120px';
                            img.style.marginRight = '8px';
                            img.style.marginBottom = '8px';
                            img.style.borderRadius = '8px';
                            previewList.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
        </script>
        <?php else: ?>
    <div style="font-style:italic; color: #856404; background: none; padding: 8px 0;">
        <i class="fas fa-info-circle me-1"></i>
        Vui lòng <a href="<?= SITE_URL ?>/login" style="color: #007bff; text-decoration: underline;">đăng nhập</a> để gửi đánh giá.
    </div>
<?php endif; ?>

        <!-- Danh sách bình luận -->
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body d-flex">
                        <!-- Ảnh user -->
                        <img src="<?= !empty($comment['photo']) ? htmlspecialchars($comment['photo']) : SITE_URL . '/assets/images/user-placeholder.png' ?>"
                             alt="avatar" class="rounded-circle me-3" width="48" height="48">
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <strong><?= htmlspecialchars($comment['username']) ?></strong>
                                <span class="ms-3 text-warning">
                                    <!-- Hiển thị số sao -->
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa-star <?= $i <= $comment['rate'] ? 'fas text-gold' : 'far text-muted' ?>"></i>
                                    <?php endfor; ?>
                                    <span class="ms-2 small text-muted"><?= number_format($comment['rate'], 1) ?>/5</span>
                                </span>
                            </div>
                            <div class="mb-1"><?= nl2br(htmlspecialchars($comment['content'])) ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
      <?php else: ?>
<div style="font-style:italic; color: #0c5460; background-color: #d1ecf1; border-radius: 4px; padding: 16px; margin-bottom: 50px;">
    <i class="fas fa-info-circle me-1"></i>Chưa có đánh giá nào cho món ăn này.
</div>
<?php endif; ?>
    </div>
</section>

<!-- Related Foods Section -->
<?php if (!empty($relatedFoods)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h3">Món Ăn Liên Quan</h2>
            <p class="text-muted">Khám phá thêm các món ăn khác trong cùng danh mục</p>
        </div>

        <div class="row">
            <?php foreach ($relatedFoods as $relatedFood): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 food-card">
                        <div class="position-relative">
                            <img src="<?= !empty($relatedFood['image']) ? htmlspecialchars($relatedFood['image']) : SITE_URL . '/assets/images/no-image.svg' ?>"
                                 class="card-img-top" alt="<?= htmlspecialchars($relatedFood['name']) ?>"
                                 style="height: 200px; object-fit: cover;">

                            <button class="btn btn-outline-light btn-sm position-absolute top-0 start-0 m-2 favorite-btn"
                                    data-food-id="<?= $relatedFood['id'] ?>">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($relatedFood['name']) ?></h5>
                            <p class="card-text flex-grow-1 text-muted">
                                <?= htmlspecialchars(substr($relatedFood['description'], 0, 100)) ?>
                                <?= strlen($relatedFood['description']) > 100 ? '...' : '' ?>
                            </p>

                            <div class="price-section mb-3">
                                <span class="h5 text-primary">
                                    <?= number_format($relatedFood['price'], 0, ',', '.') ?>đ
                                </span>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-grow-1 add-to-cart-btn"
                                        data-food-id="<?= $relatedFood['id'] ?>"
                                        data-food-name="<?= htmlspecialchars($relatedFood['name']) ?>"
                                        data-food-price="<?= $relatedFood['price'] ?>">
                                    <i class="fas fa-shopping-cart"></i> Thêm
                                </button>
                                <a href="<?= SITE_URL ?>/food/detail/<?= $relatedFood['id'] ?>"
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-eye"></i>
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



<!-- Custom CSS for this page -->
<style>
.food-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.food-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.detail-item {
    padding: 8px 0;
    border-bottom: 1px solid #f8f9fa;
}

.detail-item:last-child {
    border-bottom: none;
}

.price-section {
    font-weight: 600;
}

.dietary-info .badge {
    font-size: 0.75rem;
}

.ingredients, .allergens {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.allergens {
    border-left-color: #ffc107;
}

.favorite-btn {
    transition: all 0.2s ease;
}

.favorite-btn:hover {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.favorite-btn.active {
    background-color: #dc3545;
    color: white;
}

@media (max-width: 768px) {
    .action-buttons .row {
        flex-direction: column;
    }

    .action-buttons .col-md-8,
    .action-buttons .col-md-4 {
        width: 100%;
        margin-bottom: 10px;
    }
}
</style>

<!-- JavaScript for cart functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const foodId = this.dataset.foodId;
            const foodName = this.dataset.foodName;
            const foodPrice = this.dataset.foodPrice;

            // Add to cart logic here (you'll need to implement this based on your cart system)
            addToCart(foodId, foodName, foodPrice);
        });
    });

    // Favorite functionality
    document.querySelectorAll('.favorite-btn').forEach(button => {
        button.addEventListener('click', function() {
            const foodId = this.dataset.foodId;
            toggleFavorite(foodId, this);
        });
    });

    function addToCart(foodId, foodName, foodPrice) {
        // Implement your cart logic here
        // This might involve AJAX calls to your cart controller
        console.log('Adding to cart:', {foodId, foodName, foodPrice});

        // Show success message
        alert('Đã thêm ' + foodName + ' vào giỏ hàng!');
    }

    function toggleFavorite(foodId, button) {
        // Implement your favorite logic here
        // This might involve AJAX calls to your user controller
        const icon = button.querySelector('i');

        if (button.classList.contains('active')) {
            button.classList.remove('active');
            icon.classList.remove('fas');
            icon.classList.add('far');
        } else {
            button.classList.add('active');
            icon.classList.remove('far');
            icon.classList.add('fas');
        }    }
});
</script>
<?php else: ?>
    <!-- Hiển thị danh sách món ăn trong danh mục -->
    <section class="section-luxury">
        <div class="container">
            <h1 class="mb-4"><?= htmlspecialchars($category['name']) ?></h1>
            <?php if (!empty($category['description'])): ?>
                <p><?= htmlspecialchars($category['description']) ?></p>
            <?php endif; ?>

            <div class="row">
                <?php foreach ($foods as $item): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 food-card">
                            <img src="<?= !empty($item['image']) ? htmlspecialchars($item['image']) : SITE_URL . '/assets/images/no-image.svg' ?>"
                                 class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>"
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($item['name']) ?></h5>
                                <p class="card-text flex-grow-1 text-muted">
                                    <?= htmlspecialchars(substr($item['description'], 0, 100)) ?>
                                    <?= strlen($item['description']) > 100 ? '...' : '' ?>
                                </p>
                                <div class="price-section mb-3">
                                    <span class="h5 text-primary">
                                        <?= number_format($item['price'], 0, ',', '.') ?>đ
                                    </span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= SITE_URL ?>/food/detail/<?= $item['id'] ?>"
                                       class="btn btn-outline-primary flex-grow-1">
                                        <i class="fas fa-eye"></i> Xem chi tiết
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
