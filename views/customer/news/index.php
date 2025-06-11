<?php
/**
 * News listing page view
 */
?>

<!-- Hero Section -->
<section class="hero-section" style="height: 100vh">
    <div class="container">
        <div class="hero-content">

            <h1 class="hero-title">
                <span style="color: #fff;">Tin Tức</span> <br>
                <span class="text-white">& Sự Kiện</span>
            </h1>
            <p class="hero-subtitle">
                Cập nhật những thông tin mới nhất về nhà hàng, món ăn đặc sắc
                và các sự kiện hấp dẫn dành cho quý khách
            </p>
        </div>
    </div>
</section>

<!-- News Listing Section -->
<section class="section-luxury">
    <div class="container">
        <?php if (!empty($news)): ?>
            <div class="section-title">
                <h2>Tin Tức Mới Nhất</h2>
                <p class="section-subtitle">
                    Khám phá những câu chuyện thú vị về ẩm thực và hoạt động của nhà hàng
                </p>
            </div>

            <div class="row g-4">
                <?php foreach ($news as $item): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-luxury h-100 news-card-luxury">
                            <div class="position-relative overflow-hidden">
                                <?php if (!empty($item['image_url'])): ?>
                                    <img src="<?php echo SITE_URL; ?>/uploads/news_images/<?php echo htmlspecialchars($item['image_url']); ?>"
                                         class="news-image-luxury" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                <?php else: ?>
                                    <img src="<?php echo SITE_URL; ?>/assets/images/news-placeholder.svg"
                                         class="news-image-luxury" alt="News Placeholder">
                                <?php endif; ?>
                                <div class="news-date-luxury">
                                    <div class="date-card">
                                        <span class="day"><?php echo date('d', strtotime($item['created_at'])); ?></span>
                                        <span class="month"><?php echo date('M', strtotime($item['created_at'])); ?></span>
                                    </div>
                                </div>
                                <div class="news-overlay"></div>
                            </div>
                            <div class="card-body p-4">
                                <h5 class="card-title-luxury mb-3">
                                    <a href="<?php echo SITE_URL; ?>/news/detail?id=<?php echo $item['id']; ?>" class="text-decoration-none news-title-link">
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </a>
                                </h5>
                                <p class="card-text-luxury mb-4">
                                    <?php
                                    $excerpt = !empty($item['excerpt']) ? $item['excerpt'] : substr(strip_tags($item['content']), 0, 120) . '...';
                                    echo htmlspecialchars($excerpt);
                                    ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="author-info-luxury">
                                        <i class="fas fa-user-edit text-gold me-2"></i>
                                        <span class="text-muted">
                                            <?php
                                            $authorName = (!empty($item['first_name']) && !empty($item['last_name']))
                                                ? $item['first_name'] . ' ' . $item['last_name']
                                                : 'Admin';
                                            echo htmlspecialchars($authorName);
                                            ?>
                                        </span>
                                    </div>
                                    <a href="<?php echo SITE_URL; ?>/news/detail?id=<?php echo $item['id']; ?>" class="btn btn-luxury btn-sm">
                                        Đọc thêm <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="row mt-5">
                    <div class="col-12">
                        <nav aria-label="News pagination" class="d-flex justify-content-center">
                            <ul class="pagination-luxury">
                                <?php if ($current_page > 1): ?>
                                    <li class="page-item-luxury">
                                        <a class="page-link-luxury" href="<?php echo SITE_URL; ?>/news?page_num=<?php echo $current_page - 1; ?>">
                                            <i class="fas fa-chevron-left"></i> Trước
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item-luxury <?php echo $i === $current_page ? 'active' : ''; ?>">
                                        <a class="page-link-luxury" href="<?php echo SITE_URL; ?>/news?page_num=<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($current_page < $total_pages): ?>
                                    <li class="page-item-luxury">
                                        <a class="page-link-luxury" href="<?php echo SITE_URL; ?>/news?page_num=<?php echo $current_page + 1; ?>">
                                            Tiếp <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="section-luxury bg-luxury">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="empty-state-luxury text-center py-5">
                                <div class="icon-luxury mb-4">
                                    <i class="fas fa-newspaper fa-4x text-gold"></i>
                                </div>
                                <h3 class="card-title-luxury mb-3">Chưa có tin tức nào</h3>
                                <p class="card-text-luxury mb-4">
                                    Chúng tôi sẽ cập nhật những thông tin mới nhất sớm nhất có thể.
                                </p>
                                <a href="<?= SITE_URL ?>" class="btn btn-luxury">
                                    <i class="fas fa-home me-2"></i> Về Trang Chủ
                                </a>
                            </div>
                        </div>
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

    .news-card-luxury {
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 215, 0, 0.1);
        overflow: hidden;
    }

    .news-card-luxury:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border-color: rgba(255, 215, 0, 0.3);
    }

    .news-image-luxury {
        width: 100%;
        height: 250px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .news-card-luxury:hover .news-image-luxury {
        transform: scale(1.05);
    }

    .news-date-luxury {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 2;
    }

    .date-card {
        background: rgba(26, 26, 26, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 8px;
        padding: 0.75rem;
        text-align: center;
        color: white;
        min-width: 60px;
    }

    .date-card .day {
        display: block;
        font-size: 1.25rem;
        font-weight: bold;
        color: var(--primary-gold);
        line-height: 1;
    }

    .date-card .month {
        display: block;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 2px;
    }

    .news-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(
            to bottom,
            transparent 0%,
            transparent 60%,
            rgba(26, 26, 26, 0.7) 100%
        );
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .news-card-luxury:hover .news-overlay {
        opacity: 1;
    }

    .news-title-link {
        color: inherit !important;
        transition: color 0.3s ease;
    }

    .news-title-link:hover {
        color: var(--primary-gold) !important;
    }

    .author-info-luxury {
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.7);
    }

    .pagination-luxury {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 0.5rem;
    }

    .page-item-luxury.active .page-link-luxury {
        background: var(--primary-gold);
        border-color: var(--primary-gold);
        color: #1a1a1a;
    }

    .page-link-luxury {
        display: block;
        padding: 0.75rem 1rem;
        text-decoration: none;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 8px;
        color: white;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .page-link-luxury:hover {
        background: rgba(255, 215, 0, 0.2);
        border-color: rgba(255, 215, 0, 0.5);
        color: var(--primary-gold);
        transform: translateY(-2px);
    }

    .empty-state-luxury {
        min-height: 400px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    @media (max-width: 768px) {
        .hero-section {
            min-height: 300px;
        }

        .hero-title {
            font-size: 2.5rem;
        }

        .news-image-luxury {
            height: 200px;
        }

        .date-card {
            padding: 0.5rem;
            min-width: 50px;
        }

        .date-card .day {
            font-size: 1rem;
        }

        .pagination-luxury {
            flex-wrap: wrap;
            justify-content: center;
        }

        .page-link-luxury {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
    }
</style>
