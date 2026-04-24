<?php
/**
 * config/app.php — Cấu hình toàn cục ứng dụng
 *
 * QUAN TRỌNG: Đặt file này vào .gitignore nếu đẩy lên GitHub
 * để tránh lộ mật khẩu email và thông tin nhạy cảm.
 */

// ─── Thông tin ứng dụng ──────────────────────────────
define('APP_NAME',    'GUNPLA SHOP');
define('APP_URL',     'http://localhost/gunpla-shop');  // Đổi khi deploy
define('APP_DEBUG',   true);    // false khi deploy lên server thật

// ─── Cấu hình Gmail SMTP (PHPMailer) ────────────────
// Bước 1: Bật 2-Step Verification trên tài khoản Gmail
// Bước 2: Tạo App Password tại: myaccount.google.com/apppasswords
// Bước 3: Điền App Password vào MAIL_PASS (không phải mật khẩu Gmail thật)
define('MAIL_USER',   'royalmonsterhg@gmail.com');
define('MAIL_PASS',   'jlqe ywfn kpwt vfqr');  // App Password từ Google
define('MAIL_FROM_NAME', APP_NAME);

// ─── Cấu hình session ────────────────────────────────
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
session_name('GUNPLA_SESS');

// ─── Xử lý lỗi ───────────────────────────────────────
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', BASE_PATH . '/logs/error.log');
}
