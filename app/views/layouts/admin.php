<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title ?? 'Admin') ?> — GUNPLA SHOP</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Noto+Sans+JP:wght@300;400;500&family=Share+Tech+Mono&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/admin.css">
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="logo-main">GUNPLA</div>
    <div class="logo-sub">ADMIN PANEL</div>
  </div>
  <nav class="sidebar-nav">
    <div class="sidebar-label">Tổng quan</div>
    <a href="<?= BASE_URL ?>/admin" class="nav-item <?= rtrim(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),'/')==rtrim(BASE_URL,'/').'/admin' ? 'active' : '' ?>">
      <span class="nav-icon">▦</span> Dashboard
    </a>
    <div class="sidebar-label">Cửa hàng</div>
    <a href="<?= BASE_URL ?>/admin/products" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'],'/admin/products') ? 'active' : '' ?>">
      <span class="nav-icon">◈</span> Sản phẩm
    </a>
    <a href="<?= BASE_URL ?>/admin/orders" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'],'/admin/orders') ? 'active' : '' ?>">
      <span class="nav-icon">◎</span> Đơn hàng
    </a>
    <a href="<?= BASE_URL ?>/admin/inventory" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'],'/admin/inventory') ? 'active' : '' ?>">
      <span class="nav-icon">≡</span> Kho hàng
    </a>
    <div class="sidebar-label">Tài khoản</div>
    <a href="<?= BASE_URL ?>/" class="nav-item" target="_blank">
      <span class="nav-icon">↗</span> Xem cửa hàng
    </a>
    <a href="<?= BASE_URL ?>/admin/logout" class="nav-item">
      <span class="nav-icon">⏏</span> Đăng xuất
    </a>
  </nav>
  <div class="sidebar-footer">
    <div style="margin-bottom:2px">Đăng nhập với vai trò</div>
    <div class="user-name"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?></div>
  </div>
</aside>

<main class="main">
  <div class="topbar">
    <div class="page-title"><?= htmlspecialchars($title ?? 'Admin') ?></div>
    <div style="font-family:var(--font-m);font-size:10px;color:var(--text-3);letter-spacing:.08em">
      <?= date('d/m/Y H:i') ?>
    </div>
  </div>
  <div class="page-body">
    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="flash <?= $_SESSION['flash']['type'] ?>">
        <?= htmlspecialchars($_SESSION['flash']['msg']) ?>
      </div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <?= $content ?? '' ?>
  </div>
</main>

</body>
<script src="<?= BASE_URL ?>/public/js/admin.js?v=<?= time() ?>"></script>
</html>