<?php
/**
 * News detail page view
 */
?>

<!-- News Detail Header -->
<section class="page-header bg-primary text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($news_item['title']); ?></h1>
                <div class="d-flex justify-content-center align-items-center mt-3">
                    <div class="me-3">
                        <i class="fas fa-calendar-alt"></i>
                        <?php echo date('d/m/Y', strtotime($news_item['created_at'])); ?>
                    </div>
                    <div>
                        <i class="fas fa-user-edit"></i>
                        <?php
                        $authorName = (!empty($news_item['first_name']) && !empty($news_item['last_name']))
                            ? $news_item['first_name'] . ' ' . $news_item['last_name']
                            : 'Admin';
                        echo htmlspecialchars($authorName);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- News Detail Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <?php if (!empty($news_item['image_url'])): ?>
                            <div class="text-center mb-4">
                                <img src="<?php echo SITE_URL; ?>/uploads/news_images/<?php echo htmlspecialchars($news_item['image_url']); ?>"
                                     class="img-fluid rounded" alt="<?php echo htmlspecialchars($news_item['title']); ?>">
                            </div>
                        <?php endif; ?>

                        <div class="news-content">
                            <?php echo $news_item['content']; ?>
                        </div>
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

        <!-- Related News -->
        <?php if (!empty($related_news)): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="text-center mb-4">Tin Tức Liên Quan</h3>
                    <div class="row g-4">
                        <?php foreach ($related_news as $item): ?>
                            <div class="col-md-4">
                                <div class="card h-100 shadow-sm border-0 news-card">
                                    <div class="position-relative">
                                        <?php if (!empty($item['image_url'])): ?>
                                            <img src="<?php echo SITE_URL; ?>/uploads/news_images/<?php echo htmlspecialchars($item['image_url']); ?>"
                                                 class="card-img-top" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                        <?php else: ?>
                                            <img src="<?php echo SITE_URL; ?>/assets/images/news-placeholder.svg"
                                                 class="card-img-top" alt="News Placeholder">
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body p-3">
                                        <h5 class="card-title">
                                            <a href="<?php echo SITE_URL; ?>/news/detail?id=<?php echo $item['id']; ?>" class="text-decoration-none text-dark">
                                                <?php echo htmlspecialchars($item['title']); ?>
                                            </a>
                                        </h5>
                                        <p class="card-text text-muted small">
                                            <?php
                                            $excerpt = !empty($item['excerpt']) ? $item['excerpt'] : substr(strip_tags($item['content']), 0, 100) . '...';
                                            echo htmlspecialchars($excerpt);
                                            ?>
                                        </p>
                                        <div class="text-end">
                                            <a href="<?php echo SITE_URL; ?>/news/detail?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                Đọc thêm <i class="fas fa-arrow-right ms-1"></i>
                                            </a>
                                        </div>
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
