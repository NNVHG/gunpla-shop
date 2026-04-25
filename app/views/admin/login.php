<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width">
<title>Admin Login — GUNPLA SHOP</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Share+Tech+Mono&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/admin.css?v=<?= time() ?>">
</head>
<body class="login-page-body">
<div class="wrap">
  <div class="brand">
    <div class="brand-dot"></div>
    <div class="brand-main">GUNPLA</div>
    <div class="brand-sub">Admin Panel</div>
  </div>
  <div class="login-card">
    <div class="card-title">// Đăng nhập quản trị</div>
    <?php if(!empty($_SESSION['login_error'])): ?>
      <div class="error"><?=htmlspecialchars($_SESSION['login_error'])?></div>
      <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>
    <form method="POST" action="/admin/loginSubmit">
      <div>
        <label>Email</label>
        <input type="email" name="email" placeholder="admin@gunplashop.vn" required autofocus>
      </div>
      <div>
        <label>Mật khẩu</label>
        <input type="password" name="password" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn-login">ĐĂNG NHẬP</button>
    </form>
  </div>
  <a href="/" class="back">&larr; Về trang cửa hàng</a>
</div>
</body>
</html>
