    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-utensils"></i> <?php echo SITE_NAME; ?></h5>
                    <p>Nhà hàng buffet cao cấp với các món ăn đa dạng và chất lượng. Phục vụ 24/7 với không gian sang trọng và dịch vụ tận tình.</p>
                </div>
                <div class="col-md-4">
                    <h5><i class="fas fa-map-marker-alt"></i> Thông Tin Liên Hệ</h5>
                    <p><i class="fas fa-home"></i> 123 Đường ABC, Quận XYZ, TP.HCM</p>
                    <p><i class="fas fa-phone"></i> 0123-456-789</p>
                    <p><i class="fas fa-envelope"></i> <?php echo ADMIN_EMAIL; ?></p>
                </div>
                <div class="col-md-4">
                    <h5><i class="fas fa-clock"></i> Giờ Hoạt Động</h5>
                    <p><i class="fas fa-calendar-day"></i> Thứ 2 - Chủ Nhật: 10:00 - 22:00</p>
                    <h5 class="mt-3"><i class="fas fa-share-alt"></i> Theo Dõi Chúng Tôi</h5>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Tất cả quyền được bảo lưu.</p>
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
