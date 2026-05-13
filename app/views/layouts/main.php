<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'GUNPLA SHOP') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Noto+Sans+JP:wght@300;400;500&family=Share+Tech+Mono&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/shop.css?v=<?= time() ?>">
  <script>
    if (localStorage.getItem('theme') === 'light') {
      document.documentElement.classList.add('light-theme');
    }
  </script>
</head>

<body>
  <script>
    if (localStorage.getItem('theme') === 'light') {
      document.body.classList.add('light-theme');
    }
  </script>

  <div class="topbar">
    <div class="container">
      <div class="topbar-inner">
        <span class="topbar-notice">MIỄN PHÍ VẬN CHUYỂN cho đơn từ <span>500.000đ</span></span>
        <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-hint);letter-spacing:.1em">BÌNH DƯƠNG &amp; TP.HCM</span>
      </div>
    </div>
  </div>

  <?php
  include APP_PATH . '/views/layouts/partials/header.php';
  ?>

  <main><?= $content ?? '' ?></main>

  <?php
  include APP_PATH . '/views/layouts/partials/footer.php';
  ?>

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
      <a href="<?= BASE_URL ?>/orders/checkout" class="btn-checkout">THANH TOÁN</a>
    </div>
  </div>

  <script>
    window.BASE_URL = '<?= BASE_URL ?>';
    window.__CART__ = <?= json_encode(array_values($_SESSION['cart'] ?? [])) ?>;

    document.addEventListener('DOMContentLoaded', () => {
      if (localStorage.getItem('theme') === 'light') {
        document.body.classList.add('light-theme');
      }
      document.querySelectorAll('.theme-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
          document.body.classList.toggle('light-theme');
          localStorage.setItem('theme', document.body.classList.contains('light-theme') ? 'light' : 'dark');
        });
      });
    });
  </script>
  <script src="<?= BASE_URL ?>/public/js/shop.js?v=<?= time() ?>"></script>

</html>