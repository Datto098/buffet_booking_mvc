<?php
?>
<div class="container mt-10" >
    <div class="row justify-content-center mt-10">
        <div class="col-md-6">
            <h2 class="mb-4 text-center">Quên Mật Khẩu</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <form action="<?php echo SITE_URL; ?>/auth/forgotPassword" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Nhập email của bạn</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                <button type="submit" class="btn btn-primary w-100">Gửi liên kết đặt lại mật khẩu</button>
            </form>
        </div>
    </div>
</div>