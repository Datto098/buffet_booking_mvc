<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">
                            <i class="fas fa-user-plus"></i> Đăng Ký Tài Khoản
                        </h2>
                        <p class="text-muted">Tạo tài khoản để có trải nghiệm tuyệt vời</p>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>                    <?php endif; ?>                    <form action="<?php echo SITE_URL; ?>/auth/register"                          method="POST" data-validate="true">
                        <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="full_name" class="form-label">
                                    <i class="fas fa-user"></i> Họ và Tên <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="full_name"
                                       name="full_name"
                                       placeholder="Nhập họ và tên đầy đủ"
                                       value="<?php echo $_POST['full_name'] ?? ''; ?>"
                                       required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       class="form-control"
                                       id="email"
                                       name="email"
                                       placeholder="your@email.com"
                                       value="<?php echo $_POST['email'] ?? ''; ?>"
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">
                                    <i class="fas fa-phone"></i> Số điện thoại
                                </label>
                                <input type="tel"
                                       class="form-control"
                                       id="phone_number"
                                       name="phone_number"
                                       placeholder="0123456789"
                                       value="<?php echo $_POST['phone_number'] ?? ''; ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Mật khẩu <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control"
                                           id="password"
                                           name="password"
                                           placeholder="Tối thiểu 6 ký tự"
                                           minlength="<?php echo PASSWORD_MIN_LENGTH; ?>"
                                           required>
                                    <button class="btn btn-outline-secondary"
                                            type="button"
                                            onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password-icon"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    Mật khẩu phải có ít nhất <?php echo PASSWORD_MIN_LENGTH; ?> ký tự
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock"></i> Xác nhận mật khẩu <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control"
                                           id="confirm_password"
                                           name="confirm_password"
                                           placeholder="Nhập lại mật khẩu"
                                           required>
                                    <button class="btn btn-outline-secondary"
                                            type="button"
                                            onclick="togglePassword('confirm_password')">
                                        <i class="fas fa-eye" id="confirm_password-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                Tôi đồng ý với
                                <a href="#" class="text-decoration-none">Điều khoản dịch vụ</a>
                                và
                                <a href="#" class="text-decoration-none">Chính sách bảo mật</a>
                                <span class="text-danger">*</span>
                            </label>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="newsletter">
                            <label class="form-check-label" for="newsletter">
                                Nhận thông báo về các chương trình khuyến mãi qua email
                            </label>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus"></i> Tạo Tài Khoản
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">                    <div class="text-center">
                        <p class="mb-0">Đã có tài khoản?</p>
                        <a href="<?php echo SITE_URL; ?>/auth/login"
                           class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt"></i> Đăng Nhập Ngay
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

// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strength = calculatePasswordStrength(password);
    showPasswordStrength(strength);
});

function calculatePasswordStrength(password) {
    let score = 0;
    if (password.length >= 6) score++;
    if (password.length >= 8) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;

    return score;
}

function showPasswordStrength(score) {
    const strengthBar = document.getElementById('password-strength');
    if (!strengthBar) return;

    const colors = ['#dc3545', '#fd7e14', '#ffc107', '#28a745'];
    const labels = ['Yếu', 'Trung bình', 'Khá', 'Mạnh'];

    if (score <= 2) {
        strengthBar.style.backgroundColor = colors[0];
        strengthBar.textContent = labels[0];
    } else if (score <= 3) {
        strengthBar.style.backgroundColor = colors[1];
        strengthBar.textContent = labels[1];
    } else if (score <= 4) {
        strengthBar.style.backgroundColor = colors[2];
        strengthBar.textContent = labels[2];
    } else {
        strengthBar.style.backgroundColor = colors[3];
        strengthBar.textContent = labels[3];
    }
}
</script>
