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
        <span style="font-family:var(--font-mono);font-size:12px;color:var(--text-hint);letter-spacing:.1em">BÌNH DƯƠNG &amp; TP.HCM</span>
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
      <button id="cartClose" style="background:none;border:none;cursor:pointer;color:var(--text-secondary);font-size:20px;line-height:1">✕</button>
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

  <!-- Floating Compare Bar -->
  <div class="compare-bar" id="compareBar" style="display:none;">
    <div class="compare-bar-container">
      <div class="compare-bar-info">
        <span class="compare-title">// SO SÁNH SẢN PHẨM</span>
        <span class="compare-count" id="compareCount">(0/3)</span>
      </div>
      <div class="compare-bar-items" id="compareBarItems">
        <!-- JS dynamic render -->
      </div>
      <div class="compare-bar-actions">
        <button class="btn btn-clear-compare" id="btnClearCompare">XÓA HẾT</button>
        <a href="#" class="btn btn-gold btn-compare-now" id="btnCompareNow">SO SÁNH NGAY</a>
      </div>
    </div>
  </div>

  <?php
  $settingModel = new \App\Models\Setting();
  $chatbotEnabled = $settingModel->get('chatbot_enabled', '1');
  if ($chatbotEnabled === '1'):
  ?>
  <!-- AI Chatbot Widget -->
  <div class="chatbot-container">
    <button class="chatbot-toggle" id="chatbotToggle" title="Trò chuyện với AI Gunpla">
      <span class="chatbot-pulse"></span>
      <span class="chatbot-icon">🤖</span>
    </button>
    
    <div class="chatbot-window" id="chatbotWindow" style="display:none;">
      <div class="chatbot-header">
        <div style="display:flex;align-items:center;gap:10px">
          <span style="font-size:22px">🤖</span>
          <div>
            <div class="chatbot-title">Gunpla AI Assistant</div>
            <div class="chatbot-status">Đang hoạt động</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:12px">
          <button id="chatbotClear" class="chatbot-header-btn" title="Xóa lịch sử chat" style="background:none;border:none;cursor:pointer;font-size:16px;color:var(--text-secondary)">🗑️</button>
          <button id="chatbotClose" class="chatbot-header-btn" style="background:none;border:none;cursor:pointer;font-size:18px;color:var(--text-secondary)">✕</button>
        </div>
      </div>
      <div class="chatbot-body" id="chatbotBody">
        <div class="chatbot-msg system">
          Chào mừng bạn đến với <strong>Gunpla Shop</strong>! Mình là trợ lý AI thông minh chuyên tư vấn về các mô hình Gundam (HG, RG, MG, PG) và dụng cụ lắp ráp. Bạn cần mình trợ giúp gì hôm nay?
        </div>
        <div class="chatbot-quick-suggests">
          <button class="quick-suggest-btn" data-msg="Tôi là người mới chơi thì nên lắp dòng nào?">🆕 Người mới chọn dòng nào?</button>
          <button class="quick-suggest-btn" data-msg="Tư vấn cho tôi một số mẫu HG đẹp có sẵn">🔥 Mẫu HG nổi bật</button>
          <button class="quick-suggest-btn" data-msg="Tôi cần mua dụng cụ lắp ráp gundam cơ bản">🛠️ Dụng cụ lắp ráp</button>
          <button class="quick-suggest-btn" data-msg="Giới thiệu cho tôi các dòng PG đỉnh cao">👑 Mô hình PG cao cấp</button>
        </div>
      </div>
      <div class="chatbot-footer">
        <input type="text" id="chatbotInput" placeholder="Nhập câu hỏi của bạn..." autocomplete="off">
        <button id="chatbotSend">GỬI</button>
      </div>
      <!-- Custom Chat Clear Confirmation Modal -->
      <div class="chatbot-confirm-modal" id="chatbotConfirmModal" style="display:none;">
        <div class="chatbot-confirm-content">
          <div style="font-size:26px;margin-bottom:10px;color:var(--red-accent)">⚠️</div>
          <div class="chatbot-confirm-title">// XÓA LỊCH SỬ CHAT?</div>
          <div class="chatbot-confirm-text">Lịch sử trò chuyện trước đó của bạn sẽ bị xóa vĩnh viễn khỏi phiên làm việc.</div>
          <div class="chatbot-confirm-buttons">
            <button class="btn-confirm-no" id="btnConfirmClearNo">HỦY BỎ</button>
            <button class="btn-confirm-yes" id="btnConfirmClearYes">XÓA HẾT</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

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