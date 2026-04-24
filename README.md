# GUNPLA SHOP
### Đồ án môn Lập trình Web — PHP & MySQL
**Sinh viên:** Nguyễn Ngô Vũ Hoàng Gia  
**Trường:** Đại học Thủ Dầu Một — Khoa Công nghệ Thông tin  

---

## Giới thiệu

Hệ thống thương mại điện tử (E-commerce) chuyên bán mô hình lắp ráp Gunpla, xây dựng bằng **PHP thuần (MVC Pattern)** và **MySQL**. Tính năng nổi bật:

- Cấu trúc CSDL phân cấp 2 tầng (tỷ lệ → dòng sản phẩm)
- Giỏ hàng lưu trong Session, tính phí ship AJAX realtime
- Gửi email xác nhận đơn hàng qua **PHPMailer + Gmail SMTP**
- Admin Panel: Dashboard thống kê, quản lý kho, duyệt đơn hàng

---

## Yêu cầu

- **PHP** >= 8.0
- **MySQL** >= 5.7 / MariaDB >= 10.4
- **Apache** với mod_rewrite (XAMPP / Laragon / WAMP)
- **Composer** (để cài PHPMailer — tùy chọn)

---

## Cài đặt

### 1. Clone / Giải nén vào thư mục web server

```bash
# XAMPP
cp -r gunpla-shop/ C:/xampp/htdocs/

# Laragon
cp -r gunpla-shop/ C:/laragon/www/
```

### 2. Import cơ sở dữ liệu

1. Mở **phpMyAdmin** tại `http://localhost/phpmyadmin`
2. Tạo database mới tên `gunpla_shop` (hoặc để SQL tự tạo)
3. **Import** file `database/gunpla_shop.sql`

### 3. Cấu hình kết nối DB

Mở `config/database.php` và sửa nếu cần:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'gunpla_shop');
define('DB_USER', 'root');
define('DB_PASS', '');       // XAMPP để trống; Laragon điền 'root'
```

### 4. Cài PHPMailer (tùy chọn — cần để gửi email)

```bash
composer require phpmailer/phpmailer
```

Sau đó mở `config/app.php` và điền:
```php
define('MAIL_USER', 'your_gmail@gmail.com');
define('MAIL_PASS', 'xxxx xxxx xxxx xxxx');  // App Password từ Google
```

> **Lấy App Password:** myaccount.google.com → Bảo mật → Xác minh 2 bước → App passwords

### 5. Tạo thư mục uploads (nếu chưa có)

```bash
mkdir -p public/uploads
chmod 755 public/uploads   # Linux/Mac
```

---

## Truy cập

| URL | Mô tả |
|-----|-------|
| `http://localhost/gunpla-shop` | Trang cửa hàng |
| `http://localhost/gunpla-shop/products` | Danh sách sản phẩm |
| `http://localhost/gunpla-shop/admin` | Admin Panel |
| `http://localhost/gunpla-shop/admin/login` | Đăng nhập Admin |

**Tài khoản Admin mặc định:**
- Email: `admin@gunplashop.vn`
- Password: `Admin@123`

---

## Cấu trúc thư mục

```
gunpla-shop/
├── index.php                   ← Front Controller (router)
├── .htaccess                   ← URL Rewrite cho Apache
├── config/
│   ├── database.php            ← Kết nối MySQL (PDO)
│   └── app.php                 ← Cấu hình app + SMTP
├── app/
│   ├── controllers/            ← Xử lý logic HTTP request
│   │   ├── ProductController.php
│   │   ├── CartController.php
│   │   ├── OrderController.php
│   │   └── AdminController.php
│   ├── models/                 ← Tương tác CSDL
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── Category.php
│   │   └── User.php
│   └── views/                  ← Giao diện HTML/PHP
│       ├── layouts/
│       │   ├── main.php        ← Layout chính (shop)
│       │   └── admin.php       ← Layout Admin Panel
│       ├── home/
│       ├── products/
│       ├── orders/
│       └── admin/
├── public/
│   ├── css/shop.css            ← Toàn bộ CSS dark mode
│   ├── js/shop.js              ← JavaScript client-side
│   └── uploads/                ← Ảnh sản phẩm upload
└── database/
    └── gunpla_shop.sql         ← Schema + dữ liệu mẫu
```

---

## Tính năng chính

**Phía khách hàng:**
- Trang chủ với hero section, danh mục, sản phẩm nổi bật
- Danh sách sản phẩm với filter theo grade/scale, sắp xếp, phân trang
- Trang chi tiết sản phẩm với gallery ảnh, sản phẩm liên quan
- Giỏ hàng sidebar AJAX (không reload trang)
- Checkout: chọn tỉnh → tính phí ship realtime → đặt hàng
- Email xác nhận đơn hàng tự động (PHPMailer)

**Admin Panel:**
- Dashboard: thống kê doanh thu, biểu đồ Chart.js, cảnh báo kho
- Quản lý sản phẩm: Thêm/Sửa/Ẩn, upload ảnh
- Quản lý đơn hàng: Filter theo trạng thái, cập nhật workflow AJAX
- Quản lý kho: Điều chỉnh tồn kho nhanh, thanh tiến độ màu

---

## Bảo mật

- Mật khẩu lưu bằng `password_hash()` (BCRYPT)
- Toàn bộ query dùng **PDO Prepared Statements** (chống SQL Injection)
- Output HTML qua `htmlspecialchars()` (chống XSS)
- Session bảo vệ Admin Panel (`requireAdmin()` middleware)

---

*Đồ án CNTT — © 2026 Nguyễn Ngô Vũ Hoàng Gia — Đại học Thủ Dầu Một*
