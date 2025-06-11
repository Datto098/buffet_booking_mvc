<?php
/**
 * News detail page view
 */
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <!-- <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb justify-content-center" style="background: rgba(255,255,255,0.1); border-radius: 30px; padding: 0.75rem 1.5rem;">
                    <li class="breadcrumb-item">
                        <a href="<?= SITE_URL ?>" style="color: rgba(255,255,255,0.8);">
                            <i class="fas fa-home"></i> Trang Chủ
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= SITE_URL ?>/news" style="color: rgba(255,255,255,0.8);">
                            Tin Tức
                        </a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--primary-gold);">Chi Tiết</li>
                </ol>
            </nav> -->

            <h1 class="hero-title text-center" style="color: #fff">
                <?php echo htmlspecialchars($news_item['title']); ?>
            </h1>

            <div class="news-meta-luxury text-center mt-4">
                <div class="d-flex justify-content-center align-items-center flex-wrap gap-4">
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt text-gold me-2"></i>
                        <span><?php echo date('d/m/Y', strtotime($news_item['created_at'])); ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-user-edit text-gold me-2"></i>
                        <span>
                            <?php
                            $authorName = (!empty($news_item['first_name']) && !empty($news_item['last_name']))
                                ? $news_item['first_name'] . ' ' . $news_item['last_name']
                                : 'Admin';
                            echo htmlspecialchars($authorName);
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- News Detail Content -->
<section class="section-luxury">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card-luxury p-5 mb-5">
                    <?php if (!empty($news_item['image_url'])): ?>
                        <div class="news-featured-image mb-5">
                            <img src="<?php echo SITE_URL; ?>/uploads/news_images/<?php echo htmlspecialchars($news_item['image_url']); ?>"
                                 class="img-fluid rounded shadow-luxury" alt="<?php echo htmlspecialchars($news_item['title']); ?>">
                        </div>
                    <?php endif; ?>

                    <div class="news-content-luxury">
                        <?php echo $news_item['content']; ?>
                    </div>
                </div>

                <!-- Back to News Link -->
                <div class="text-center mb-5">
                    <a href="<?php echo SITE_URL; ?>/news" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Quay lại danh sách tin tức
                    </a>
                </div>
            </div>
        </div>

                        <!-- Action Buttons -->
                <div class="news-actions text-center mt-5 pt-4 border-top border-gold">
                    <a href="<?= SITE_URL ?>/news" class="btn btn-luxury me-3">
                        <i class="fas fa-arrow-left me-2"></i> Về Trang Tin Tức
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-luxury">
                        <i class="fas fa-print me-2"></i> In Bài Viết
                    </button>
                </div>
            </div>
        </div>

        <!-- Related News -->
        <?php if (!empty($related_news)): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <div class="section-title">
                        <h3>Tin Tức Liên Quan</h3>
                        <p class="section-subtitle">Các bài viết khác có thể bạn quan tâm</p>
                    </div>
                    <div class="row g-4">
                        <?php foreach ($related_news as $item): ?>
                            <div class="col-md-4">
                                <div class="card-luxury h-100 news-card-small">
                                    <div class="position-relative overflow-hidden">
                                        <?php if (!empty($item['image_url'])): ?>
                                            <img src="<?php echo SITE_URL; ?>/uploads/news_images/<?php echo htmlspecialchars($item['image_url']); ?>"
                                                 class="related-news-image" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                        <?php else: ?>
                                            <img src="<?php echo SITE_URL; ?>/assets/images/news-placeholder.svg"
                                                 class="related-news-image" alt="News Placeholder">
                                        <?php endif; ?>
                                        <div class="news-overlay-small"></div>
                                    </div>
                                    <div class="card-body p-3">
                                        <h6 class="card-title-luxury">
                                            <a href="<?php echo SITE_URL; ?>/news/detail?id=<?php echo $item['id']; ?>" class="text-decoration-none news-title-link">
                                                <?php echo htmlspecialchars($item['title']); ?>
                                            </a>
                                        </h6>
                                        <p class="card-text-luxury small mb-3">
                                            <?php
                                            $excerpt = !empty($item['excerpt']) ? $item['excerpt'] : substr(strip_tags($item['content']), 0, 80) . '...';
                                            echo htmlspecialchars($excerpt);
                                            ?>
                                        </p>
                                        <a href="<?php echo SITE_URL; ?>/news/detail?id=<?php echo $item['id']; ?>" class="btn btn-luxury btn-sm">
                                            Đọc thêm <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
    .hero-section {
        min-height: 400px;
        display: flex;
        align-items: center;
    }

    .news-meta-luxury .meta-item {
        background: rgba(255, 255, 255, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        border: 1px solid rgba(255, 215, 0, 0.3);
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.9rem;
    }

    .news-featured-image {
        text-align: center;
        position: relative;
    }

    .news-featured-image img {
        max-height: 400px;
        width: 100%;
        object-fit: cover;
        border-radius: 12px;
    }

    .news-content-luxury {
        font-size: 1.1rem;
        line-height: 1.8;
        /* color: rgba(255, 255, 255, 0.9); */
    }

    .news-content-luxury h1,
    .news-content-luxury h2,
    .news-content-luxury h3,
    .news-content-luxury h4,
    .news-content-luxury h5,
    .news-content-luxury h6 {
        color: var(--primary-gold);
        margin: 2rem 0 1rem 0;
    }

    .news-content-luxury p {
        margin-bottom: 1.5rem;
    }

    .news-content-luxury img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1rem 0;
    }

    .border-gold {
        border-color: rgba(255, 215, 0, 0.3) !important;
    }

    .btn-outline-luxury {
        background: transparent;
        border: 2px solid rgba(255, 215, 0, 0.5);
        color: var(--primary-gold);
        padding: 0.75rem 2rem;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
    }

    .btn-outline-luxury:hover {
        background: var(--primary-gold);
        color: #1a1a1a;
        border-color: var(--primary-gold);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
    }

    .news-card-small {
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 215, 0, 0.1);
    }

    .news-card-small:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        border-color: rgba(255, 215, 0, 0.3);
    }

    .related-news-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .news-card-small:hover .related-news-image {
        transform: scale(1.05);
    }

    .news-overlay-small {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(
            to bottom,
            transparent 0%,
            rgba(26, 26, 26, 0.3) 100%
        );
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .news-card-small:hover .news-overlay-small {
        opacity: 1;
    }

    @media (max-width: 768px) {
        .hero-section {
            min-height: 300px;
        }

        .hero-title {
            font-size: 2rem;
        }

        .news-meta-luxury .d-flex {
            flex-direction: column;
            gap: 0.5rem;
        }

        .news-featured-image img {
            max-height: 250px;
        }

        .news-content-luxury {
            font-size: 1rem;
        }

        .news-actions .btn {
            display: block;
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .news-actions .me-3 {
            margin-right: 0 !important;
        }

        .related-news-image {
            height: 120px;
        }
    }

    @media print {
        .news-actions,
        .hero-section nav,
        .section-title {
            display: none !important;
        }

        .hero-section {
            background: white !important;
            color: black !important;
            min-height: auto;
            padding: 1rem 0;
        }

        .card-luxury {
            background: white !important;
            color: black !important;
            box-shadow: none !important;
            border: 1px solid #ddd;
        }
    }
</style>
