<nav class="navbar">
  <div class="container">
    <div class="navbar-inner">
      <a href="<?= BASE_URL ?>/" class="logo">
        <span class="logo-dot"></span>
        <span class="logo-main">GUNPLA</span>
        <span class="logo-sub">SHOP</span>
      </a>
      <ul class="nav-links">
        <li><a href="<?= BASE_URL ?>/" <?= $_SERVER['REQUEST_URI']==='/' ?'class="active"':'' ?>>Trang chủ</a></li>
        <li><a href="<?= BASE_URL ?>/products" <?= str_starts_with($_SERVER['REQUEST_URI'],'/products')?'class="active"':'' ?>>Sản phẩm</a></li>
        <li><a href="<?= BASE_URL ?>/products?category_slug=dung-cu-co-ban">Dụng cụ</a></li>
        <li><a href="#">Tin tức</a></li>
      </ul>
      <div class="search-wrap" style="position:relative;flex:1;max-width:340px">
        <span class="search-icon">&#9906;</span>
        <input type="text" id="globalSearch" placeholder="Tìm HG, MG, RG..." autocomplete="off">
        <div id="searchDropdown" class="search-dropdown"></div>
      </div>
      
      <div class="nav-actions">
        <?php if (!empty($_SESSION['user'])): ?>
          
          <a href="<?= BASE_URL ?>/user/profile?tab=wishlist" class="btn-icon" title="Sản phẩm yêu thích" style="text-decoration:none; display:flex; align-items:center; justify-content:center;">
            ♥
          </a>

          <a href="<?= BASE_URL ?>/user/profile" class="btn-icon" title="Tài khoản" style="text-decoration:none; display:flex; align-items:center; justify-content:center; font-size:14px;">
            👤
          </a>

          <span style="font-family:var(--font-mono);font-size:10px;color:var(--gold);letter-spacing:.08em;max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin:0 10px;">
            <?= htmlspecialchars($_SESSION['user']['name']) ?>
          </span>
          
          <a href="<?= BASE_URL ?>/user/logout" class="btn-icon" title="Đăng xuất" style="font-size:14px;text-decoration:none;display:flex;align-items:center;justify-content:center">
            ⏏
          </a>
          
        <?php else: ?>
          <a href="<?= BASE_URL ?>/user/login" class="btn-primary" style="text-decoration:none">Đăng nhập</a>
        <?php endif; ?>
        
        <button class="btn-icon" id="cartBtn" style="display:flex; align-items:center; justify-content:center;">
          &#9635;
          <span class="cart-count" id="cartBadge">
            <?= array_sum(array_column($_SESSION['cart'] ?? [], 'qty')) ?: '' ?>
          </span>
        </button>
      </div>
      </div>
  </div>
</nav>