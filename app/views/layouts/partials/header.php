<nav class="navbar">
  <div class="container">
    <div class="navbar-inner">
      <a href="<?= BASE_URL ?>/" class="logo">
        <span class="logo-dot"></span>
        <span class="logo-main">GUNPLA</span>
        <span class="logo-sub">SHOP</span>
      </a>
      
      <ul class="nav-links">
        <?php
          $uri = $_SERVER['REQUEST_URI'];
          // Cập nhật điều kiện kiểm tra cho Dụng cụ: dựa vào tham số group=tools
          $isTool = isset($_GET['group']) && $_GET['group'] === 'tools';
          
          // isProduct sẽ true nếu đang ở trang products nhưng không phải là Dụng cụ
          $isProduct = str_contains($uri, '/products') && !$isTool;
        ?>
        <li><a href="<?= BASE_URL ?>/" <?= $uri==='/' || $uri==='/gunpla-shop/' ? 'class="active"' : '' ?>>Trang chủ</a></li>
        
        <li><a href="<?= BASE_URL ?>/products" class="nav-link <?= ($isProduct && empty($_GET['group'])) ? 'active' : '' ?>">Sản phẩm</a></li>
        
        <li><a href="<?= BASE_URL ?>/products?group=gunpla" class="nav-link <?= (isset($_GET['group']) && $_GET['group'] === 'gunpla') ? 'active' : '' ?>">Gunpla</a></li>
        
        <li><a href="<?= BASE_URL ?>/products?group=tools&sort=newest" <?= $isTool ? 'class="active"' : '' ?>>Dụng cụ</a></li>
        
        <li><a href="<?= BASE_URL ?>/news" <?= str_contains($uri, '/news') ? 'class="active"' : '' ?>>Tin tức</a></li>
      </ul>

      <div class="search-wrap" style="position:relative;flex:1;max-width:340px">
        <span class="search-icon">&#9906;</span>
        <input type="text" id="globalSearch" placeholder="Tìm HG, MG, RG..." autocomplete="off" style="padding-right:36px">
        <button type="button" id="voiceSearchBtn" class="voice-search-btn" title="Tìm kiếm bằng giọng nói" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-hint);cursor:pointer;font-size:16px;padding:4px;display:flex;align-items:center;justify-content:center;transition:color 0.2s;">🎙️</button>
        <div id="searchDropdown" class="search-dropdown"></div>
      </div>
      
      <div class="nav-actions">
        <button class="btn-icon theme-toggle" title="Giao diện Sáng/Tối" style="display:flex; align-items:center; justify-content:center; font-size:18px;">
          🌓
        </button>
        <?php if (!empty($_SESSION['user'])): ?>
          <a href="<?= BASE_URL ?>/user/profile?tab=wishlist" class="btn-icon" title="Sản phẩm yêu thích" style="text-decoration:none; display:flex; align-items:center; justify-content:center;">
            ♥
          </a>

          <?php
            require_once APP_PATH . '/Models/Notification.php';
            $notifyModel = new \App\Models\Notification();
            $unreadNotifyCount = $notifyModel->getUnreadCount((int)$_SESSION['user']['id']);
          ?>
          <a href="<?= BASE_URL ?>/user/profile?tab=notifications" class="btn-icon" id="notifyBellBtn" title="Thông báo" style="text-decoration:none; display:flex; align-items:center; justify-content:center; position:relative;">
            🔔
            <?php if ($unreadNotifyCount > 0): ?>
              <span class="notify-badge" style="position:absolute; top:-5px; right:-5px; background:#e63946; color:#fff; border-radius:50%; width:16px; height:16px; font-size:11px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-family:var(--font-mono)">
                <?= $unreadNotifyCount ?>
              </span>
            <?php endif; ?>
          </a>

          <a href="<?= BASE_URL ?>/user/profile" class="btn-icon" title="Tài khoản" style="text-decoration:none; display:flex; align-items:center; justify-content:center; font-size:16px;">
            👤
          </a>

          <span style="font-family:var(--font-mono);font-size:12px;color:var(--gold);letter-spacing:.08em;max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin:0 10px;">
            <?= htmlspecialchars($_SESSION['user']['name']) ?>
          </span>
          
          <a href="<?= BASE_URL ?>/user/logout" class="btn-icon" title="Đăng xuất" style="font-size:16px;text-decoration:none;display:flex;align-items:center;justify-content:center">
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

        <button class="btn-icon hamburger-btn" id="menuToggleBtn" style="display: none;" title="Menu">
          ☰
        </button>
      </div>
    </div>
  </div>
</nav>