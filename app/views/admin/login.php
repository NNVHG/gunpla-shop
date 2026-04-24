<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width">
<title>Admin Login — GUNPLA SHOP</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Share+Tech+Mono&display=swap" rel="stylesheet">
<style>
:root{--bg:#080909;--panel:#0f1011;--card:#141618;--border:#242729;--gold:#c8a85a;--gold-b:#e8c878;--gold-d:#4a3a18;--red:#c84040;--t1:#e0dcd4;--t2:#7a7874;--t3:#3a3835;--fd:'Bebas Neue',sans-serif;--fm:'Share Tech Mono',monospace;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{background:var(--bg);color:var(--t1);font-family:var(--fm);font-size:13px;min-height:100vh;display:flex;align-items:center;justify-content:center;}
.wrap{width:360px;padding:0 16px;}
.brand{text-align:center;margin-bottom:32px;}
.brand-main{font-family:var(--fd);font-size:36px;letter-spacing:.12em;}
.brand-sub{font-size:9px;color:var(--gold);letter-spacing:.25em;text-transform:uppercase;}
.brand-dot{width:7px;height:7px;background:var(--gold);border-radius:50%;margin:0 auto 10px;}
.card{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:28px;}
.card-title{font-size:10px;color:var(--gold);letter-spacing:.18em;text-transform:uppercase;margin-bottom:22px;padding-bottom:12px;border-bottom:1px solid var(--border);}
label{display:block;font-size:9px;color:var(--t3);letter-spacing:.14em;text-transform:uppercase;margin-bottom:5px;}
input{width:100%;padding:10px 13px;background:var(--panel);border:1px solid var(--border);border-radius:4px;color:var(--t1);font-family:var(--fm);font-size:13px;outline:none;transition:border-color .2s;margin-bottom:14px;}
input:focus{border-color:var(--gold-d);}
input::placeholder{color:var(--t3);}
.btn{width:100%;padding:13px;background:var(--gold);color:var(--bg);border:none;border-radius:4px;font-family:var(--fd);font-size:20px;letter-spacing:.1em;cursor:pointer;transition:background .2s;margin-top:4px;}
.btn:hover{background:var(--gold-b);}
.error{background:rgba(200,64,64,.1);border:1px solid rgba(200,64,64,.3);border-radius:4px;padding:10px 14px;color:#e07070;font-size:11px;margin-bottom:16px;}
.back{display:block;text-align:center;margin-top:16px;font-size:10px;color:var(--t3);letter-spacing:.1em;text-decoration:none;transition:color .2s;}
.back:hover{color:var(--t2);}
</style>
</head>
<body>
<div class="wrap">
  <div class="brand">
    <div class="brand-dot"></div>
    <div class="brand-main">GUNPLA</div>
    <div class="brand-sub">Admin Panel</div>
  </div>
  <div class="card">
    <div class="card-title">// Đăng nhập quản trị</div>
    <?php if(!empty($_SESSION['login_error'])): ?>
      <div class="error"><?=htmlspecialchars($_SESSION['login_error'])?></div>
      <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>
    <form method="POST" action="/admin/loginSubmit">
      <div>
        <label>Email</label>
        <input type="email" name="email" placeholder="admin@gunplashop.vn" required autofocus>
      </div>
      <div>
        <label>Mật khẩu</label>
        <input type="password" name="password" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn">ĐĂNG NHẬP</button>
    </form>
  </div>
  <a href="/" class="back">&larr; Về trang cửa hàng</a>
</div>
</body>
</html>
