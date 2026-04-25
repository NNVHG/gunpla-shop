<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Đăng nhập') ?> — GUNPLA SHOP</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/auth.css?v=<?= time() ?>">
</head>
<body class="auth-page">

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <a href="<?= BASE_URL ?>/" class="auth-logo">GUNPLA<span>SHOP</span></a>
            <div class="auth-subtitle">// ĐĂNG NHẬP HỆ THỐNG</div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/user/loginSubmit">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '/') ?>">

            <div class="form-group">
                <label class="form-label">Email / Tài khoản</label>
                <input type="email" name="email" class="form-control" placeholder="pilot@gunplashop.vn" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" id="loginPass" class="form-control" placeholder="••••••••" required>
                <button type="button" class="btn-toggle-pass" data-target="loginPass" title="Hiện mật khẩu">👁️</button>
            </div>

            <button type="submit" class="btn-submit">BẮT ĐẦU KẾT NỐI</button>
        </form>

        <div class="auth-footer">
            Chưa có tài khoản? <a href="<?= BASE_URL ?>/user/register">Đăng ký ngay</a><br><br>
            <a href="<?= BASE_URL ?>/" style="color: var(--text-muted); font-family: 'Share Tech Mono';">← Trở về căn cứ (Trang chủ)</a>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/public/js/auth.js?v=<?= time() ?>"></script>
</body>
</html>