<?php
/**
 * app/Views/admin/settings.php
 * @var array $settings
 * @var string $title
 */
?>

<div class="admin-table-wrap" style="max-width: 720px; margin: 0 auto; padding: 28px; background: var(--bg-card); border-radius: 8px;">
  <div style="border-bottom: 1px solid var(--border); padding-bottom: 16px; margin-bottom: 24px;">
    <h2 style="font-family: var(--font-d); font-size: 24px; color: var(--gold); letter-spacing: 0.05em; margin: 0;">// CẤU HÌNH AI & CHATBOT</h2>
    <p style="font-family: var(--font-m); font-size: 11px; color: var(--text-2); margin-top: 4px;">Thiết lập trạng thái hoạt động và phương thức xử lý của trợ lý ảo</p>
  </div>

  <form method="POST" action="<?= BASE_URL ?>/admin/settings">
    <div style="display: flex; flex-direction: column; gap: 24px;">
      
      <!-- Chatbot Enabled Switch -->
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: var(--bg-panel); border: 1px solid var(--border); border-radius: 6px;">
        <div>
          <div style="font-family: var(--font-m); font-size: 12px; color: var(--text-1); text-transform: uppercase; letter-spacing: 0.05em; font-weight: bold;">
            Trạng thái Chatbot
          </div>
          <div style="font-size: 12px; color: var(--text-2); margin-top: 4px;">
            Bật/Tắt bong bóng chat và phản hồi khách hàng ở trang ngoài.
          </div>
        </div>
        <label class="switch-container">
          <input type="checkbox" name="chatbot_enabled" value="1" <?= ($settings['chatbot_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
          <span class="switch-slider"></span>
        </label>
      </div>

      <!-- Chatbot AI Mode Switch -->
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: var(--bg-panel); border: 1px solid var(--border); border-radius: 6px;">
        <div>
          <div style="font-family: var(--font-m); font-size: 12px; color: var(--text-1); text-transform: uppercase; letter-spacing: 0.05em; font-weight: bold;">
            Chế độ AI Mode (Gemini)
          </div>
          <div style="font-size: 12px; color: var(--text-2); margin-top: 4px;">
            Nếu tắt, hệ thống sẽ sử dụng bộ quy tắc Rule-based thủ công (phản hồi từ khóa) thay thế.
          </div>
        </div>
        <label class="switch-container">
          <input type="checkbox" name="chatbot_ai_mode" value="1" <?= ($settings['chatbot_ai_mode'] ?? '1') === '1' ? 'checked' : '' ?>>
          <span class="switch-slider"></span>
        </label>
      </div>

      <!-- Gemini API Key Input -->
      <div style="display: flex; flex-direction: column; gap: 8px; padding: 16px; background: var(--bg-panel); border: 1px solid var(--border); border-radius: 6px;">
        <label style="font-family: var(--font-m); font-size: 11px; color: var(--text-1); text-transform: uppercase; letter-spacing: 0.05em; font-weight: bold;">
          Khóa Gemini API Key (Ghi đè)
        </label>
        <input type="password" name="chatbot_gemini_key" class="form-input" 
               value="<?= htmlspecialchars($settings['chatbot_gemini_key'] ?? '') ?>" 
               placeholder="Nhập API Key để ghi đè cấu hình .env (bỏ trống nếu muốn dùng .env)"
               style="font-family: var(--font-m); letter-spacing: 0.05em; padding: 9px 12px; background: var(--bg-panel); border: 1px solid var(--border); border-radius: 4px; color: var(--text-1); width: 100%; outline: none;">
        <div style="font-size: 11px; color: var(--text-2);">
          * Để trống để tiếp tục sử dụng API Key trong tệp `.env`. Cấu hình này hữu ích khi bạn muốn cập nhật nhanh API Key từ giao diện Admin.
        </div>
      </div>

      <!-- Submit button -->
      <div style="display: flex; justify-content: flex-end; margin-top: 8px;">
        <button type="submit" class="btn btn-gold" style="padding: 12px 30px; font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; font-family: var(--font-m);">
          Lưu cấu hình hệ thống
        </button>
      </div>

    </div>
  </form>
</div>

<style>
/* Custom toggle switch style matching gold premium theme */
.switch-container {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 26px;
}
.switch-container input {
  opacity: 0;
  width: 0;
  height: 0;
}
.switch-slider {
  position: absolute;
  cursor: pointer;
  inset: 0;
  background-color: var(--border);
  transition: .4s;
  border-radius: 34px;
  border: 1px solid var(--border-mid);
}
.switch-slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 2px;
  background-color: var(--text-2);
  transition: .4s;
  border-radius: 50%;
}
.switch-container input:checked + .switch-slider {
  background-color: rgba(200, 168, 90, 0.15);
  border-color: var(--gold);
}
.switch-container input:checked + .switch-slider:before {
  transform: translateX(24px);
  background-color: var(--gold);
}
</style>
