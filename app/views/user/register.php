<?php /* app/views/user/register.php — Biến: $errors, $old */ ?>
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;padding:40px 24px">
  <div style="width:100%;max-width:460px">
    <div style="text-align:center;margin-bottom:32px">
      <h1 style="font-family:var(--font-display);font-size:36px;letter-spacing:.1em">ĐĂNG KÝ</h1>
      <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-hint);letter-spacing:.1em;margin-top:6px">// TẠO TÀI KHOẢN MỚI</p>
    </div>
    <div class="form-card">
      <form method="POST" action="/user/registerSubmit">
        <div class="form-group">
          <label class="form-label">Họ và tên *</label>
          <input type="text" name="full_name" class="form-input <?= isset($errors['full_name'])?'error':'' ?>"
                 value="<?= htmlspecialchars($old['full_name'] ?? '') ?>" placeholder="Nguyễn Văn A" required>
          <?php if (isset($errors['full_name'])): ?><div class="form-error"><?= $errors['full_name'] ?></div><?php endif; ?>
        </div>
        <div class="form-group">
          <label class="form-label">Email *</label>
          <input type="email" name="email" class="form-input <?= isset($errors['email'])?'error':'' ?>"
                 value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="your@email.com" required>
          <?php if (isset($errors['email'])): ?><div class="form-error"><?= $errors['email'] ?></div><?php endif; ?>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div class="form-group">
            <label class="form-label">Mật khẩu *</label>
            <input type="password" name="password" class="form-input <?= isset($errors['password'])?'error':'' ?>"
                   placeholder="Tối thiểu 8 ký tự" required>
            <?php if (isset($errors['password'])): ?><div class="form-error"><?= $errors['password'] ?></div><?php endif; ?>
          </div>
          <div class="form-group">
            <label class="form-label">Xác nhận mật khẩu *</label>
            <input type="password" name="password_confirm" class="form-input <?= isset($errors['password_confirm'])?'error':'' ?>"
                   placeholder="Nhập lại" required>
            <?php if (isset($errors['password_confirm'])): ?><div class="form-error"><?= $errors['password_confirm'] ?></div><?php endif; ?>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Số điện thoại</label>
          <input type="tel" name="phone" class="form-input" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" placeholder="0901 234 567">
        </div>
        <button type="submit" style="width:100%;padding:13px;background:var(--gold);color:var(--bg-void);border:none;border-radius:4px;font-family:var(--font-display);font-size:22px;letter-spacing:.1em;cursor:pointer;margin-top:8px">
          TẠO TÀI KHOẢN
        </button>
      </form>
    </div>
    <p style="text-align:center;margin-top:20px;font-size:13px;color:var(--text-hint)">
      Đã có tài khoản? <a href="/user/login" style="color:var(--gold)">Đăng nhập</a>
    </p>
  </div>
</div>
