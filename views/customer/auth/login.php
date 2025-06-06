<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">
                            <i class="fas fa-sign-in-alt"></i> Đăng Nhập
                        </h2>
                        <p class="text-muted">Đăng nhập để trải nghiệm dịch vụ tốt nhất</p>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>                    <?php endif; ?>                    <form action="<?php echo SITE_URL; ?>/auth/login"
                          method="POST" data-validate="true">
                        <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email"
                                   class="form-control"
                                   id="email"
                                   name="email"
                                   placeholder="Nhập email của bạn"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Mật khẩu
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control"
                                       id="password"
                                       name="password"
                                       placeholder="Nhập mật khẩu"
                                       required>
                                <button class="btn btn-outline-secondary"
                                        type="button"
                                        onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password-icon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">
                                Ghi nhớ đăng nhập
                            </label>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Đăng Nhập
                            </button>
                        </div>                        <div class="text-center">
                            <a href="<?php echo SITE_URL; ?>/auth/forgotPassword"
                               class="text-decoration-none">
                                <i class="fas fa-key"></i> Quên mật khẩu?
                            </a>
                        </div>
                    </form>

                    <hr class="my-4">                    <div class="text-center">
                        <p class="mb-0">Chưa có tài khoản?</p>
                        <a href="<?php echo SITE_URL; ?>/auth/register"
                           class="btn btn-outline-primary">
                            <i class="fas fa-user-plus"></i> Đăng Ký Ngay
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="<?php echo SITE_URL; ?>" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left"></i> Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');

    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
