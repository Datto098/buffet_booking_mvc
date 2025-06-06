<?php
/**
 * News listing page view
 */
?>

<!-- News Header Section -->
<section class="page-header bg-primary text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-4 fw-bold">Tin Tức</h1>
                <p class="lead">Cập nhật thông tin mới nhất về nhà hàng và ẩm thực</p>
            </div>
        </div>
    </div>
</section>

<!-- News Listing Section -->
<section class="py-5">
    <div class="container">
        <?php if (!empty($news)): ?>
            <div class="row g-4">
                <?php foreach ($news as $item): ?>
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
                                <div class="news-date position-absolute bottom-0 start-0 m-3 px-3 py-2 bg-primary text-white rounded">
                                    <?php echo date('d/m/Y', strtotime($item['created_at'])); ?>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <h5 class="card-title">
                                    <a href="<?php echo SITE_URL; ?>/news/detail?id=<?php echo $item['id']; ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted mb-3">
                                    <?php
                                    $excerpt = !empty($item['excerpt']) ? $item['excerpt'] : substr(strip_tags($item['content']), 0, 150) . '...';
                                    echo htmlspecialchars($excerpt);
                                    ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="author-info">
                                        <small class="text-muted">
                                            <i class="fas fa-user-edit"></i>
                                            <?php
                                            $authorName = (!empty($item['first_name']) && !empty($item['last_name']))
                                                ? $item['first_name'] . ' ' . $item['last_name']
                                                : 'Admin';
                                            echo htmlspecialchars($authorName);
                                            ?>
                                        </small>
                                    </div>
                                    <a href="<?php echo SITE_URL; ?>/news/detail?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        Đọc thêm <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="row mt-5">
                    <div class="col-12">
                        <nav aria-label="News pagination">
                            <ul class="pagination justify-content-center">
                                <?php if ($current_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo SITE_URL; ?>/news?page_num=<?php echo $current_page - 1; ?>">
                                            <i class="fas fa-chevron-left"></i> Trước
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo SITE_URL; ?>/news?page_num=<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($current_page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo SITE_URL; ?>/news?page_num=<?php echo $current_page + 1; ?>">
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
            <div class="row">
                <div class="col-12 text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                        <h3>Chưa có tin tức nào</h3>
                        <p>Chúng tôi sẽ cập nhật thông tin sớm nhất.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
