<!-- Forgot Password Page with Luxury Site Layout -->
<div class="auth-page-luxury">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="auth-card-luxury">
                    <div class="auth-header-luxury">
                        <div class="auth-icon-luxury">
                            <i class="fas fa-key"></i>
                        </div>
                        <h2>Quên mật khẩu?</h2>
                        <p>Nhập email để nhận link đặt lại mật khẩu</p>
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

                    <form action="<?php echo SITE_URL; ?>/auth/forgot-password" method="POST" class="auth-form-luxury">
                        <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">

                        <div class="form-group-luxury">
                            <label for="email">
                                <i class="fas fa-envelope"></i>
                                Email của bạn
                            </label>
                            <input type="email" id="email" name="email" class="form-control-luxury"
                                   placeholder="Nhập địa chỉ email đã đăng ký" required>
                        </div>

                        <button type="submit" class="btn-luxury btn-primary-luxury">
                            Gửi link đặt lại mật khẩu
                        </button>

                        <div class="auth-footer-luxury">
                            <p>Nhớ ra mật khẩu? <a href="<?php echo SITE_URL; ?>/auth/login">Đăng nhập ngay</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Forgot Password uses same luxury styles as login */
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
    line-height: 1.5;
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
    margin-bottom: 2rem;
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
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.auth-form-luxury');
    const emailInput = document.getElementById('email');

    // Form validation
    form.addEventListener('submit', function(e) {
        const email = emailInput.value.trim();

        if (!email) {
            emailInput.style.borderColor = '#dc3545';
            showError('Vui lòng nhập email');
            e.preventDefault();
            return;
        }

        // Simple email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            emailInput.style.borderColor = '#dc3545';
            showError('Email không hợp lệ');
            e.preventDefault();
            return;
        }

        emailInput.style.borderColor = 'var(--neutral-pearl)';
    });

    // Clear error styling on input
    emailInput.addEventListener('input', function() {
        if (this.value.trim()) {
            this.style.borderColor = 'var(--neutral-pearl)';
            clearErrors();
        }
    });

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
