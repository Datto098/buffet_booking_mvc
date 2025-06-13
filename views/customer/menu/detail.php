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
                        <img src="<?= !empty($food['image']) ? SITE_URL . '/uploads/food_images/' . htmlspecialchars($food['image']) : SITE_URL . '/assets/images/food-placeholder.svg' ?>"
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
                                    <?php
                                    $avg = isset($avgRating) ? floatval($avgRating) : 0;
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($avg >= $i) {
                                            echo '<i class="fas fa-star text-gold"></i>';
                                        } elseif ($avg >= $i - 0.5) {
                                            echo '<i class="fas fa-star-half-alt text-gold"></i>';
                                        } else {
                                            echo '<i class="far fa-star text-gold"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                                <span class="rating-text">
                                    (<?= number_format($avg, 1) ?>/5 - <?= $totalRating ?> đánh giá)
                                </span>
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
                <?php if (!empty($isReviewed)): ?>
                    <div style="color: #007bff; font-style: italic; margin-bottom: 16px;">
                        <i class="fas fa-info-circle me-1"></i>
                        Bạn đã đánh giá món ăn này rồi.
                    </div>
                <?php elseif (!empty($userOrdered)): ?>
                    <!-- Hiển thị form gửi đánh giá -->
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
                        Bạn cần đặt món này thành công mới có thể đánh giá.
                    </div>
                <?php endif; ?>
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
                        <div class="card-body d-flex align-items-start position-relative">
                            <!-- Avatar trái -->
                            <div class="me-3 flex-shrink-0">
                                <?php if (!empty($comment['avatar'])): ?>
                                    <img src="<?= SITE_URL ?>/uploads/food_images/<?= htmlspecialchars($comment['avatar']) ?>"
                                        alt="Profile Picture"
                                        class="rounded-circle border"
                                        width="56" height="56"
                                        style="object-fit:cover; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                                <?php else: ?>
                                    <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center"
                                        style="width:56px;height:56px;background:#f3f3f3;border:1px solid #eee;">
                                        <i class="fas fa-user fa-2x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- Nội dung bình luận -->
                            <div class="flex-grow-1 pe-5">
                                <div class="d-flex align-items-center mb-1">
                                    <strong class="me-2" style="font-size:1.1em;">
                                        <?= htmlspecialchars(trim(($comment['first_name'] ?? '') . ' ' . ($comment['last_name'] ?? ''))) ?: 'Ẩn danh' ?>
                                    </strong>
                                    <span>
                                        <?php
                                        $rating = isset($comment['rating']) ? floatval($comment['rating']) : 0;
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($rating >= $i) {
                                                echo '<i class="fas fa-star text-warning"></i>';
                                            } elseif ($rating >= $i - 0.5) {
                                                echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                            } else {
                                                echo '<i class="far fa-star text-warning"></i>';
                                            }
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="mb-1 text-muted" style="font-size:0.95em;">
                                    <?= isset($comment['created_at']) ? date('d/m/Y H:i', strtotime($comment['created_at'])) : '' ?>
                                </div>
                                <div class="mb-2">
                                    <?= isset($comment['comment']) ? nl2br(htmlspecialchars($comment['comment'])) : '' ?>
                                </div>
                                <!-- Ảnh bình luận -->
                                <div class="mb-2">
                                    <?php
                                    $photos = !empty($comment['photos']) ? json_decode($comment['photos'], true) : [];
                                    foreach ($photos as $photo) {
                                        echo '<img src="' . SITE_URL . '/assets/images/' . htmlspecialchars($photo) . '" style="max-width:100px;margin:4px 8px 4px 0;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,0.07);">';
                                    }
                                    ?>
                                </div>
                            </div>
                            <!-- Nút chức năng bên phải -->
                            <div class="comment-actions d-flex flex-row align-items-center justify-content-end position-absolute" style="top:16px; right:24px; min-width:110px; gap: 10px;">
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                                    <form method="post" action="<?= SITE_URL ?>/review/delete/<?= $comment['id'] ?>" onsubmit="return confirm('Bạn chắc chắn muốn xóa đánh giá này?');" style="display:inline;">
                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill mb-2" style="margin-top: 10px;">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                    <a href="javascript:void(0);"
                                        class="btn btn-warning btn-sm rounded-pill edit-review-btn"
                                        data-id="<?= $comment['id'] ?>"
                                        data-content="<?= htmlspecialchars($comment['comment'], ENT_QUOTES) ?>"
                                        data-rating="<?= $comment['rating'] ?>"
                                        data-photos='<?= htmlspecialchars($comment['photos'] ?? "[]", ENT_QUOTES) ?>'>
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                <?php endif; ?>
                                <?php
                                $liked = false;
                                if (isset($_SESSION['user_id'])) {
                                    $liked = $this->reviewModel->hasUserLiked($_SESSION['user_id'], $comment['id']);
                                }
                                ?>
                                <button type="button"
                                    class="btn btn-outline-primary btn-sm like-btn<?= $liked ? ' active' : '' ?>"
                                    data-review-id="<?= $comment['id'] ?>">
                                    <i class="fas fa-thumbs-up"></i>
                                    <span class="like-count"><?= (int)($comment['helpful_count'] ?? 0) ?></span>
                                </button>
                            </div>
                        </div>

                        <!-- Form sửa bình luận -->
                        <div class="edit-comment-form" id="edit-form-<?= $comment['id'] ?>" style="display:none;">
                            <form method="POST" action="<?= SITE_URL ?>/review/update/<?= $comment['id'] ?>" enctype="multipart/form-data">
                                <div class="mb-2">
                                    <label class="form-label mb-1">Sửa đánh giá:</label>
                                    <div class="star-rating-edit" style="font-size: 1.3rem;">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <input type="radio" id="edit-star<?= $i ?>-<?= $comment['id'] ?>" name="rate" value="<?= $i ?>" style="display:none;">
                                            <label for="edit-star<?= $i ?>-<?= $comment['id'] ?>" style="cursor:pointer;">
                                                <i class="fa-star far text-warning"></i>
                                            </label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <textarea name="content" class="form-control" rows="2" required></textarea>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Ảnh mới (tùy chọn):</label>
                                    <input type="file" name="photo[]" accept="image/*" class="form-control" multiple>
                                    <div class="mt-2">
                                        <?php
                                        $photos = !empty($comment['photos']) ? json_decode($comment['photos'], true) : [];
                                        foreach ($photos as $photo) {
                                            echo '<div style="display:inline-block;position:relative;margin-right:8px;">';
                                            echo '<img src="' . SITE_URL . '/assets/images/' . htmlspecialchars($photo) . '" style="max-width:80px;border-radius:6px;">';
                                            echo '<input type="checkbox" name="delete_photos[]" value="' . htmlspecialchars($photo) . '"> Xóa';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Lưu</button>
                                <button type="button" class="btn btn-secondary btn-sm cancel-edit-btn" data-comment-id="<?= $comment['id'] ?>">Hủy</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div>Chưa có đánh giá nào cho món ăn này.</div>
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
                                    <img src="<?= !empty($relatedFood['image']) ? SITE_URL . '/uploads/food_images/' . htmlspecialchars($relatedFood['image']) : SITE_URL . '/assets/images/food-placeholder.svg' ?>"
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
        .comment-actions {
            min-width: 110px;
            z-index: 2;
            gap: 10px;
        }

        .btn-warning {
            color: #fff;
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }

        @media (max-width: 768px) {
            .comment-actions {
                position: static !important;
                flex-direction: column !important;
                margin-top: 12px;
                min-width: 0;
                gap: 8px;
            }
        }

        .card-body {
            position: relative;
        }

        .food-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .like-btn.active {
            background: #0d6efd;
            color: #fff;
            border-color: #0d6efd;
        }

        /* Thêm vào <style> cuối file */
        .avatar-placeholder {
            background: #f3f3f3;
            border: 1px solid #eee;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-body .btn.like-btn.active {
            background: #0d6efd;
            color: #fff;
            border-color: #0d6efd;
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

        .ingredients,
        .allergens {
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
                console.log('Adding to cart:', {
                    foodId,
                    foodName,
                    foodPrice
                });

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
                }
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.like-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        alert('Vui lòng đăng nhập để like bình luận!');
                        return;
                    <?php endif; ?>
                    var reviewId = this.dataset.reviewId;
                    var likeCountSpan = this.querySelector('.like-count');
                    var button = this;
                    fetch('<?= SITE_URL ?>/review/like/' + reviewId, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (typeof data.like_count !== 'undefined') {
                                likeCountSpan.textContent = data.like_count;
                            }
                            if (data.liked) {
                                button.classList.add('active');
                            } else {
                                button.classList.remove('active');
                            }
                        });
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.edit-review-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var id = this.dataset.id;
                    var content = this.dataset.content;
                    var rating = this.dataset.rating;
                    var photos = [];
                    try {
                        photos = JSON.parse(this.dataset.photos);
                    } catch (e) {}

                    // Set form action
                    var form = document.getElementById('editReviewForm');
                    form.action = '<?= SITE_URL ?>/review/update/' + id;

                    // Set content
                    form.querySelector('textarea[name="content"]').value = content;

                    // Set rating
                    var stars = form.querySelectorAll('#edit-stars input[type=radio]');
                    stars.forEach(function(star) {
                        star.checked = (star.value == rating);
                        var icon = star.nextElementSibling.querySelector('i');
                        if (star.value <= rating) {
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                        } else {
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                        }
                        // Đổi màu khi chọn
                        star.addEventListener('change', function() {
                            stars.forEach(function(s) {
                                var ic = s.nextElementSibling.querySelector('i');
                                if (s.value <= star.value) {
                                    ic.classList.remove('far');
                                    ic.classList.add('fas');
                                } else {
                                    ic.classList.remove('fas');
                                    ic.classList.add('far');
                                }
                            });
                        });
                    });

                    // Hiện ảnh cũ
                    var oldPhotosDiv = document.getElementById('edit-old-photos');
                    oldPhotosDiv.innerHTML = '';
                    if (photos && photos.length) {
                        photos.forEach(function(photo) {
                            oldPhotosDiv.innerHTML += `
                        <div style="display:inline-block;position:relative;margin-right:8px;">
                            <img src="<?= SITE_URL ?>/assets/images/${photo}" style="max-width:80px;border-radius:6px;">
                            <input type="checkbox" name="delete_photos[]" value="${photo}"> Xóa
                        </div>
                    `;
                        });
                    }

                    // Show modal
                    var modal = new bootstrap.Modal(document.getElementById('editReviewModal'));
                    modal.show();
                });
            });
        });
    </script>

    <!-- Modal sửa bình luận -->
    <div class="modal fade" id="editReviewModal" tabindex="-1" aria-labelledby="editReviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="editReviewForm" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editReviewModalLabel">Sửa bình luận</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Số sao:</label>
                            <div id="edit-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <input type="radio" id="edit-star<?= $i ?>" name="rate" value="<?= $i ?>" style="display:none;">
                                    <label for="edit-star<?= $i ?>" style="cursor:pointer;">
                                        <i class="fa-star far text-warning"></i>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label>Nội dung:</label>
                            <textarea name="content" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="mb-2">
                            <label>Ảnh mới (tùy chọn):</label>
                            <input type="file" name="photo[]" accept="image/*" class="form-control" multiple>
                            <div id="edit-old-photos" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php else: ?>
    <!-- Hiển thị danh sách món ăn trong danh mục -->
    <section class="section-luxury">
        <div class="container">
            <h1 class="mb-4"><?= htmlspecialchars($category['name']) ?></h1>
            <?php if (!empty($category['description'])): ?>
                <p><?= htmlspecialchars($category['description']) ?></p>
            <?php endif; ?>

            <div class="row">
                <?php foreach ($foods as $item): ?> <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 food-card">
                            <img src="<?= !empty($item['image']) ? SITE_URL . '/uploads/food_images/' . htmlspecialchars($item['image']) : SITE_URL . '/assets/images/food-placeholder.svg' ?>"
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

<?php if (!empty($_SESSION['message'])): ?>
    <div class="alert alert-success"><?= $_SESSION['message'];
                                        unset($_SESSION['message']); ?></div>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error'];
                                    unset($_SESSION['error']); ?></div>
<?php endif; ?>
