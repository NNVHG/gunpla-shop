<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Đăng ký') ?> — GUNPLA SHOP</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/auth.css?v=<?= time() ?>">
</head>
<body class="auth-page">

<div class="auth-wrapper" style="max-width: 500px;">
    <div class="auth-card">
        <div class="auth-header">
            <a href="<?= BASE_URL ?>/" class="auth-logo">GUNPLA<span>SHOP</span></a>
            <div class="auth-subtitle">// GIA NHẬP LỰC LƯỢNG</div>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/user/registerSubmit">
            
            <div class="form-group">
                <label class="form-label">Họ và Tên *</label>
                <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($old['full_name'] ?? '') ?>" placeholder="VD: Amuro Ray" required>
                <?php if (isset($errors['full_name'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['full_name']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="pilot@gunplashop.vn" required>
                <?php if (isset($errors['email'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['email']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Số điện thoại (Tùy chọn)</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" placeholder="09xxxxxxxxx">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Mật khẩu *</label>
                    <input type="password" name="password" id="regPass" class="form-control" placeholder="••••••••" required>
                    <button type="button" class="btn-toggle-pass" data-target="regPass">👁️</button>
                    <?php if (isset($errors['password'])): ?>
                        <span class="error-text"><?= htmlspecialchars($errors['password']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Xác nhận MK *</label>
                    <input type="password" name="password_confirm" id="regPassConfirm" class="form-control" placeholder="••••••••" required>
                    <button type="button" class="btn-toggle-pass" data-target="regPassConfirm">👁️</button>
                    <?php if (isset($errors['password_confirm'])): ?>
                        <span class="error-text"><?= htmlspecialchars($errors['password_confirm']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn-submit">ĐĂNG KÝ HỒ SƠ</button>
        </form>

        <div class="auth-footer">
            Đã là Pilot? <a href="<?= BASE_URL ?>/user/login">Đăng nhập tại đây</a><br><br>
            <a href="<?= BASE_URL ?>/" style="color: var(--text-muted); font-family: 'Share Tech Mono';">← Trở về căn cứ</a>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/public/js/auth.js?v=<?= time() ?>"></script>
</body>
</html>