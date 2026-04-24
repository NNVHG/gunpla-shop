<?php /* app/views/user/login.php */ ?>
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;padding:40px 24px">
  <div style="width:100%;max-width:400px">
    <div style="text-align:center;margin-bottom:32px">
      <h1 style="font-family:var(--font-display);font-size:36px;letter-spacing:.1em">ĐĂNG NHẬP</h1>
      <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-hint);letter-spacing:.1em;margin-top:6px">// GUNPLA SHOP ACCOUNT</p>
    </div>
    <?php if ($error): ?>
      <div style="background:rgba(200,64,64,.1);border:1px solid rgba(200,64,64,.3);border-radius:5px;padding:12px 16px;margin-bottom:20px;color:#e07070;font-size:13px">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>
    <div class="form-card">
      <form method="POST" action="/user/loginSubmit">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '/') ?>">
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-input" placeholder="your@email.com" required autofocus>
        </div>
        <div class="form-group">
          <label class="form-label">Mật khẩu</label>
          <input type="password" name="password" class="form-input" placeholder="••••••••" required>
        </div>
        <button type="submit" style="width:100%;padding:13px;background:var(--gold);color:var(--bg-void);border:none;border-radius:4px;font-family:var(--font-display);font-size:22px;letter-spacing:.1em;cursor:pointer;margin-top:8px">
          ĐĂNG NHẬP
        </button>
      </form>
    </div>
    <p style="text-align:center;margin-top:20px;font-size:13px;color:var(--text-hint)">
      Chưa có tài khoản? <a href="/user/register" style="color:var(--gold)">Đăng ký ngay</a>
    </p>
  </div>
</div>
