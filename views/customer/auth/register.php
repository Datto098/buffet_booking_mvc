<!-- Register Page with Luxury Site Layout -->
<div class="auth-page-luxury">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="auth-card-luxury">
                    <div class="auth-header-luxury">
                        <div class="auth-icon-luxury">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h2>Tạo tài khoản mới</h2>
                        <p>Đăng ký để nhận nhiều ưu đãi hấp dẫn</p>
                    </div>

                    <!-- Flash Messages -->
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger luxury-alert">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $_SESSION['error'];
                            unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success luxury-alert">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $_SESSION['success'];
                            unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo SITE_URL; ?>/auth/register" method="POST" class="auth-form-luxury">
                        <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">

                        <div class="form-group-luxury">
                            <label for="full_name">
                                <i class="fas fa-user"></i>
                                Họ và tên *
                            </label>
                            <input type="text" id="full_name" name="full_name" class="form-control-luxury"
                                placeholder="Nhập họ và tên đầy đủ"
                                value="<?php echo $_POST['full_name'] ?? ''; ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-luxury">
                                    <label for="email">
                                        <i class="fas fa-envelope"></i>
                                        Email *
                                    </label>
                                    <input type="email" id="email" name="email" class="form-control-luxury"
                                        placeholder="your@email.com"
                                        value="<?php echo $_POST['email'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-luxury">
                                    <label for="phone_number">
                                        <i class="fas fa-phone"></i>
                                        Số điện thoại *
                                    </label>
                                    <input type="tel" id="phone_number" name="phone_number" class="form-control-luxury"
                                        placeholder="0123456789"
                                        value="<?php echo $_POST['phone_number'] ?? ''; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-luxury">
                                    <label for="password">
                                        <i class="fas fa-lock"></i>
                                        Mật khẩu *
                                    </label>
                                    <div class="password-input-luxury">
                                        <input type="password" id="password" name="password" class="form-control-luxury"
                                            placeholder="Ít nhất 6 ký tự" required>
                                        <button type="button" class="password-toggle-luxury" onclick="togglePassword('password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-luxury">
                                    <label for="password_confirmation">
                                        <i class="fas fa-lock"></i>
                                        Xác nhận mật khẩu *
                                    </label>
                                    <div class="password-input-luxury">
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            class="form-control-luxury" placeholder="Nhập lại mật khẩu" required>
                                        <button type="button" class="password-toggle-luxury" onclick="togglePassword('password_confirmation')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="form-group-luxury">
                            <label for="address">
                                <i class="fas fa-map-marker-alt"></i>
                                Địa chỉ
                            </label>
                            <textarea id="address" name="address" class="form-control-luxury" rows="3"
                                placeholder="Nhập địa chỉ của bạn"><?php echo $_POST['address'] ?? ''; ?></textarea>
                        </div> -->

                        <div class="form-options-luxury register-options">
                            <label class="checkbox-luxury">
                                <input type="checkbox" id="terms" name="terms" required>
                                <span class="checkmark-luxury"></span>
                                Tôi đồng ý với <a href="#" class="link-luxury" style="margin-left: 8px"> Điều khoản dịch vụ</a>
                            </label>

                            <label class="checkbox-luxury">
                                <input type="checkbox" id="newsletter" name="newsletter" checked>
                                <span class="checkmark-luxury"></span>
                                Nhận thông báo khuyến mãi
                            </label>
                        </div>

                        <button type="submit" class="btn-luxury btn-primary-luxury">
                            Đăng ký tài khoản
                        </button>

                        <div class="auth-footer-luxury">
                            <p>Đã có tài khoản? <a href="<?php echo SITE_URL; ?>/auth/login">Đăng nhập ngay</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Auth Page Luxury Styles - Matching Site Theme */
    .auth-page-luxury {
        min-height: 100vh;
        background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-navy-dark) 100%);
        display: flex;
        align-items: center;
        padding: 2rem 0;
        position: relative;
    }

    /* Hide scrollbar for Chrome, Safari and Opera */
    .auth-card-luxury::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .auth-card-luxury {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    .auth-page-luxury::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23D4AF37" fill-opacity="0.05"><circle cx="30" cy="30" r="1"/></g></svg>') repeat;
        animation: float 20s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    .auth-card-luxury {
        background: var(--bg-primary);
        border-radius: var(--radius-xl);
        padding: 3rem;
        box-shadow: var(--shadow-strong);
        border: 1px solid var(--neutral-pearl);
        position: relative;
        z-index: 1;
        max-height: 90vh;
        overflow-y: auto;
    }

    .auth-header-luxury {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .auth-icon-luxury {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-gold), var(--primary-gold-dark));
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        color: var(--text-white);
        font-size: 2rem;
        box-shadow: var(--shadow-gold);
    }

    .auth-header-luxury h2 {
        color: var(--text-primary);
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .auth-header-luxury p {
        color: var(--text-secondary);
        font-family: 'Inter', sans-serif;
        font-size: 1rem;
        margin: 0;
    }

    .luxury-alert {
        border-radius: var(--radius-md);
        border: none;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-family: 'Inter', sans-serif;
    }

    .alert-danger.luxury-alert {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border-left: 4px solid #dc3545;
    }

    .alert-success.luxury-alert {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
        border-left: 4px solid #28a745;
    }

    .form-group-luxury {
        margin-bottom: 1.5rem;
    }

    .form-group-luxury label {
        display: block;
        margin-bottom: 0.75rem;
        color: var(--text-primary);
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .form-group-luxury label i {
        margin-right: 0.5rem;
        color: var(--primary-gold);
    }

    .form-control-luxury {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid var(--neutral-pearl);
        border-radius: var(--radius-md);
        font-size: 1rem;
        font-family: 'Inter', sans-serif;
        background: var(--bg-secondary);
        color: var(--text-primary);
        transition: all 0.3s ease;
    }

    .form-control-luxury:focus {
        outline: none;
        border-color: var(--primary-gold);
        background: var(--bg-primary);
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
    }

    .form-control-luxury[rows] {
        resize: vertical;
        min-height: 100px;
        font-family: 'Inter', sans-serif;
    }

    .password-input-luxury {
        position: relative;
    }

    .password-toggle-luxury {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-light);
        cursor: pointer;
        padding: 0.5rem;
        border-radius: var(--radius-sm);
        transition: all 0.3s ease;
    }

    .password-toggle-luxury:hover {
        color: var(--primary-gold);
        background: rgba(212, 175, 55, 0.1);
    }

    .form-options-luxury {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .register-options {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .checkbox-luxury {
        display: flex;
        align-items: center;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        color: var(--text-secondary);
        position: relative;
        margin-bottom: 0;
    }

    .checkbox-luxury input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .checkmark-luxury {
        height: 20px;
        width: 20px;
        background: var(--bg-secondary);
        border: 2px solid var(--neutral-pearl);
        border-radius: 4px;
        margin-right: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .checkbox-luxury input:checked~.checkmark-luxury {
        background: var(--primary-gold);
        border-color: var(--primary-gold);
    }

    .checkbox-luxury input:checked~.checkmark-luxury::after {
        content: '\2713';
        color: white;
        font-weight: bold;
        font-size: 12px;
    }

    .link-luxury {
        color: var(--primary-gold);
        text-decoration: none;
        font-weight: 500;
    }

    .link-luxury:hover {
        color: var(--primary-gold-dark);
        text-decoration: underline;
    }

    .btn-luxury {
        width: 100%;
        padding: 1rem 2rem;
        border: none;
        border-radius: var(--radius-md);
        font-family: 'Inter', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1.5rem;
    }

    .btn-primary-luxury {
        background: linear-gradient(135deg, var(--primary-gold), var(--primary-gold-dark));
        color: var(--text-white);
        box-shadow: var(--shadow-gold);
    }

    .btn-primary-luxury:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(212, 175, 55, 0.4);
    }

    .auth-footer-luxury {
        text-align: center;
        padding-top: 1.5rem;
        border-top: 1px solid var(--neutral-pearl);
    }

    .auth-footer-luxury p {
        margin: 0;
        color: var(--text-light);
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
    }

    .auth-footer-luxury a {
        color: var(--primary-gold);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .auth-footer-luxury a:hover {
        color: var(--primary-gold-dark);
        text-decoration: underline;
    }

    /* Form validation styles */
    .form-control-luxury.is-invalid {
        border-color: #dc3545;
        background-color: rgba(220, 53, 69, 0.05);
    }

    .form-control-luxury.is-valid {
        border-color: #28a745;
        background-color: rgba(40, 167, 69, 0.05);
    }

    .input-error {
        color: #dc3545;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        font-family: 'Inter', sans-serif;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .password-strength {
        margin-top: 0.5rem;
        font-size: 0.85rem;
        font-family: 'Inter', sans-serif;
    }

    .strength-weak {
        color: #dc3545;
    }

    .strength-fair {
        color: #ffc107;
    }

    .strength-good {
        color: #17a2b8;
    }

    .strength-strong {
        color: #28a745;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .auth-card-luxury {
            padding: 2rem;
            margin: 1rem;
        }

        .auth-header-luxury h2 {
            font-size: 1.75rem;
        }

        .register-options {
            text-align: left;
        }

        .row .col-md-6 {
            margin-bottom: 0;
        }

        .form-group-luxury {
            margin-bottom: 1.25rem;
        }
    }
</style>

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

    // Enhanced form validation for register
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.auth-form-luxury');
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

        // Password strength checker
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });

        // Form submission validation
        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Check required fields
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    input.classList.remove('is-valid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                }
            });

            // Check password match
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.classList.add('is-invalid');
                showError('Mật khẩu xác nhận không khớp');
                isValid = false;
            }

            // Check email format
            const emailInput = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailInput.value && !emailRegex.test(emailInput.value)) {
                emailInput.classList.add('is-invalid');
                showError('Email không hợp lệ');
                isValid = false;
            }

            // Check phone format
            if (phoneInput.value && phoneInput.value.length < 10) {
                phoneInput.classList.add('is-invalid');
                showError('Số điện thoại không hợp lệ');
                isValid = false;
            }

            // Check password strength
            if (passwordInput.value && passwordInput.value.length < 6) {
                passwordInput.classList.add('is-invalid');
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
                    this.classList.remove('is-invalid');
                    clearErrors();
                }
            });
        });

        // Password confirmation validation
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value && passwordInput.value === this.value) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else if (this.value) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            }
        });

        function checkPasswordStrength(password) {
            const strengthIndicator = document.getElementById('password-strength') || createStrengthIndicator();

            if (!password) {
                strengthIndicator.textContent = '';
                return;
            }

            let score = 0;
            if (password.length >= 8) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;

            const levels = ['', 'strength-weak', 'strength-fair', 'strength-good', 'strength-strong'];
            const texts = ['', 'Yếu', 'Trung bình', 'Khá', 'Mạnh'];

            strengthIndicator.className = `password-strength ${levels[Math.min(score, 4)]}`;
            strengthIndicator.textContent = score > 0 ? `Độ mạnh: ${texts[score]}` : '';
        }

        function createStrengthIndicator() {
            const indicator = document.createElement('div');
            indicator.id = 'password-strength';
            indicator.className = 'password-strength';
            passwordInput.closest('.form-group-luxury').appendChild(indicator);
            return indicator;
        }

        function showError(message) {
            clearErrors();

            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger luxury-alert error-alert';
            alertDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;

            const form = document.querySelector('.auth-form-luxury');
            form.insertBefore(alertDiv, form.firstChild);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        function clearErrors() {
            const errorAlert = document.querySelector('.error-alert');
            if (errorAlert) {
                errorAlert.remove();
            }
        }
    });
</script>
