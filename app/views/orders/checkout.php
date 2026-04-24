<?php
/**
 * app/views/orders/checkout.php
 * Trang thanh toán — tích hợp tính phí ship AJAX realtime
 *
 * Biến nhận từ OrderController:
 *   $items         — sản phẩm trong giỏ
 *   $subtotal      — tổng tiền hàng
 *   $shippingZones — mảng tỉnh => phí
 *   $user          — thông tin user đã đăng nhập (hoặc null)
 */
?>
<div style="max-width:1100px;margin:0 auto;padding:40px 24px">

  <!-- Breadcrumb -->
  <div style="font-family:var(--font-mono);font-size:11px;color:var(--text-hint);letter-spacing:0.1em;margin-bottom:32px">
    <a href="/" style="color:var(--text-hint);text-decoration:none">Trang chủ</a>
    <span style="margin:0 8px;color:var(--border-mid)">/</span>
    <a href="/cart" style="color:var(--text-hint);text-decoration:none">Giỏ hàng</a>
    <span style="margin:0 8px;color:var(--border-mid)">/</span>
    <span style="color:var(--gold)">Thanh toán</span>
  </div>

  <h1 style="font-family:var(--font-display);font-size:36px;letter-spacing:0.08em;margin-bottom:32px">
    THANH TOÁN
  </h1>

  <?php if (!empty($_SESSION['order_error'])): ?>
    <div style="background:rgba(200,64,64,0.1);border:1px solid var(--red-accent);border-radius:6px;padding:14px 18px;margin-bottom:24px;color:#e87070;font-size:13px">
      <?= htmlspecialchars($_SESSION['order_error']) ?>
      <?php unset($_SESSION['order_error']); ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="/orders/place" id="checkoutForm">
    <div style="display:grid;grid-template-columns:1fr 380px;gap:32px;align-items:start">

      <!-- ── CỘT TRÁI: Form thông tin ─────────── -->
      <div>
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:28px;margin-bottom:20px">
          <h2 style="font-family:var(--font-mono);font-size:11px;color:var(--gold);letter-spacing:0.15em;text-transform:uppercase;margin:0 0 24px">
            // Thông tin người nhận
          </h2>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
            <!-- Họ tên -->
            <div style="grid-column:1/-1">
              <label class="form-label">Họ và tên *</label>
              <input type="text" name="full_name" class="form-input <?= isset($_SESSION['checkout_errors']['full_name']) ? 'error' : '' ?>"
                     value="<?= htmlspecialchars($_SESSION['checkout_form']['full_name'] ?? $user['full_name'] ?? '') ?>"
                     placeholder="Nguyễn Văn A" required>
              <?php if (isset($_SESSION['checkout_errors']['full_name'])): ?>
                <div class="form-error"><?= $_SESSION['checkout_errors']['full_name'] ?></div>
              <?php endif; ?>
            </div>

            <!-- Số điện thoại -->
            <div>
              <label class="form-label">Số điện thoại *</label>
              <input type="tel" name="phone" class="form-input <?= isset($_SESSION['checkout_errors']['phone']) ? 'error' : '' ?>"
                     value="<?= htmlspecialchars($_SESSION['checkout_form']['phone'] ?? $user['phone'] ?? '') ?>"
                     placeholder="0901 234 567" required>
              <?php if (isset($_SESSION['checkout_errors']['phone'])): ?>
                <div class="form-error"><?= $_SESSION['checkout_errors']['phone'] ?></div>
              <?php endif; ?>
            </div>

            <!-- Email (không bắt buộc) -->
            <div>
              <label class="form-label">Email <span style="color:var(--text-hint)">(để nhận xác nhận)</span></label>
              <input type="email" name="email" class="form-input"
                     value="<?= htmlspecialchars($_SESSION['checkout_form']['email'] ?? $user['email'] ?? '') ?>"
                     placeholder="example@gmail.com">
            </div>
          </div>

          <!-- Tỉnh/thành -->
          <div style="margin-bottom:16px">
            <label class="form-label">Tỉnh / Thành phố *</label>
            <select name="province" id="provinceSelect" class="form-input <?= isset($_SESSION['checkout_errors']['province']) ? 'error' : '' ?>" required onchange="updateShipping()">
              <option value="">-- Chọn tỉnh/thành --</option>
              <?php foreach (array_keys($shippingZones) as $province): ?>
                <?php if ($province === 'default') continue; ?>
                <option value="<?= htmlspecialchars($province) ?>"
                  <?= ($_SESSION['checkout_form']['province'] ?? '') === $province ? 'selected' : '' ?>>
                  <?= htmlspecialchars($province) ?>
                </option>
              <?php endforeach; ?>
              <option value="Khác">Tỉnh/thành khác</option>
            </select>
            <?php if (isset($_SESSION['checkout_errors']['province'])): ?>
              <div class="form-error"><?= $_SESSION['checkout_errors']['province'] ?></div>
            <?php endif; ?>
          </div>

          <!-- Địa chỉ chi tiết -->
          <div style="margin-bottom:16px">
            <label class="form-label">Địa chỉ chi tiết *</label>
            <textarea name="address" class="form-input <?= isset($_SESSION['checkout_errors']['address']) ? 'error' : '' ?>"
                      rows="2" placeholder="Số nhà, tên đường, phường/xã..." required><?= htmlspecialchars($_SESSION['checkout_form']['address'] ?? $user['address'] ?? '') ?></textarea>
            <?php if (isset($_SESSION['checkout_errors']['address'])): ?>
              <div class="form-error"><?= $_SESSION['checkout_errors']['address'] ?></div>
            <?php endif; ?>
          </div>

          <!-- Ghi chú -->
          <div>
            <label class="form-label">Ghi chú đơn hàng <span style="color:var(--text-hint)">(tùy chọn)</span></label>
            <textarea name="note" class="form-input" rows="2"
                      placeholder="Giao giờ hành chính, để ở bảo vệ..."><?= htmlspecialchars($_SESSION['checkout_form']['note'] ?? '') ?></textarea>
          </div>

        </div>

        <?php unset($_SESSION['checkout_errors'], $_SESSION['checkout_form']); ?>
      </div>

      <!-- ── CỘT PHẢI: Tóm tắt đơn hàng ─────── -->
      <div style="position:sticky;top:80px">
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:24px;margin-bottom:16px">
          <h2 style="font-family:var(--font-mono);font-size:11px;color:var(--gold);letter-spacing:0.15em;text-transform:uppercase;margin:0 0 20px">
            // Đơn hàng của bạn
          </h2>

          <!-- Danh sách sản phẩm -->
          <?php foreach ($items as $item): ?>
            <div style="display:flex;gap:12px;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid var(--border)">
              <!-- Thumbnail placeholder -->
              <div style="width:48px;height:48px;background:var(--bg-surface);border:1px solid var(--border);border-radius:4px;display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-size:12px;color:var(--gold);flex-shrink:0">
                <?= htmlspecialchars($item['grade']) ?>
              </div>
              <div style="flex:1;min-width:0">
                <div style="font-size:12px;color:var(--text-primary);line-height:1.4;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                  <?= htmlspecialchars($item['name']) ?>
                </div>
                <div style="font-family:var(--font-mono);font-size:10px;color:var(--text-hint)">
                  <?= htmlspecialchars($item['scale']) ?> · SL: <?= $item['qty'] ?>
                </div>
              </div>
              <div style="font-family:var(--font-display);font-size:16px;color:var(--gold);white-space:nowrap;flex-shrink:0">
                <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?>đ
              </div>
            </div>
          <?php endforeach; ?>

          <!-- Tổng tiền -->
          <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:13px">
            <span style="color:var(--text-secondary)">Tạm tính</span>
            <span style="color:var(--text-primary)"><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
          </div>
          <div style="display:flex;justify-content:space-between;margin-bottom:16px;font-size:13px">
            <span style="color:var(--text-secondary)">Phí vận chuyển</span>
            <span id="shippingFeeDisplay" style="color:var(--text-primary)">— Chọn tỉnh/thành</span>
          </div>
          <div style="display:flex;justify-content:space-between;padding-top:16px;border-top:1px solid var(--border)">
            <span style="font-family:var(--font-mono);font-size:12px;color:var(--text-primary);letter-spacing:0.08em">TỔNG CỘNG</span>
            <span id="totalDisplay" style="font-family:var(--font-display);font-size:26px;color:var(--gold)">
              <?= number_format($subtotal, 0, ',', '.') ?>đ
            </span>
          </div>
        </div>

        <!-- Nút đặt hàng -->
        <button type="submit" id="submitBtn"
          style="width:100%;padding:16px;background:var(--gold);color:var(--bg-void);border:none;border-radius:4px;font-family:var(--font-display);font-size:22px;letter-spacing:0.1em;cursor:pointer;transition:background 0.2s">
          ĐẶT HÀNG
        </button>
        <a href="/cart" style="display:block;text-align:center;margin-top:12px;font-family:var(--font-mono);font-size:11px;color:var(--text-hint);text-decoration:none;letter-spacing:0.08em">
          &larr; Quay lại giỏ hàng
        </a>
      </div>

    </div>
  </form>
</div>

<!-- Inline styles cho form elements -->
<style>
.form-label {
  display: block;
  font-family: var(--font-mono);
  font-size: 10px;
  color: var(--text-hint);
  letter-spacing: 0.12em;
  text-transform: uppercase;
  margin-bottom: 6px;
}
.form-input {
  width: 100%;
  padding: 10px 14px;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: 5px;
  color: var(--text-primary);
  font-family: var(--font-body);
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s;
  resize: vertical;
}
.form-input:focus { border-color: var(--gold-dim); }
.form-input.error { border-color: var(--red-accent); }
.form-input::placeholder { color: var(--text-hint); }
.form-error { font-size: 11px; color: #e87070; margin-top: 4px; }
select.form-input option { background: var(--bg-surface); }
</style>

<script>
const subtotal = <?= $subtotal ?>;

async function updateShipping() {
  const province = document.getElementById('provinceSelect').value;
  if (!province) return;

  const res  = await fetch('/cart/shipping?province=' + encodeURIComponent(province));
  const data = await res.json();

  document.getElementById('shippingFeeDisplay').textContent =
    data.shipping_fee.toLocaleString('vi-VN') + 'đ';
  document.getElementById('totalDisplay').textContent =
    data.total.toLocaleString('vi-VN') + 'đ';
}

// Chặn submit khi chưa chọn tỉnh
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
  if (!document.getElementById('provinceSelect').value) {
    e.preventDefault();
    document.getElementById('provinceSelect').classList.add('error');
    document.getElementById('provinceSelect').focus();
  }
});
</script>
