Đây là sản phẩm phục vụ cho **Đồ án Phát triển phần mềm mã nguồn mở** tại **Trường Đại học Thủ Dầu Một - Viện Chuyển Đổi Số**.

### 👥 Nhóm phát triển (Sinh viên thực hiện)
* **Nguyễn Ngô Vũ Hoàng Gia** (MSSV: 2224802010628)
* **Nguyễn Huỳnh Dương Dũ** (MSSV: 2224802010783)
* **Giáo viên hướng dẫn:** Nguyễn Danh Minh Trí

---

## ✨ Tính năng nổi bật (Features)

### 👤 Giao diện Người dùng (Customer/Frontend)
- **UI/UX Hiện đại:** Thiết kế tinh gọn, hỗ trợ tính năng chuyển đổi **Dark/Light Mode** đồng bộ toàn hệ thống bằng CSS Variables. Ưu tiên trải nghiệm hiển thị sản phẩm trực quan.
- **Quản lý & Lọc Sản phẩm:** Hiển thị chi tiết mô hình theo cấp độ (Grade: HG, MG, RG, PG) và tỷ lệ (Scale). Hỗ trợ bộ lọc chéo đa chiều.
- **Tích hợp Dữ liệu Thật:** Kho dữ liệu hàng trăm sản phẩm được bóc tách (crawl) từ Web/Kaggle và Import tự động qua PHP script.
- **Chức năng Cốt lõi (E-commerce):** - Giỏ hàng (Cart) sử dụng Session.
  - Danh sách yêu thích (Wishlist/Favorite).
  - Thanh toán (Checkout) với tính năng nội suy tính phí vận chuyển động theo khu vực địa lý.
  - Hỗ trợ đa dạng phương thức thanh toán: **COD** và cổng thanh toán trực tuyến **VNPAY**.
- **Tin tức & Blog:** Chuyên trang cập nhật tin tức, kỹ thuật lắp ráp và sự kiện Gunpla.
- **Bảo mật:** Hệ thống đăng nhập/đăng ký với mật khẩu được mã hóa an toàn (BCRYPT), phòng chống SQL Injection qua PDO.
- **Email Tự động:** Gửi hóa đơn phản hồi tự động bằng PHPMailer ngay khi đặt hàng thành công.

### ⚙️ Giao diện Quản trị (Admin Panel)
- **Dashboard:** Thống kê tổng quan đơn hàng, doanh thu (ngày/tháng) và lượng truy cập. Trực quan hoá dữ liệu bằng Chart.js.
- **Quản lý Kho (Inventory):** Hệ thống báo động đỏ (Low Stock Alerts) cho sản phẩm sắp hết (tồn kho ≤ 5).
- **Quản lý Đơn hàng (Orders):** Giám sát tuyến tính vòng đời đơn hàng (Chờ xác nhận -> Đang giao -> Đã giao). Hỗ trợ xác nhận dòng tiền thanh toán (Mark Paid) cho COD.
- **Quản lý Dữ liệu Toàn diện:** Thêm/Sửa/Xóa (Cơ chế Soft Delete) sản phẩm, danh mục đa cấp, tin tức, tài khoản người dùng và kiểm duyệt đánh giá (Reviews).

---

## 🛠 Công nghệ sử dụng (Tech Stack)

- **Frontend:** HTML5, CSS3 (Vanilla + CSS Variables), JavaScript thuần (ES6), Chart.js (Biểu đồ).
- **Backend:** PHP thuần (Vanilla PHP) với kiến trúc MVC tự xây dựng, hoàn toàn không phụ thuộc Framework.
- **Cơ sở dữ liệu:** MySQL / MariaDB (Sử dụng PDO).
- **Thư viện bên thứ 3:** PHPMailer (gửi email), Composer.
- **Môi trường & Công cụ:** XAMPP, Git, VS Code.

---

## 📂 Cấu trúc thư mục (Directory Structure)

Dự án được tổ chức chặt chẽ theo chuẩn mô hình MVC:

```text
gunpla-shop/
│
├── app/                  # Chứa toàn bộ Logic xử lý (Lõi MVC)
│   ├── Controllers/      # Các Controller (Product, Cart, Order, Admin, News...)
│   ├── Models/           # Tương tác Cơ sở dữ liệu (PDO)
│   └── Views/            # Giao diện hiển thị (Home, Products, Admin, Layouts...)
│
├── config/               # Cấu hình hệ thống (app.php, database.php)
├── database/             # Chứa tệp SQL (gunpla_shop.sql) khởi tạo CSDL
├── public/               # Thư mục Public (Trỏ Document Root)
│   ├── css/              # Tệp định dạng stylesheet
│   ├── js/               # Script xử lý frontend
│   └── index.php         # Entry point (Bộ định tuyến - Router chính)
│
├── vendor/               # Chứa các gói thư viện Composer
├── .env                  # Tệp biến môi trường
└── README.md             # Tài liệu dự án

```

---

## 🚀 Hướng dẫn cài đặt (Installation Guide)

Để khởi chạy dự án trên môi trường local, hãy làm theo các bước sau:

**Bước 1: Chuẩn bị môi trường**
Cài đặt phần mềm **XAMPP** (Hỗ trợ PHP 8.x trở lên).

**Bước 2: Clone dự án**
Clone toàn bộ mã nguồn vào thư mục `htdocs` của XAMPP:

```bash
cd C:\\xampp\\htdocs
git clone <đường-dẫn-repo-của-bạn> gunpla-shop

```

**Bước 3: Khởi tạo Cơ sở dữ liệu (Database)**

1. Mở XAMPP Control Panel, khởi động **Apache** và **MySQL**.
2. Truy cập `http://localhost/phpmyadmin`.
3. Tạo một Database mới mang tên: `gunpla_shop` (Mã hóa: `utf8mb4_unicode_ci`).
4. Chọn tab **Import**, tải lên tệp `database/gunpla_shop.sql` có sẵn trong thư mục dự án và thực thi.

**Bước 4: Cấu hình hệ thống kết nối DB**
Mở tệp `config/database.php` (hoặc `.env` nếu có) và cấu hình chuẩn xác các thông số:

```php
'host' => 'localhost',
'dbname' => 'gunpla_shop',
'username' => 'root',
'password' => '' // Mặc định của XAMPP là bỏ trống

```

*(Nếu bạn muốn thử tính năng gửi mail, hãy cập nhật cấu hình SMTP trong tệp cài đặt email).*

**Bước 5: Chạy dự án**
Mở trình duyệt web và truy cập vào đường dẫn sau:

* **Trang khách hàng:** `http://localhost/gunpla-shop/public/`
* **Trang quản trị (Admin):** `http://localhost/gunpla-shop/public/admin/login`

---
