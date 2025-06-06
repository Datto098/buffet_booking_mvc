    </main>

    <!-- Footer -->
    <footer class="footer-luxury">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-crown"></i> <?php echo SITE_NAME; ?></h3>
                    <p>Nhà hàng buffet đẳng cấp quốc tế với không gian sang trọng và ẩm thực tinh tế. Chúng tôi mang đến trải nghiệm ẩm thực khó quên với hương vị đa dạng từ khắp nơi trên thế giới.</p>
                    <div class="social-links">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Thông Tin Liên Hệ</h3>
                    <p><i class="fas fa-home"></i> 123 Đường Nguyễn Huệ, Quận 1, TP.HCM</p>
                    <p><i class="fas fa-phone"></i> <a href="tel:0123456789">0123-456-789</a></p>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a></p>
                    <p><i class="fas fa-globe"></i> www.luxury-buffet.com</p>
                </div>

                <div class="footer-section">
                    <h3><i class="fas fa-clock"></i> Giờ Hoạt Động</h3>
                    <p><strong>Thứ 2 - Thứ 6:</strong> 11:00 - 22:00</p>
                    <p><strong>Thứ 7 - Chủ Nhật:</strong> 10:00 - 23:00</p>
                    <p><strong>Ngày Lễ:</strong> 10:00 - 23:30</p>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/luxury-effects.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="text-light me-3">Chính Sách Bảo Mật</a>
                    <a href="#" class="text-light">Điều Khoản Dịch Vụ</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>    <!-- CSRF Token for AJAX requests -->
    <script>
        window.csrfToken = '<?php echo $_SESSION['csrf_token'] ?? generateCSRFToken(); ?>';
        window.siteUrl = '<?php echo SITE_URL; ?>';
        <?php if (!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = generateCSRFToken(); ?>
    </script>
</body>
</html>
