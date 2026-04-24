<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title ?? 'GUNPLA SHOP') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Noto+Sans+JP:wght@300;400;500&family=Share+Tech+Mono&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/gunpla-shop/public/css/shop.css">
</head>
<body>

<div class="topbar">
  <div class="container">
    <div class="topbar-inner">
      <span class="topbar-notice">MIỄN PHÍ VẬN CHUYỂN cho đơn từ <span>500.000đ</span></span>
      <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-hint);letter-spacing:.1em">BÌNH DƯƠNG &amp; TP.HCM</span>
    </div>
  </div>
</div>

<nav class="navbar">
  <div class="container">
    <div class="navbar-inner">
      <a href="/" class="logo">
        <span class="logo-dot"></span>
        <span class="logo-main">GUNPLA</span>
        <span class="logo-sub">SHOP</span>
      </a>
      <ul class="nav-links">
        <li><a href="/"         <?= $_SERVER['REQUEST_URI']==='/' ?'class="active"':'' ?>>Trang chủ</a></li>
        <li><a href="/products" <?= str_starts_with($_SERVER['REQUEST_URI'],'/products')?'class="active"':'' ?>>Sản phẩm</a></li>
        <li><a href="/products?type=tool">Dụng cụ</a></li>
        <li><a href="#">Tin tức</a></li>
      </ul>
      <div class="search-wrap" style="position:relative;flex:1;max-width:340px">
        <span class="search-icon">&#9906;</span>
        <input type="text" id="globalSearch" placeholder="Tìm HG, MG, RG..." autocomplete="off">
        <div id="searchDropdown" class="search-dropdown"></div>
      </div>
      <div class="nav-actions">
        <?php if (!empty($_SESSION['user'])): ?>
          <span style="font-family:var(--font-mono);font-size:10px;color:var(--gold);letter-spacing:.08em;max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
            <?= htmlspecialchars($_SESSION['user']['name']) ?>
          </span>
          <a href="/gunpla-shop/auth/logout" class="btn-icon" title="Đăng xuất" style="font-size:13px;text-decoration:none">⏏</a>
        <?php else: ?>
          <a href="/gunpla-shop/app/views/user/login.php" class="btn-primary" style="text-decoration:none">Đăng nhập</a>
        <?php endif; ?>
        <button class="btn-icon" id="cartBtn">
          &#9635;
          <span class="cart-count" id="cartBadge"><?= array_sum(array_column($_SESSION['cart'] ?? [], 'qty')) ?: '' ?></span>
        </button>
      </div>
    </div>
  </div>
</nav>

<main><?= $content ?? '' ?></main>

<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="logo" style="margin-bottom:10px"><span class="logo-dot"></span><span class="logo-main">GUNPLA</span><span class="logo-sub">SHOP</span></div>
        <p style="font-size:12px;color:var(--text-hint);line-height:1.8;max-width:260px">Chuyên cung cấp mô hình lắp ráp Bandai chính hãng — Bình Dương &amp; TP.HCM.</p>
      </div>
      <div>
        <div class="footer-col-title">Sản phẩm</div>
        <ul class="footer-links">
          <li><a href="/products?grade=HG">High Grade (HG)</a></li>
          <li><a href="/products?grade=MG">Master Grade (MG)</a></li>
          <li><a href="/products?grade=RG">Real Grade (RG)</a></li>
          <li><a href="/products?grade=PG">Perfect Grade (PG)</a></li>
        </ul>
      </div>
      <div>
        <div class="footer-col-title">Hỗ trợ</div>
        <ul class="footer-links">
          <li><a href="#">Hướng dẫn mua hàng</a></li>
          <li><a href="#">Chính sách đổi trả</a></li>
          <li><a href="#">Tra cứu đơn hàng</a></li>
        </ul>
      </div>
      <div>
        <div class="footer-col-title">Liên hệ</div>
        <ul class="footer-links">
          <li><a href="#">info@gunplashop.vn</a></li>
          <li><a href="#">Facebook</a></li>
          <li><a href="#">Shopee Store</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span class="footer-copy">© 2026 GUNPLA SHOP — Đồ án CNTT — Đại học Thủ Dầu Một</span>
      <span class="footer-copy">Nguyễn Ngô Vũ Hoàng Gia</span>
    </div>
  </div>
</footer>

<!-- CART SIDEBAR -->
<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-sidebar" id="cartSidebar">
  <div class="cart-header">
    <span class="cart-title">GIỎ HÀNG</span>
    <button id="cartClose" style="background:none;border:none;cursor:pointer;color:var(--text-secondary);font-size:18px;line-height:1">✕</button>
  </div>
  <div class="cart-body" id="cartBody"></div>
  <div class="cart-footer">
    <div class="cart-total-row">
      <span class="cart-total-label">Tạm tính</span>
      <span class="cart-total-val" id="cartTotal">0đ</span>
    </div>
    <a href="/orders/checkout" class="btn-checkout">THANH TOÁN</a>
  </div>
</div>

<script>
// Dữ liệu giỏ từ PHP session — khởi tạo cho JS
window.__CART__ = <?= json_encode(array_values($_SESSION['cart'] ?? [])) ?>;
</script>
<script src="/public/js/shop.js"></script>
</body>
</html>
