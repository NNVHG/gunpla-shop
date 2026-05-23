# 🤖 GUNPLA SHOP — HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ MÔ HÌNH GUNDAM CAO CẤP
> **Đồ Án Phát Triển Phần Mềm Mã Nguồn Mở**  
> *Viện Chuyển Đổi Số — Trường Đại học Thủ Dầu Một (TDMU)*

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%208.0-777bb4.svg?style=flat-square&logo=php)](https://www.php.net/)
[![Database](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-blue?style=flat-square&logo=mysql)](https://www.mysql.com/)
[![AI Integration](https://img.shields.io/badge/AI-Google%20Gemini%20API-orange?style=flat-square&logo=google-gemini)](https://deepmind.google/technologies/gemini/)
[![Payment Gate](https://img.shields.io/badge/Payment-VNPAY%20Sandbox-red?style=flat-square)](https://sandbox.vnpayment.vn/)
[![Aesthetic Style](https://img.shields.io/badge/Design-Sci--fi%20%2F%20Gaming-darkgreen?style=flat-square)](#)

---

## 👥 Nhóm Thực Hiện (Sinh Viên)
*   **Nguyễn Ngô Vũ Hoàng Gia** (MSSV: 2224802010628)
*   **Nguyễn Huỳnh Dương Dũ** (MSSV: 2224802010783)
*   **Giảng viên hướng dẫn:** ThS. Nguyễn Danh Minh Trí

---

## 🌟 GIỚI THIỆU CHUNG (PROJECT PRESENTATION)

**Gunpla Shop** là một nền tảng thương mại điện tử chuyên nghiệp được thiết kế và tối ưu hóa riêng cho cộng đồng người chơi mô hình lắp ráp Gundam (Gunpla) và phụ kiện lắp ráp đi kèm. Dự án được xây dựng từ con số 0 trên nền tảng **PHP thuần (Vanilla PHP) theo kiến trúc MVC tự viết**, kết hợp với cơ sở dữ liệu MySQL và các tính năng tương tác bất đồng bộ (AJAX/Fetch API).

Không sử dụng các framework PHP cồng kềnh, Gunpla Shop tập trung tối đa vào tốc độ tải trang cực nhanh, giao diện tối giản hiện đại (Sci-fi/Gaming theme) hỗ trợ chế độ chuyển đổi Sáng/Tối (Dark/Light Mode) đồng bộ và tính bảo mật vượt trội.

---

## ⚡ CÁC TÍNH NĂNG ĐỘT PHÁ (PREMIUM FEATURES)

Hệ thống được nâng cấp toàn diện với những tính năng nâng cao trải nghiệm mua sắm và quản lý bán hàng:

### 1. 🔍 So Sánh Sản Phẩm Đa Chiều (Product Compare)
*   Cho phép người dùng lựa chọn và đối chiếu đồng thời lên đến **3 sản phẩm** mô hình cạnh nhau.
*   Bảng so sánh thông minh hiển thị toàn bộ thông số kỹ thuật chi tiết: Tỷ lệ (Scale), Cấp độ (Grade), Dòng phim (Series), Trọng lượng (Weight), Số lượng mảnh ghép (Parts Count), Độ khó lắp ráp (Difficulty), Giá tiền và đánh giá trung bình.

### 2. 🛠 Báo Cáo Sản Phẩm Lỗi & Timeline Tiến Trình (Defect Reports)
*   Giải quyết triệt để nỗi lo lắng của người chơi Gunpla về lỗi gãy part hoặc thiếu part khi mua hàng.
*   Khách hàng gửi khiếu nại đính kèm hình ảnh/video bằng chứng trực tiếp từ lịch sử đơn hàng.
*   **Timeline đồ họa trực quan:** Hiển thị 4 giai đoạn xử lý minh bạch: `Đã tiếp nhận` ➔ `Đang kiểm tra` ➔ `Đã duyệt` ➔ `Đã gửi part thay thế`.
*   Tự động phát thông báo (Notifications) về tài khoản khách hàng khi Admin cập nhật tiến độ.

### 🤖 3. Trợ Lý AI Chatbot Gemini & FAQ Thông Minh
*   Khung chat bóng bẩy (Glassmorphism UI) hỗ trợ tư vấn tự động 24/7.
*   Tích hợp trực tiếp API của **Google Gemini API (`gemini-2.0-flash`)** với prompt hướng dẫn chuyên môn sâu về Gunpla, lấy thông tin 12 tin nhắn lịch sử và 45 sản phẩm thực tế của cửa hàng làm ngữ cảnh.
*   **Cơ chế dự phòng (Local FAQ Fallback):** Tự động chuyển sang đối sánh từ khóa thông minh để trả lời FAQ local nếu mất kết nối API hoặc Admin chưa cấu hình Key.
*   Trang quản lý cấu hình Chatbot AI trong Admin Panel cho phép bật/tắt AI Mode và cập nhật API Key bất kỳ lúc nào.

### 🎙 4. Tìm Kiếm Bằng Giọng Nói (Voice Search)
*   Tích hợp công nghệ **Web Speech API** vào ô tìm kiếm chính.
*   Kích hoạt micro bằng một nút bấm kèm hoạt ảnh sóng âm (pulsing wave visualizer).
*   Nhận diện giọng nói tiếng Việt/tiếng Anh chuẩn xác, tự động điền từ khóa và thực thi truy vấn tức thì.

### 📊 5. Biểu Đồ Thống Kê Doanh Thu Động (Real-time Admin Chart)
*   Biểu đồ đường (Line Chart) mượt mà với hiệu ứng Gold Gradient bóng bẩy sử dụng **Chart.js v4**.
*   Lọc dữ liệu theo thời gian thực tùy chỉnh bằng hai ô chọn ngày: `Từ ngày` - `Đến ngày`.
*   **Cơ chế chia mốc tự động (Granularity):**
    *   *Trong ngày:* Chia theo 24 giờ (`00h` - `23h`).
    *   *Dưới 90 ngày:* Chia theo từng ngày (`ngày/tháng`).
    *   *Trên 90 ngày:* Tự động gom nhóm theo từng tháng (`tháng/năm`) tránh rối mắt.
*   Tooltip tích hợp dữ liệu chéo: Xem đồng thời Doanh thu (VNĐ) và Số đơn hàng trên từng mốc thời gian.

### 🔔 6. Thông Báo Hàng Về Tự Động (Back-in-stock Alert)
*   Khi mô hình hết hàng (`stock = 0`), hiển thị nút **Báo khi có hàng**.
*   Khách hàng đăng ký theo dõi sản phẩm bằng 1-click (AJAX).
*   Khi Admin cập nhật tăng số lượng tồn kho (`stock > 0`), hệ thống tự động quét dữ liệu và đẩy thông báo hàng về trực tiếp vào tài khoản của những người đăng ký.

---

## 🛠 CÔNG NGHỆ & THƯ VIỆN SỬ DỤNG (TECH STACK)

*   **Backend:** PHP 8.x thuần, kiến trúc MVC tự viết, kết nối CSDL qua thư viện an toàn **PDO** chống SQL Injection.
*   **Frontend:** HTML5 (Semantic HTML), CSS3 (Vanilla + CSS Variables tạo Dark/Light Mode đồng bộ), Javascript (ES6+) thuần không dùng framework.
*   **Thư viện đồ họa:** Chart.js v4 (UMD).
*   **Thư viện hỗ trợ:**
    *   `vlucas/phpdotenv` (Quản lý bảo mật môi trường).
    *   `PHPMailer` (Tự động gửi email hóa đơn khi đặt hàng).
    *   `Composer` (Quản lý autoload & thư viện).
*   **Cổng thanh toán:** **VNPAY Sandbox** (Thanh toán trực tuyến bảo mật).

---

## 📂 HƯỚNG DẪN CÀI ĐẶT NHANH (QUICK START)

1.  **Sao chép mã nguồn:** Tải mã nguồn về mục `htdocs` của XAMPP:
    ```bash
    cd C:\xampp\htdocs
    # Hoặc sao chép thư mục dự án của bạn vào C:\xampp\htdocs\gunpla-shop
    ```
2.  **Cài đặt các gói thư viện phụ thuộc:** Chạy lệnh tại thư mục dự án:
    ```bash
    composer install
    ```
3.  **Import Database:** 
    *   Tạo cơ sở dữ liệu mới có tên `gunpla_shop` trên `phpMyAdmin` (với Collation: `utf8mb4_unicode_ci`).
    *   Vào tab **Import**, tải lên và thực thi tệp tin `database/gunpla_shop.sql`.
4.  **Cấu hình môi trường:** Tạo file `.env` tại thư mục gốc và điền cấu hình:
    ```ini
    DB_HOST=127.0.0.1
    DB_NAME=gunpla_shop
    DB_USER=root
    DB_PASS=
    GEMINI_API_KEY=YOUR_GEMINI_API_KEY_HERE
    ```
5.  **Khởi động ứng dụng:**
    *   *Trang chủ khách hàng:* `http://localhost/gunpla-shop/public/`
    *   *Trang admin:* `http://localhost/gunpla-shop/public/admin/login`

### 🔑 Tài Khoản Thử Nghiệm (Demo Credentials)
*   **Quản trị viên (Admin):**
    *   Email: `admin@gunplashop.com`
    *   Mật khẩu: `admin123`
*   **Khách hàng (Customer):**
    *   Email: `user@example.com`
    *   Mật khẩu: `password123`
    *   *(Hoặc đăng ký tài khoản mới trực tiếp từ giao diện)*

---

## 🧭 HƯỚNG DẪN TRẢI NGHIỆM CHI TIẾT CÁC TÍNH NĂNG ĐỘT PHÁ

### 1. Tính năng So sánh sản phẩm:
*   Vào trang danh sách sản phẩm.
*   Nhấp chọn biểu tượng đối chiếu trên tối đa 3 sản phẩm để đưa vào thanh so sánh nổi ở chân trang.
*   Nhấn **So Sánh Ngay** để đối chiếu các thông số kỹ thuật (Grade, Scale, Parts, price...).

### 2. Tìm kiếm giọng nói:
*   Nhấp vào nút **Microphone** trên thanh tìm kiếm ở Header.
*   Cấp quyền Micro cho trình duyệt và nói từ khóa (ví dụ: `Gundam RG`).
*   Hệ thống tự nhận diện giọng nói và tự động submit để trả về danh sách kết quả.

### 3. AI Chatbot:
*   Nhấp vào bong bóng chat ở góc dưới bên phải màn hình để kích hoạt khung chat Glassmorphism.
*   Trò chuyện với AI Chatbot để nhận tư vấn mua hàng (AI sẽ tự động chèn liên kết sản phẩm của shop).
*   Đăng nhập tài khoản Admin, vào mục **Settings** để bật/tắt **AI Mode** hoặc thay đổi API Key. Khi tắt AI Mode, chatbot sẽ tự động chuyển sang chế độ FAQ offline để phản hồi người dùng.

### 4. Đăng ký nhận hàng về (Back-in-stock):
*   Truy cập chi tiết sản phẩm đã hết hàng (`Số lượng kho = 0`) khi đã đăng nhập tài khoản Khách hàng.
*   Nhấn nút **Báo khi có hàng**.
*   Đăng nhập tài khoản Admin, vào trang Quản lý Kho hàng hoặc cập nhật số lượng tồn kho sản phẩm này lên > 0.
*   Quay lại tài khoản khách hàng, kiểm tra thông báo chuông ở Header để xem tin báo hàng về tự động.

### 5. Báo lỗi sản phẩm & Timeline:
*   Đăng nhập tài khoản khách hàng, vào Lịch sử đơn hàng, chọn đơn hàng đã giao thành công (`Delivered`).
*   Bấm **Báo lỗi sản phẩm**, mô tả chi tiết lỗi và tải lên ảnh/video minh chứng.
*   Vào tài khoản Admin, mở trang quản lý khiếu nại báo lỗi, kiểm duyệt bằng chứng và cập nhật trạng thái (Checking, Approved, Shipped, Rejected).
*   Khách hàng có thể theo dõi tiến trình 4 bước trực quan qua Timeline tại trang chi tiết đơn hàng của mình.

### 6. Biểu đồ doanh thu động của Admin:
*   Đăng nhập tài khoản Admin, quan sát biểu đồ doanh thu trên Dashboard.
*   Sử dụng bộ lọc ngày để thay đổi khoảng thời gian lọc và kiểm tra cơ chế tự động chia mốc thời gian (giờ/ngày/tháng). Rê chuột lên các điểm nút để hiển thị đồng thời doanh thu & số đơn hàng.

---

## 📂 CẤU TRÚC THƯ MỤC HỆ THỐNG (DIRECTORY TREE)

```text
gunpla-shop/
├── app/                              # Logic cốt lõi của ứng dụng (kiến trúc MVC)
│   ├── Controllers/                  # Các bộ điều khiển điều hướng nghiệp vụ
│   │   ├── AdminController.php       # Dashboard, biểu đồ thống kê, quản lý đơn lỗi
│   │   ├── CartController.php        # Nghiệp vụ giỏ hàng
│   │   ├── ChatbotController.php     # Kết nối Gemini API và xử lý FAQ
│   │   ├── ProductController.php     # Phân trang, tìm kiếm, so sánh sản phẩm
│   │   ├── OrderController.php       # Đặt đơn, VNPAY, gửi mail hóa đơn
│   │   └── UserController.php        # Đăng ký, đăng nhập, thông báo tài khoản
│   ├── Models/                       # Các thực thể tương tác CSDL bằng PDO
│   │   ├── Product.php               # Quản lý hàng hóa, kích hoạt thông báo hàng về
│   │   ├── Order.php                 # Quản lý hóa đơn và doanh thu
│   │   ├── User.php                  # Quản lý người dùng
│   │   ├── DefectReport.php          # Lưu trữ báo lỗi và timeline
│   │   ├── StockSubscription.php     # Lưu trữ lượt đăng ký báo hàng về
│   │   ├── Notification.php          # Lưu trữ thông báo tài khoản
│   │   └── Setting.php               # Lưu trữ cấu hình hệ thống động (AI Mode/Key)
│   └── Views/                        # Giao diện HTML/PHP kết xuất hiển thị
│       ├── admin/                    # Các trang của ban quản trị
│       ├── products/                 # Danh sách, chi tiết, bảng so sánh sản phẩm
│       ├── layouts/                  # Giao diện khung (Header, Footer, Chatbot UI)
│       └── home/                     # Giao diện trang chủ khách hàng
├── config/                           # Các tệp tin cấu hình
│   ├── app.php                       # Cấu hình cài đặt chung
│   └── database.php                  # Thiết lập kết nối PDO database
├── database/                         # Chứa mã nguồn SQL cơ sở dữ liệu
│   └── gunpla_shop.sql               # Backup CSDL mẫu
├── public/                           # Thư mục gốc công khai tiếp nhận Request (Document Root)
│   ├── css/                          # CSS Variables định dạng Sáng/Tối đồng bộ
│   ├── js/                           # Thư mục chứa kịch bản JavaScript client-side
│   ├── uploads/                      # Lưu trữ hình ảnh tải lên (Products, Defects, News)
│   └── index.php                     # Bộ định tuyến trung tâm (Front Controller Router)
├── vendor/                           # Thư mục tự động tải thư viện qua Composer
├── .env                              # Quản lý biến môi trường bảo mật
├── composer.json                     # Định nghĩa các thư viện phụ thuộc PHP
└── README.md                         # Tài liệu giới thiệu hiển thị trên GitHub
```

---

## 📄 TÀI LIỆU HỆ THỐNG LIÊN QUAN (DOCUMENTATION LINKS)

Để tìm hiểu chi tiết sâu hơn về kiến trúc và cách thức vận hành hệ thống, vui lòng tham khảo:
*   [PROJECT_OVERVIEW.md](file:///e:/XAMPP/htdocs/gunpla-shop/PROJECT_OVERVIEW.md) — Phân tích chi tiết kiến trúc MVC, vòng đời request, cấu trúc chi tiết của 13 bảng CSDL, và các dòng nghiệp vụ thanh toán, báo lỗi.
*   [SPECIAL_FEATURES.md](file:///e:/XAMPP/htdocs/gunpla-shop/SPECIAL_FEATURES.md) — Hướng dẫn cài đặt, cơ chế hoạt động, và giải pháp kỹ thuật của các tính năng nâng cao (AI Chatbot, So sánh, Giọng nói, Timeline lỗi, Biểu đồ động).
