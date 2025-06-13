<!-- Clean & Minimal Login Design -->
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="logo">
                <i class="fas fa-utensils"></i>
            </div>
            <h1>Chào mừng trở lại</h1>
            <p>Đăng nhập để tiếp tục</p>
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

        <form action="<?php echo SITE_URL; ?>/auth/login" method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Nhập email của bạn" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <div class="password-input">
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-options">
                <label class="checkbox">
                    <input type="checkbox" id="remember">
                    <span>Ghi nhớ đăng nhập</span>
                </label>
                <a href="<?php echo SITE_URL; ?>/auth/forgot-password" class="forgot-link">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="btn-primary">Đăng nhập</button>

            <div class="auth-footer">
                <p>Chưa có tài khoản? <a href="<?php echo SITE_URL; ?>/auth/register">Đăng ký ngay</a></p>
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
    max-width: 420px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
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
    margin-bottom: 1.75rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.75rem;
    color: #374151;
    font-weight: 600;
    font-size: 0.95rem;
}

.form-group input {
    width: 100%;
    padding: 1rem 1.25rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f9fafb;
    color: #1f2937;
}

.form-group input:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    transform: translateY(-1px);
}

.form-group input::placeholder {
    color: #9ca3af;
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
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.checkbox {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 0.9rem;
    color: #374151;
    font-weight: 500;
}

.checkbox input {
    width: auto !important;
    margin-right: 0.5rem;
    accent-color: #667eea;
    transform: scale(1.1);
}

.forgot-link {
    color: #667eea;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.2s ease;
}

.forgot-link:hover {
    color: #5a67d8;
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

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Responsive */
@media (max-width: 480px) {
    .auth-container {
        padding: 1rem;
    }
    
    .auth-card {
        padding: 2rem;
    }
    
    .auth-header h1 {
        font-size: 1.75rem;
    }
    
    .logo {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .form-options {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
}
</style>

<!-- Clean JavaScript -->
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleButton = document.querySelector('.password-toggle i');
    
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

// Enhanced form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.auth-form');
    const inputs = form.querySelectorAll('input[required]');
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        inputs.forEach(input => {
            input.classList.remove('input-error');
            
            if (!input.value.trim()) {
                input.classList.add('input-error');
                isValid = false;
            }
            
            // Email validation
            if (input.type === 'email' && input.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(input.value)) {
                    input.classList.add('input-error');
                    isValid = false;
                }
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            // Focus first error
            const firstError = form.querySelector('.input-error');
            if (firstError) {
                firstError.focus();
            }
        }
    });
    
    // Clear error styling on input
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('input-error');
            }
        });
        
        input.addEventListener('focus', function() {
            this.classList.remove('input-error');
        });
    });
    
    // Add smooth focus transitions
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });
});
</script>