<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title ?? 'Admin') ?> — GUNPLA SHOP</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Noto+Sans+JP:wght@300;400;500&family=Share+Tech+Mono&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/admin.css?v=<?= time() ?>">
</head>
<body>
<script>
  if (localStorage.getItem('theme') === 'light') {
    document.body.classList.add('light-theme');
  }
</script>

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
    <a href="<?= BASE_URL ?>/admin/categories" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'],'/admin/categories') ? 'active' : '' ?>">
      <span class="nav-icon">▤</span> Danh mục
    </a>
    <a href="<?= BASE_URL ?>/admin/orders" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'],'/admin/orders') ? 'active' : '' ?>">
      <span class="nav-icon">◎</span> Đơn hàng
    </a>
    <a href="<?= BASE_URL ?>/admin/inventory" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'],'/admin/inventory') ? 'active' : '' ?>">
      <span class="nav-icon">≡</span> Kho hàng
    </a>
    <a href="<?= BASE_URL ?>/admin/users" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'],'/admin/users') ? 'active' : '' ?>">
      <span class="nav-icon">👥</span> Khách hàng
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
    <div class="page-title"><?= htmlspecialchars($title ?? 'Dashboard') ?></div>
    <div style="display:flex; align-items:center; gap: 12px;">
      <button class="btn-icon theme-toggle" title="Giao diện Sáng/Tối" style="display:flex; align-items:center; justify-content:center; font-size:16px; border:none; background:transparent; cursor:pointer;">
        🌓
      </button>
      <a href="<?= BASE_URL ?>/" target="_blank" style="font-family:var(--font-m);font-size:10px;color:var(--gold);letter-spacing:.1em;border:1px solid var(--gold-dim);padding:5px 10px;border-radius:4px;transition:all .2s">
        &rarr; XEM WEBSITE
      </a>
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
<script>const BASE_URL = '<?= BASE_URL ?>';
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.theme-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      document.body.classList.toggle('light-theme');
      localStorage.setItem('theme', document.body.classList.contains('light-theme') ? 'light' : 'dark');
    });
  });
});
</script>
<script src="<?= BASE_URL ?>/public/js/admin.js?v=<?= time() ?>"></script>
</html>