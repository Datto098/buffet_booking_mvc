<!-- Clean & Minimal Register Design -->
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="logo">
                <i class="fas fa-user-plus"></i>
            </div>
            <h1>Tạo tài khoản mới</h1>
            <p>Đăng ký để nhận nhiều ưu đãi</p>
        </div>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo SITE_URL; ?>/auth/register" method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">

            <div class="form-group">
                <label for="full_name">Họ và tên *</label>
                <input type="text" id="full_name" name="full_name"
                       value="<?php echo $_POST['full_name'] ?? ''; ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email"
                           value="<?php echo $_POST['email'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone_number">Số điện thoại *</label>
                    <input type="tel" id="phone_number" name="phone_number"
                           value="<?php echo $_POST['phone_number'] ?? ''; ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Mật khẩu *</label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Xác nhận mật khẩu *</label>
                    <div class="password-input">
                        <input type="password" id="password_confirmation" name="password_confirmation" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <textarea id="address" name="address" rows="3"><?php echo $_POST['address'] ?? ''; ?></textarea>
            </div>

            <div class="form-options">
                <label class="checkbox">
                    <input type="checkbox" id="terms" name="terms" required>
                    <span>Tôi đồng ý với <a href="#" class="link">Điều khoản dịch vụ</a></span>
                </label>

                <label class="checkbox">
                    <input type="checkbox" id="newsletter" name="newsletter" checked>
                    <span>Nhận thông báo khuyến mãi</span>
                </label>
            </div>

            <button type="submit" class="btn-primary">Đăng ký tài khoản</button>

            <div class="auth-footer">
                <p>Đã có tài khoản? <a href="<?php echo SITE_URL; ?>/auth/login">Đăng nhập ngay</a></p>
            </div>
        </form>
    </div>
</div>

<!-- Clean & Minimal CSS -->
<style>
body {
    margin: 0;
    padding: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    background: #f8fafc;
}

.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
}

.auth-card {
    background: white;
    border-radius: 20px;
    padding: 3rem;
    width: 100%;
    max-width: 520px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    max-height: 95vh;
    overflow-y: auto;
    animation: slideUp 0.6s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.auth-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.logo {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    color: white;
    font-size: 1.8rem;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.auth-header h1 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
    font-size: 2rem;
    font-weight: 700;
}

.auth-header p {
    margin: 0;
    color: #6b7280;
    font-size: 1rem;
}

.alert {
    padding: 1rem 1.25rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 500;
}

.alert-error {
    background: linear-gradient(135deg, #fef2f2, #fee2e2);
    color: #dc2626;
    border: 1px solid #fecaca;
}

.alert-success {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    color: #16a34a;
    border: 1px solid #bbf7d0;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.75rem;
    color: #374151;
    font-weight: 600;
    font-size: 0.9rem;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 1rem 1.25rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f9fafb;
    font-family: inherit;
    color: #1f2937;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    transform: translateY(-1px);
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: #9ca3af;
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.password-input {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 6px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.password-toggle:hover {
    color: #374151;
    background: #f3f4f6;
}

.form-options {
    margin-bottom: 2rem;
}

.checkbox {
    display: flex;
    align-items: flex-start;
    cursor: pointer;
    font-size: 0.9rem;
    color: #374151;
    margin-bottom: 1rem;
    line-height: 1.4;
    font-weight: 500;
}

.checkbox input {
    width: auto !important;
    margin-right: 0.5rem;
    margin-top: 0.1rem;
    accent-color: #667eea;
    transform: scale(1.1);
    flex-shrink: 0;
}

.link {
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
}

.link:hover {
    text-decoration: underline;
}

.btn-primary {
    width: 100%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 1.125rem;
    border-radius: 12px;
    font-size: 1.05rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 2rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
}

.btn-primary:active {
    transform: translateY(0);
}

.auth-footer {
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.auth-footer p {
    margin: 0;
    color: #6b7280;
    font-size: 0.95rem;
}

.auth-footer a {
    color: #667eea;
    text-decoration: none;
    font-weight: 700;
    transition: all 0.2s ease;
}

.auth-footer a:hover {
    color: #5a67d8;
    text-decoration: underline;
}

/* Form validation */
.input-error {
    border-color: #dc2626 !important;
    background: #fef2f2 !important;
    animation: shake 0.3s ease;
}

.input-success {
    border-color: #16a34a !important;
    background: #f0fdf4 !important;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Password strength indicator */
.password-strength {
    margin-top: 0.5rem;
    padding: 0.75rem;
    border-radius: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    font-size: 0.8rem;
}

.strength-meter {
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.strength-bar {
    height: 100%;
    transition: all 0.3s ease;
    border-radius: 2px;
}

.strength-weak { width: 25%; background: #dc2626; }
.strength-fair { width: 50%; background: #f59e0b; }
.strength-good { width: 75%; background: #3b82f6; }
.strength-strong { width: 100%; background: #16a34a; }

/* Responsive */
@media (max-width: 576px) {
    .auth-container {
        padding: 1rem;
    }
    
    .auth-card {
        padding: 2rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .auth-header h1 {
        font-size: 1.75rem;
    }
    
    .logo {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
}
</style>

<!-- Clean JavaScript -->
<script>
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleButton = passwordInput.nextElementSibling.querySelector('i');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleButton.classList.remove('fa-eye');
        toggleButton.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleButton.classList.remove('fa-eye-slash');
        toggleButton.classList.add('fa-eye');
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.auth-form');
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const phoneInput = document.getElementById('phone_number');

    // Phone number formatting
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) {
            value = value.slice(0, 11);
        }
        e.target.value = value;
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
        let isValid = true;

        // Check required fields
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('input-error');
                isValid = false;
            } else {
                input.classList.remove('input-error');
                input.classList.add('input-success');
            }
        });

        // Check password match
        if (passwordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.classList.add('input-error');
            showError('Mật khẩu xác nhận không khớp');
            isValid = false;
        }

        // Check email format
        const emailInput = document.getElementById('email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailInput.value && !emailRegex.test(emailInput.value)) {
            emailInput.classList.add('input-error');
            showError('Email không hợp lệ');
            isValid = false;
        }

        // Check phone format
        if (phoneInput.value && phoneInput.value.length < 10) {
            phoneInput.classList.add('input-error');
            showError('Số điện thoại không hợp lệ');
            isValid = false;
        }

        // Check password strength
        if (passwordInput.value && passwordInput.value.length < 6) {
            passwordInput.classList.add('input-error');
            showError('Mật khẩu phải có ít nhất 6 ký tự');
            isValid = false;
        }

        // Check terms agreement
        const termsCheckbox = document.getElementById('terms');
        if (!termsCheckbox.checked) {
            showError('Bạn phải đồng ý với điều khoản dịch vụ');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    // Clear error styling on input
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('input-error');
            }
        });
    });

    // Password confirmation validation
    confirmPasswordInput.addEventListener('input', function() {
        if (this.value && passwordInput.value === this.value) {
            this.classList.remove('input-error');
            this.classList.add('input-success');
        } else if (this.value) {
            this.classList.add('input-error');
            this.classList.remove('input-success');
        }
    });

    function showError(message) {
        // Remove existing error alerts
        const existingAlert = document.querySelector('.alert-error');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Create new error alert
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-error';
        alertDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;

        // Insert at top of form
        const form = document.querySelector('.auth-form');
        form.insertBefore(alertDiv, form.firstChild);

        // Remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>
