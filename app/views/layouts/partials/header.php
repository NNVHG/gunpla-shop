<nav class="navbar">
  <div class="container">
    <div class="navbar-inner">
      <a href="/" class="logo">
        <span class="logo-dot"></span>
        <span class="logo-main">GUNPLA</span>
        <span class="logo-sub">SHOP</span>
      </a>
      <ul class="nav-links">
        <li><a href="/gunpla-shop/app/views/layouts/main.php"         <?= $_SERVER['REQUEST_URI']==='/' ?'class="active"':'' ?>>Trang chủ</a></li>
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
            <a href="<?= BASE_URL ?>/user/logout" class="btn-icon" title="Đăng xuất" style="font-size:13px;text-decoration:none">⏏</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/user/loginForm" class="btn-primary" style="text-decoration:none">Đăng nhập</a>
        <?php endif; ?>
        <button class="btn-icon" id="cartBtn">
          &#9635;
          <span class="cart-count" id="cartBadge"><?= array_sum(array_column($_SESSION['cart'] ?? [], 'qty')) ?: '' ?></span>
        </button>
      </div>
    </div>
  </div>
</nav>
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