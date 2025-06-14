<!-- Login Page with Luxury Site Layout -->
<div class="auth-page-luxury">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="auth-card-luxury">
                    <div class="auth-header-luxury">
                        <div class="auth-icon-luxury">
                            <i class="fas fa-crown"></i>
                        </div>
                        <h2>Chào mừng trở lại</h2>
                        <p>Đăng nhập để tiếp tục trải nghiệm</p>
                    </div>

                    <!-- Flash Messages -->
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger luxury-alert">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success luxury-alert">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo SITE_URL; ?>/auth/login" method="POST" class="auth-form-luxury">
                        <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">

                        <div class="form-group-luxury">
                            <label for="email">
                                <i class="fas fa-envelope"></i>
                                Email
                            </label>
                            <input type="email" id="email" name="email" class="form-control-luxury" placeholder="Nhập địa chỉ email" required>
                        </div>

                        <div class="form-group-luxury">
                            <label for="password">
                                <i class="fas fa-lock"></i>
                                Mật khẩu
                            </label>
                            <div class="password-input-luxury">
                                <input type="password" id="password" name="password" class="form-control-luxury" placeholder="Nhập mật khẩu" required>
                                <button type="button" class="password-toggle-luxury" onclick="togglePassword()">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-options-luxury">
                            <label class="checkbox-luxury">
                                <input type="checkbox" id="remember">
                                <span class="checkmark-luxury"></span>
                                Ghi nhớ đăng nhập
                            </label>
                            <a href="<?php echo SITE_URL; ?>/auth/forgot-password" class="forgot-link-luxury">
                                Quên mật khẩu?
                            </a>
                        </div>

                        <button type="submit" class="btn-luxury btn-primary-luxury">
                            Đăng nhập
                        </button>

                        <div class="auth-footer-luxury">
                            <p>Chưa có tài khoản? <a href="<?php echo SITE_URL; ?>/auth/register">Đăng ký ngay</a></p>
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
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.auth-card-luxury {
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    padding: 3rem;
    box-shadow: var(--shadow-strong);
    border: 1px solid var(--neutral-pearl);
    position: relative;
    z-index: 1;
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

.checkbox-luxury {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    font-size: 0.9rem;
    color: var(--text-secondary);
    position: relative;
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
}

.checkbox-luxury input:checked ~ .checkmark-luxury {
    background: var(--primary-gold);
    border-color: var(--primary-gold);
}

.checkbox-luxury input:checked ~ .checkmark-luxury::after {
    content: '\2713';
    color: white;
    font-weight: bold;
    font-size: 12px;
}

.forgot-link-luxury {
    color: var(--primary-gold);
    text-decoration: none;
    font-family: 'Inter', sans-serif;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.forgot-link-luxury:hover {
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

/* Responsive */
@media (max-width: 768px) {
    .auth-card-luxury {
        padding: 2rem;
        margin: 1rem;
    }

    .auth-header-luxury h2 {
        font-size: 1.75rem;
    }

    .form-options-luxury {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}
</style>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleButton = document.querySelector('.password-toggle-luxury i');

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
    const form = document.querySelector('.auth-form-luxury');
    const inputs = form.querySelectorAll('input[required]');

    form.addEventListener('submit', function(e) {
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.style.borderColor = '#dc3545';
                isValid = false;
            } else {
                input.style.borderColor = 'var(--neutral-pearl)';
            }
        });

        if (!isValid) {
            e.preventDefault();
        }
    });

    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.style.borderColor = 'var(--neutral-pearl)';
            }
        });
    });
});
</script>


