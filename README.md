```markdown
# 🤖 Gunpla Shop - Nền tảng Thương mại Điện tử Mô hình Gundam

Dự án **Gunpla Shop** là một hệ thống website thương mại điện tử chuyên cung cấp mô hình lắp ráp (Gunpla) và dụng cụ/phụ kiện mô hình. Hệ thống được phát triển dựa trên mô hình MVC (Model-View-Controller) tùy chỉnh bằng PHP thuần, tích hợp giao diện hiện đại với cơ chế Dark/Light Mode tự động và quản lý dữ liệu với hiệu suất cao.

---

## ✨ Tính năng nổi bật (Features)

### 👤 Giao diện Người dùng (Customer/Frontend)
- **UI/UX Hiện đại:** Thiết kế tinh gọn, hỗ trợ tính năng chuyển đổi **Dark/Light Mode** đồng bộ toàn hệ thống bằng CSS Variables.
- **Quản lý Sản phẩm:** Hiển thị chi tiết mô hình theo cấp độ (Grade: HG, MG, RG, PG) và tỷ lệ (Scale). 
- **Tích hợp Dữ liệu Thật:** Kho dữ liệu hàng trăm sản phẩm được bóc tách (crawl) từ Kaggle/Web và Import tự động qua PHP script.
- **Chức năng Cốt lõi:** Giỏ hàng (Cart) sử dụng Session, yêu thích (Wishlist/Favorite), thanh toán (Checkout) tính phí vận chuyển động.
- **Tin tức & Blog:** Chuyên trang cập nhật tin tức, kỹ thuật lắp ráp và sự kiện Gunpla.
- **Bảo mật:** Hệ thống đăng nhập/đăng ký với mật khẩu được mã hóa an toàn.

### ⚙️ Giao diện Quản trị (Admin Panel)
- **Dashboard:** Thống kê tổng quan đơn hàng, doanh thu và lượng truy cập.
- **Quản lý Kho (Inventory):** Theo dõi số lượng tồn kho của từng mẫu Gunpla.
- **Quản lý Đơn hàng (Orders):** Cập nhật trạng thái xử lý, duyệt đơn và xem chi tiết giao dịch.
- **Quản lý Dữ liệu:** Thêm/Sửa/Xóa sản phẩm, danh mục (Categories) và người dùng (Users).

---

## 🛠 Công nghệ sử dụng (Tech Stack)

- **Frontend:** HTML5, CSS3 (Vanilla + CSS Variables), JavaScript thuần (ES6).
- **Backend:** PHP (Kiến trúc MVC tự xây dựng - No Framework).
- **Cơ sở dữ liệu:** MySQL / MariaDB.
- **Thư viện bên thứ 3:** PHPMailer (gửi email), Composer.
- **Công cụ phát triển:** XAMPP, VS Code, Git, Python (dùng để crawl và chuẩn hóa dataset CSV).

---

## 📂 Cấu trúc thư mục (Directory Structure)

Dự án được tổ chức chặt chẽ theo chuẩn mô hình MVC:

```text
gunpla-shop/
│
├── app/                  # Chứa toàn bộ Logic xử lý
│   ├── Controllers/      # Các Controller (Product, Cart, Order, Admin, News...)
│   ├── Models/           # Tương tác Database (PDO)
│   └── Views/            # Giao diện hiển thị (Home, Products, Admin, Layouts...)
│
├── config/               # Cấu hình hệ thống (app.php, database.php)
├── database/             # Chứa tệp gunpla_shop.sql để khởi tạo CSDL
├── public/               # Thư mục Public (Nơi trỏ Document Root)
│   ├── css/              # Tệp định dạng (shop.css, admin.css)
│   ├── js/               # Script frontend (shop.js, admin.js)
│   └── index.php         # Entry point (Bộ định tuyến - Router chính)
│
├── vendor/               # Chứa các gói Composer (PHPMailer...)
├── .env                  # Tệp biến môi trường
└── README.md             # Tài liệu dự án

```

---

## 🚀 Hướng dẫn cài đặt (Installation Guide)

Để chạy dự án này trên môi trường local, hãy làm theo các bước sau:

**Bước 1: Chuẩn bị môi trường**
Cài đặt [XAMPP](https://www.apachefriends.org/) (Hỗ trợ PHP 8.x trở lên).

**Bước 2: Clone dự án**
Clone hoặc copy toàn bộ thư mục dự án vào thư mục `htdocs` của XAMPP:

```bash
cd C:\xampp\htdocs
git clone <đường-dẫn-repo-của-bạn> gunpla-shop

```

**Bước 3: Thiết lập Cơ sở dữ liệu (Database)**

1. Mở XAMPP Control Panel, khởi động **Apache** và **MySQL**.
2. Truy cập `http://localhost/phpmyadmin`.
3. Tạo một database mới với tên: `gunpla_shop` (Collation: `utf8mb4_unicode_ci`).
4. Chọn tab **Import**, tải lên tệp `database/gunpla_shop.sql` và chạy.

**Bước 4: Cấu hình hệ thống**

1. Mở tệp `config/database.php` (hoặc `.env` nếu có) và cấu hình kết nối DB:
```php
'host' => 'localhost',
'dbname' => 'gunpla_shop',
'username' => 'root',
'password' => '' // Bỏ trống nếu dùng XAMPP mặc định

```



**Bước 5: Chạy dự án**
Mở trình duyệt web và truy cập vào đường dẫn:

```text
Trang khách hàng: http://localhost/gunpla-shop/public/
Trang quản trị:   http://localhost/gunpla-shop/public/admin/login

```

---

## 📝 Chú thích tự động Import Dữ liệu (Seed Data)

Dự án có đi kèm script tự động đọc file CSV (chứa hàng ngàn sản phẩm) để đổ vào CSDL.
Nếu bạn muốn nạp lại dữ liệu, hãy đặt file CSV vào thư mục `gunpla_database/` và chạy script `import_database.php` thông qua trình duyệt.

---

Dự án này được phát triển định hướng theo quy trình phần mềm thực tế, từ thu thập dữ liệu (Python), thiết kế kiến trúc, UX/UI đến lập trình Backend/Frontend hoàn chỉnh.

---

*Cảm ơn đã ghé thăm dự án của tôi! Nếu thấy hữu ích, hãy cho dự án 1 ⭐ nhé.*

```