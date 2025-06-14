<!-- Modern Login UI -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="login-bg">
  <div class="login-card">
    <div class="login-logo">
      <i class="fa-solid fa-utensils"></i>
    </div>
    <h2 class="login-title">Đăng nhập</h2>
    <p class="login-sub">Chào mừng bạn quay lại Buffet Booking</p>

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

    <form action="<?php echo SITE_URL; ?>/auth/login" method="POST" class="login-form" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
      <div class="input-wrap">
        <label for="email"><i class="fa-solid fa-envelope"></i></label>
        <input type="email" id="email" name="email" placeholder="Email" required>
      </div>
      <div class="input-wrap">
        <label for="password"><i class="fa-solid fa-lock"></i></label>
        <input type="password" id="password" name="password" placeholder="Mật khẩu" required>
        <button type="button" class="showpass" onclick="togglePassword()"><i class="fa-solid fa-eye"></i></button>
      </div>
      <div class="login-options">
        <label class="remember">
          <input type="checkbox" id="remember"> Ghi nhớ
        </label>
        <a href="<?php echo SITE_URL; ?>/auth/forgot-password" class="forgot">Quên mật khẩu?</a>
      </div>
      <button type="submit" class="login-btn">Đăng nhập</button>
      <div class="login-footer">
        <span>Chưa có tài khoản?</span>
        <a href="<?php echo SITE_URL; ?>/auth/register">Đăng ký ngay</a>
      </div>
    </form>
  </div>
</div>

<style>
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  overflow: hidden; /* Ngăn kéo xuống */
}
body {
  height: 100vh;
  width: 100vw;
}
.login-bg {
  height: 100vh;
  min-height: 100vh;
  background: linear-gradient(120deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}
.login-card {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 8px 32px rgba(102,126,234,0.18);
  padding: 2.5rem 2rem 2rem 2rem;
  width: 100%;
  max-width: 370px;
  display: flex;
  flex-direction: column;
  align-items: center;
  animation: fadeIn 0.7s;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(30px);}
  to { opacity: 1; transform: translateY(0);}
}
.login-logo {
  width: 64px; height: 64px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 2rem; margin-bottom: 1.2rem;
  box-shadow: 0 4px 18px rgba(102,126,234,0.25);
}
.login-title {
  font-size: 1.7rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 0.3rem;
  letter-spacing: 0.5px;
}
.login-sub {
  color: #888;
  font-size: 1rem;
  margin-bottom: 1.7rem;
}
.login-form {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
}
.input-wrap {
  display: flex;
  align-items: center;
  background: #f3f4f6;
  border-radius: 10px;
  padding: 0.7rem 1rem;
  border: 2px solid #e5e7eb;
  transition: border 0.2s;
  position: relative;
}
.input-wrap:focus-within {
  border-color: #667eea;
  background: #fff;
}
.input-wrap label {
  color: #667eea;
  font-size: 1.1rem;
  margin-right: 0.7rem;
}
.input-wrap input {
  border: none;
  background: transparent;
  outline: none;
  font-size: 1rem;
  width: 100%;
  color: #222;
  padding: 0.2rem 0;
}
.input-wrap input::placeholder {
  color: #b5b5b5;
}
.showpass {
  background: none;
  border: none;
  color: #aaa;
  font-size: 1.1rem;
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  padding: 2px 4px;
  border-radius: 5px;
  transition: background 0.2s;
}
.showpass:hover { background: #f3f4f6; color: #667eea; }
.login-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.97rem;
  margin-bottom: 0.2rem;
}
.remember {
  display: flex; align-items: center; gap: 5px;
  color: #444;
}
.remember input[type="checkbox"] {
  accent-color: #667eea;
  width: 16px; height: 16px;
}
.forgot {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}
.forgot:hover { color: #764ba2; text-decoration: underline; }
.login-btn {
  width: 100%;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: #fff;
  border: none;
  padding: 0.95rem;
  border-radius: 10px;
  font-size: 1.07rem;
  font-weight: 600;
  cursor: pointer;
  margin-top: 0.2rem;
  margin-bottom: 0.7rem;
  box-shadow: 0 4px 15px rgba(102,126,234,0.18);
  transition: background 0.2s, transform 0.2s;
  letter-spacing: 0.5px;
}
.login-btn:hover { background: linear-gradient(135deg, #5a67d8, #764ba2); transform: translateY(-2px);}
.login-footer {
  text-align: center;
  color: #888;
  font-size: 0.97rem;
}
.login-footer a {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
  margin-left: 4px;
  transition: color 0.2s;
}
.login-footer a:hover { color: #764ba2; text-decoration: underline; }
.alert {
  padding: 0.8rem 1rem;
  border-radius: 10px;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.7rem;
  font-weight: 500;
  font-size: 0.97rem;
}
.alert-error {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}
.alert-success {
  background: #f0fdf4;
  color: #16a34a;
  border: 1px solid #bbf7d0;
}
@media (max-width: 480px) {
  .login-card { padding: 1.2rem; }
  .login-title { font-size: 1.2rem; }
  .login-logo { width: 48px; height: 48px; font-size: 1.2rem;}
}
footer, .footer { display: none !important; }
</style>
<script>
function togglePassword() {
  const input = document.getElementById('password');
  const icon = document.querySelector('.showpass i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}
</script>