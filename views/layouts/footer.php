<?php if (!isset($info) && isset($data['info'])) $info = $data['info']; ?>
</main>
    <!-- Footer -->
    <footer class="footer-luxury">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-crown"></i> <?php echo htmlspecialchars($info['restaurant_name'] ?? SITE_NAME); ?></h3>
                    <p><?php echo htmlspecialchars($info['description'] ?? ''); ?></p>
                    <div class="social-links">
                        <?php if (!empty($info['facebook'])): ?>
                            <a href="<?php echo htmlspecialchars($info['facebook']); ?>" class="social-link" aria-label="Facebook" target="_blank">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($info['instagram'])): ?>
                            <a href="<?php echo htmlspecialchars($info['instagram']); ?>" class="social-link" aria-label="Instagram" target="_blank">
                                <i class="fab fa-instagram"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($info['twitter'])): ?>
                            <a href="<?php echo htmlspecialchars($info['twitter']); ?>" class="social-link" aria-label="Twitter" target="_blank">
                                <i class="fab fa-twitter"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="footer-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Thông Tin Liên Hệ</h3>
                    <p><i class="fas fa-home"></i> <?php echo htmlspecialchars($info['address'] ?? ''); ?></p>
                    <p><i class="fas fa-phone"></i> <a href="tel:<?php echo htmlspecialchars($info['phone'] ?? ''); ?>"><?php echo htmlspecialchars($info['phone'] ?? ''); ?></a></p>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($info['email'] ?? ''); ?>"><?php echo htmlspecialchars($info['email'] ?? ''); ?></a></p>
                    <p><i class="fas fa-globe"></i> <?php echo htmlspecialchars($info['website'] ?? ''); ?></p>
                </div>

                <div class="footer-section">
                    <h3><i class="fas fa-clock"></i> Giờ Hoạt Động</h3>
                    <p><?php echo nl2br(htmlspecialchars($info['opening_hours'] ?? '')); ?></p>
                    <p class="mt-3"><i class="fas fa-info-circle"></i> <em>Phục vụ buffet liên tục</em></p>
                </div>

                <div class="footer-section">
                    <h3><i class="fas fa-concierge-bell"></i> Dịch Vụ</h3>
                    <p><a href="<?php echo SITE_URL; ?>/menu">Thực Đơn Buffet</a></p>
                    <p><a href="<?php echo SITE_URL; ?>/booking">Đặt Bàn Online</a></p>
                    <p><a href="<?php echo SITE_URL; ?>/promotions">Khuyến Mãi Đặc Biệt</a></p>
                    <p><a href="<?php echo SITE_URL; ?>/about">Về Chúng Tôi</a></p>
                    <p><a href="<?php echo SITE_URL; ?>/news">Tin Tức & Sự Kiện</a></p>
                </div>
            </div>

            <hr style="border-color: rgba(212, 175, 55, 0.3); margin: 2rem 0 1rem;">
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/luxury-effects.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>

<<<<<<< HEAD
    <!-- CSRF Token for AJAX requests -->
=======
    </html>
    </div>
    <!-- <div class="col-md-6 text-end">
        <a href="#" class="text-light me-3">Chính Sách Bảo Mật</a>
        <a href="#" class="text-light">Điều Khoản Dịch Vụ</a>
    </div> -->
    </div>
    </div>
    </footer> <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Luxury Smooth Scrolling -->
    <script src="<?php echo SITE_URL; ?>/assets/js/luxury-scroll.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script> <!-- CSRF Token for AJAX requests -->
>>>>>>> main
    <script>
        window.csrfToken = '<?php echo $_SESSION['csrf_token'] ?? ''; ?>';
        window.siteUrl = '<?php echo SITE_URL; ?>';
        window.SITE_URL = '<?php echo SITE_URL; ?>';
        <?php
        // Only generate token if none exists and we're not overwriting existing one
        if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = generateCSRFToken();
        }
        ?>
<<<<<<< HEAD
=======
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Parse cover images from PHP
    let heroImages = [];
    <?php
    $coverImages = [];
    if (!empty($info['cover_images'])) {
        $decoded = json_decode($info['cover_images'], true);
        if (is_array($decoded)) {
            foreach ($decoded as $img) {
                // Nếu ảnh không có http thì thêm SITE_URL
                $coverImages[] = (strpos($img, 'http') === 0) ? $img : SITE_URL . '/' . ltrim($img, '/');
            }
        }
    }
    ?>
    heroImages = <?php echo json_encode($coverImages); ?>;
    if (heroImages.length === 0) {
        heroImages = [
            '<?php echo SITE_URL; ?>/assets/images/nha-hang-buffet-2.png',
            '<?php echo SITE_URL; ?>/assets/images/nha-hang-buffet.jpg'
        ];
    }
    let currentHero = 0;
    const heroBg = document.getElementById('hero-bg');
    function setHeroBg(idx) {
        if (heroBg) heroBg.style.backgroundImage = 'url(' + heroImages[idx] + ')';
    }
    setHeroBg(currentHero);
    setInterval(() => {
        currentHero = (currentHero + 1) % heroImages.length;
        setHeroBg(currentHero);
    }, 3000);
});
>>>>>>> main
</script>
    </body>
    </html>
