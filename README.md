# 🤖 GUNPLA SHOP — HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ MÔ HÌNH GUNDAM CAO CẤP
> **Đồ Án Phát Triển Phần Mềm Mã Nguồn Mở**  
> *Viện Chuyển Đổi Số — Trường Đại học Thủ Dầu Một (TDMU)*

---

## 👥 Nhóm Thực Hiện (Sinh Viên)
*   **Nguyễn Ngô Vũ Hoàng Gia** (MSSV: 2224802010628)
*   **Nguyễn Huỳnh Dương Dũ** (MSSV: 2224802010783)
*   **Giảng viên hướng dẫn:** ThS. Nguyễn Danh Minh Trí

---

## 🌟 GIỚI THIỆU CHUNG (PROJECT PRESENTATION)

**Gunpla Shop** là một nền tảng thương mại điện tử chuyên nghiệp được thiết kế và tối ưu hóa riêng cho cộng đồng người chơi mô hình lắp ráp Gundam (Gunpla). Dự án được xây dựng từ con số 0 trên nền tảng **PHP thuần (Vanilla PHP) theo kiến trúc MVC**, kết hợp với cơ sở dữ liệu MySQL và các tính năng tương tác bất đồng bộ (AJAX/Fetch API).

Không sử dụng các framework PHP cồng kềnh, Gunpla Shop tập trung tối đa vào tốc độ tải trang cực nhanh, giao diện tối giản hiện đại (Sci-fi/Gaming theme) và tính bảo mật vượt trội.

---

## ⚡ CÁC TÍNH NĂNG ĐỘT PHÁ (PREMIUM FEATURES)

Hệ thống được nâng cấp toàn diện với những tính năng nâng cao trải nghiệm mua sắm và quản lý bán hàng:

### 1. 🔍 So Sánh Sản Phẩm Đa Chiều (Product Compare)
*   Cho phép người dùng lựa chọn và đối chiếu đồng thời lên đến **3 sản phẩm** mô hình cạnh nhau.
*   Bảng so sánh thông minh hiển thị toàn bộ thông số kỹ thuật chi tiết: Tỷ lệ (Scale), Cấp độ (Grade), Dòng phim (Series), Trọng lượng (Weight), Số lượng mảnh ghép (Parts Count), Độ khó lắp ráp (Difficulty), Giá tiền và Mô tả.

### 2. 🛠 Báo Cáo Sản Phẩm Lỗi & Timeline Tiến Trình (Defect Reports)
*   Giải quyết triệt để nỗi lo lắng của người chơi Gunpla về lỗi gãy part hoặc thiếu part khi mua hàng.
*   Khách hàng gửi khiếu nại đính kèm hình ảnh/video bằng chứng trực tiếp từ lịch sử đơn hàng.
*   **Timeline đồ họa trực quan:** Hiển thị 4 giai đoạn xử lý minh bạch: `Đã tiếp nhận` ➔ `Đang kiểm tra` ➔ `Đã duyệt` ➔ `Đã gửi part thay thế`.
*   Tự động bắn thông báo (Notifications) về tài khoản khách hàng khi Admin cập nhật tiến độ.

### 🤖 3. Trợ Lý AI Chatbot Gemini & FAQ Thông Minh
*   Khung chat bóng bẩy (Glassmorphism UI) hỗ trợ tư vấn 24/7.
*   Tích hợp trực tiếp API của **Google Gemini API (`gemini-1.5-flash`)** với prompt hướng dẫn chuyên môn sâu về Gunpla.
*   **Cơ chế dự phòng (Local FAQ Fallback):** Tự động chuyển sang đối sánh từ khóa địa phương để trả lời nếu mất kết nối API hoặc Admin chưa cấu hình Key.
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
*   Khi Admin cập nhật số lượng tồn kho (`stock > 0`), hệ thống tự động quét dữ liệu và đẩy thông báo hàng về trực tiếp vào tài khoản của những người đăng ký.

---

## 🛠 CÔNG NGHỆ & THƯ VIỆN SỬ DỤNG (TECH STACK)

*   **Backend:** PHP 8.x thuần, kiến trúc MVC tự viết, kết nối CSDL qua thư viện an toàn **PDO**.
*   **Frontend:** HTML5 (Semantic HTML), CSS3 (Vanilla + CSS Variables tạo Dark/Light Mode đồng bộ), Javascript (ES6+) thuần không dùng framework.
*   **Thư viện đồ họa:** Chart.js v4 (UMD).
*   **Thư viện hỗ trợ:**
    *   `vlucas/phpdotenv` (Quản lý bảo mật môi trường).
    *   `PHPMailer` (Tự động gửi email hóa đơn khi đặt hàng).
    *   `Composer` (Quản lý autoload & thư viện).
*   **Cổng thanh toán:** **VNPAY Sandbox** (Thanh toán trực tuyến bảo mật).

---

## 📂 HƯỚNG DẪN CÀI ĐẶT NHANH (QUICK START)

1.  **Clone dự án:** Tải mã nguồn về mục `htdocs` của XAMPP:
    ```bash
    cd C:\xampp\htdocs
    git clone <đường-dẫn-repo> gunpla-shop
    ```
2.  **Cài đặt thư viện:** Chạy lệnh tại thư mục dự án:
    ```bash
    composer install
    ```
3.  **Import Database:** Tạo CSDL `gunpla_shop` trên `phpMyAdmin` (Mã hóa: `utf8mb4_unicode_ci`), sau đó import file `database/gunpla_shop.sql`.
4.  **Cấu hình môi trường:** Tạo file `.env` tại thư mục gốc và cấu hình:
    ```ini
    DB_HOST=127.0.0.1
    DB_NAME=gunpla_shop
    DB_USER=root
    DB_PASS=
    GEMINI_API_KEY=YOUR_API_KEY_HERE
    ```
5.  **Khởi động:**
    *   *Trang chủ khách hàng:* `http://localhost/gunpla-shop/public/`
    *   *Trang admin:* `http://localhost/gunpla-shop/public/admin/login` (Tài khoản: `admin@gunplashop.com` / Mật khẩu: `admin123`).

---

## 📄 TÀI LIỆU HỆ THỐNG LIÊN QUAN (DOCUMENTATION LINKS)

Để tìm hiểu chi tiết sâu hơn về hệ thống, bạn có thể tham khảo các tài liệu chuyên đề sau:
*   [PROJECT_OVERVIEW.md](file:///e:/XAMPP/htdocs/gunpla-shop/PROJECT_OVERVIEW.md) — Phân tích chi tiết kiến trúc MVC, vòng đời request, cấu trúc chi tiết của 13 bảng CSDL, và các dòng nghiệp vụ thanh toán, báo lỗi.
*   [SPECIAL_FEATURES.md](file:///e:/XAMPP/htdocs/gunpla-shop/SPECIAL_FEATURES.md) — Hướng dẫn cài đặt, cơ chế hoạt động, và giải pháp kỹ thuật của các tính năng nâng cao (AI Chatbot, So sánh, Giọng nói, Timeline lỗi, Biểu đồ động).
