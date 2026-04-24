<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title ?? 'GUNPLA SHOP') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Noto+Sans+JP:wght@300;400;500&family=Share+Tech+Mono&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/shop.css">
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

<?php 
// Nhúng Header
include APP_PATH . '/views/layouts/partials/header.php'; 
?>

<main><?= $content ?? '' ?></main>

<?php 
// Nhúng Footer
include APP_PATH . '/views/layouts/partials/footer.php'; 
?>

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
