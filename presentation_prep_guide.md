# CẨM NANG CHUẨN BỊ THUYẾT TRÌNH ĐỒ ÁN: GUNPLA SHOP
**Báo cáo Đồ án Phát triển phần mềm mã nguồn mở — Đại học Thủ Dầu Một**

Tài liệu này được biên soạn để giúp bạn nắm bắt toàn bộ dự án chỉ trong 10-15 phút đọc, tự tin thực hiện bài thuyết trình **3 phút** và trả lời xuất sắc các câu hỏi phản biện (Q&A) từ giảng viên.

---

## ⏱ 1. KỊCH BẢN THUYẾT TRÌNH 3 PHÚT (3-MINUTE PRESENTATION SCRIPT)

Hãy nói với phong thái tự tin, rõ ràng, tốc độ vừa phải. Có thể mở slide hoặc demo trực tiếp giao diện tương ứng theo từng phân đoạn.

| Thời gian | Nội dung trình bày | Hành động trên màn hình (Demo) |
| :--- | :--- | :--- |
| **0:00 - 0:30**<br>*(30 giây)* | **Mở đầu & Kiến trúc:**<br>"Em chào Thầy và các bạn. Em tên là Hoàng Gia, đại diện nhóm thuyết trình đồ án **Gunpla Shop** - Hệ thống thương mại điện tử chuyên biệt mô hình Gundam.<br>Điểm đặc biệt đầu tiên của dự án là nhóm em **tự xây dựng một MVC Framework bằng PHP thuần**, không sử dụng framework ăn sẵn như Laravel để tối ưu hóa hiệu năng, kiểm soát mã nguồn và hiểu sâu về vòng đời của một Request thông qua file Front Controller `index.php` kết hợp định tuyến Router tự viết." | Hiển thị Slide trang bìa hoặc Trang chủ Website.<br>Nói rõ vai trò tự viết MVC. |
| **0:30 - 1:20**<br>*(50 giây)* | **Các luồng nghiệp vụ cốt lõi:**<br>"Gunpla Shop sở hữu đầy đủ nghiệp vụ của một trang thương mại điện tử chuyên nghiệp:<br>1. Duyệt sản phẩm theo Cấp độ (Grade như HG, RG, MG), tỷ lệ lắp ráp (Scale).<br>2. Giỏ hàng động sử dụng Session PHP.<br>3. Trang thanh toán thông minh: Nhờ tích hợp JavaScript gọi AJAX, hệ thống tự động **nội suy phí vận chuyển** theo trọng lượng và Tỉnh/Thành phố của khách hàng.<br>4. Tích hợp thanh toán online qua **cổng VNPAY** và gửi email hóa đơn tự động qua **PHPMailer**." | Demo nhanh: Vào trang danh sách sản phẩm -> Thêm vào giỏ hàng -> Vào trang Checkout nhập Tỉnh thành để thấy phí ship thay đổi. |
| **1:20 - 2:30**<br>*(70 giây)* | **Các tính năng đột phá & AI:**<br>"Bên cạnh các tính năng cơ bản, nhóm em tích hợp 4 tính năng nâng cao:<br>1. **AI Chatbot Gemini:** Tư vấn mua hàng 24/7. Nếu lỗi kết nối API, chatbot tự động kích hoạt chế độ dự phòng local (Rule-based) đảm bảo trải nghiệm liền mạch.<br>2. **Tìm kiếm bằng giọng nói:** Tích hợp Web Speech API của trình duyệt, nhận diện tiếng Việt cực tốt.<br>3. **So sánh sản phẩm:** Đối chiếu trực quan tối đa 3 mô hình về số lượng mảnh ghép, độ khó, giá cả.<br>4. **Báo lỗi sản phẩm & Timeline:** Khi sản phẩm lỗi runner/gãy part, khách hàng gửi báo cáo kèm ảnh. Tiến trình xử lý của Admin được hiển thị qua một **Timeline đồ họa trực quan** gồm 4 mốc thời gian thực." | Click biểu tượng Micro nói thử từ khóa tìm kiếm.<br>Click Chatbot AI hỏi 1 câu.<br>Vào Chi tiết đơn hàng hiển thị Timeline báo lỗi sản phẩm. |
| **2:30 - 3:00**<br>*(30 giây)* | **Giao diện Admin & Tổng kết:**<br>"Về phía quản trị, Admin Dashboard tích hợp biểu đồ doanh thu **Chart.js** dạng đường uốn cong (Line Chart) cực kỳ cao cấp, tự động gộp nhóm theo Giờ, Ngày hoặc Tháng tùy khoảng thời gian lọc (Granularity) để tránh tràn biểu đồ. Đồng thời, hệ thống tự động gửi thông báo 'Hàng về' (Back-in-stock) tới khách hàng đã đăng ký khi Admin cập nhật kho.<br>Đồ án này đã giúp nhóm em thực hành toàn diện từ thiết kế CSDL (13 bảng), lập trình hướng đối tượng đến tối ưu bảo mật. Em xin cảm ơn Thầy và xin nhận các câu hỏi!" | Mở giao diện Admin Dashboard chỉ vào biểu đồ doanh thu vàng Gold sang trọng.<br>Kết thúc bài thuyết trình. |

---

## 🏛 2. BẢN ĐỒ CÔNG NGHỆ & MÃ NGUỒN (TECHNICAL STACK & CORE LOGIC)

Nếu Thầy hỏi kỹ về kỹ thuật hoặc yêu cầu chỉ file code, hãy nhớ các từ khóa và vị trí file dưới đây:

### A. Kiến trúc MVC tự viết (Custom MVC)
*   **Front Controller:** File [public/index.php](file:///e:/XAMPP/htdocs/gunpla-shop/public/index.php) là nơi duy nhất đón nhận request. Nó nạp `.env`, Composer Autoload, khởi chạy session và gọi **Router**.
*   **Router:** Nằm trong thư mục `app/Core/` hoặc logic phân tích URI trực tiếp tại `index.php` để tìm ra đúng `Controller`, `Action` (hàm trong controller) và `Parameter` (ID truyền vào).
*   **Cơ chế Render View:** Controller sử dụng **Output Buffering** (`ob_start()` và `ob_get_clean()`) để nạp file view con, gán nội dung vào biến `$content`, rồi include file giao diện chung [layouts/main.php](file:///e:/XAMPP/htdocs/gunpla-shop/app/views/layouts/main.php) để tạo trang hoàn chỉnh.

### B. Bảo mật (Security)
*   **SQL Injection:** Sử dụng **PDO Parameter Binding** (Prepared Statement). Không bao giờ nối chuỗi SQL trực tiếp với biến đầu vào.
*   **XSS (Cross-Site Scripting):** Dùng hàm `htmlspecialchars($data, ENT_QUOTES, 'UTF-8')` khi in biến ra HTML.
*   **Mã hóa mật khẩu:** Dùng hàm `password_hash($password, PASSWORD_BCRYPT)` và xác thực bằng `password_verify()`.

---

## 💬 3. BỘ CÂU HỎI ĐÁP (Q&A) THƯỜNG GẶP VỚI GIẢNG VIÊN

Dưới đây là 10 câu hỏi giảng viên rất dễ hỏi và cách trả lời "ăn điểm" tuyệt đối:

### Câu 1: Tại sao nhóm không dùng Laravel/CodeIgniter cho nhanh mà lại tự viết MVC?
*   **Cách trả lời:**
    > "Dạ thưa Thầy, việc tự viết MVC giúp chúng em hiểu rõ bản chất cốt lõi của lập trình hướng đối tượng PHP và cơ chế hoạt động của Web Server (như Front Controller, Routing, Request Lifecycle) mà các Framework lớn thường che giấu đi.
    > Ngoài ra, tự viết giúp ứng dụng có dung lượng cực nhẹ (chỉ vài megabyte), tốc độ tải trang cực nhanh và không bị phụ thuộc vào các thư viện bên thứ ba không cần thiết, rất phù hợp với môn học Mã nguồn mở này ạ."

### Câu 2: Trình bày luồng đi (Lifecycle) khi người dùng truy cập link `/products/detail/5`?
*   **Cách trả lời:**
    > "Dạ, quy trình gồm 5 bước chính:
    > 1. Request được Apache điều hướng về file Front Controller duy nhất là `public/index.php`.
    > 2. `index.php` nạp Composer Autoload, các cấu hình CSDL, và gọi Router.
    > 3. Router phân tích URI `/products/detail/5` để xác định: Controller là `ProductController`, Action là `detail` và ID sản phẩm là `5`.
    > 4. `ProductController` gọi Model `Product` để thực hiện câu lệnh SQL bind tham số an toàn lấy thông tin sản phẩm ID = 5.
    > 5. Dữ liệu sản phẩm nhận về được Controller đẩy vào View `products/detail.php`, kết xuất ra HTML và trả về cho trình duyệt ạ."

### Câu 3: Cơ chế hoạt động của AI Chatbot là gì? Em xử lý thế nào khi mất kết nối mạng hoặc API Key hết tiền?
*   **Cách trả lời:**
    > "Dạ, AI Chatbot tích hợp API của Google Gemini (model `gemini-2.0-flash`). Khi khách hàng gửi tin nhắn, hệ thống gửi request HTTPS lên Google Cloud cùng với chỉ dẫn hệ thống (System Instruction) để AI đóng vai trò tư vấn viên Gunpla.
    > Để đảm bảo hệ thống không bị lỗi (chết trang) khi mất kết nối mạng hoặc API Key hết hạn/hết tiền (như lỗi `429 Resource Exhausted`), em đã lập trình cơ chế **Fallback (dự phòng cục bộ)**: Hệ thống tự động chuyển sang chế độ so khớp từ khóa (Pattern Matching) trong bộ FAQ có sẵn ở database local để trả lời tự động cho khách hàng."

### Câu 4: Tính năng tìm kiếm bằng giọng nói hoạt động dựa trên API hay thư viện nào? Có tốn phí không?
*   **Cách trả lời:**
    > "Dạ, tính năng này hoàn toàn miễn phí và không cần cài đặt thư viện bên thứ ba. Nhóm em sử dụng **Web Speech API** (`webkitSpeechRecognition`) - một API tiêu chuẩn được tích hợp sẵn trong nhân các trình duyệt hiện đại (Chrome, Edge, Safari).
    > JavaScript trên trình duyệt sẽ xin quyền sử dụng Micro, thu âm giọng nói, chuyển từ giọng nói thành văn bản tiếng Việt thời gian thực và tự động điền vào ô tìm kiếm rồi submit form ạ."

### Câu 5: Phí vận chuyển (Shipping Fee) được tính động như thế nào khi thanh toán?
*   **Cách trả lời:**
    > "Dạ, trong trang thanh toán, khi người dùng thay đổi Tỉnh/Thành phố nhận hàng, một sự kiện AJAX được kích hoạt gửi dữ liệu về `OrderController`. 
    > Hệ thống sẽ nội suy phí ship: các tỉnh miền Nam (gần shop) có mức phí cố định thấp (ví dụ: 25k). Với các tỉnh miền Trung/Bắc, hệ thống sẽ tính phí ship lũy tiến dựa trên tổng trọng lượng của các mô hình trong giỏ hàng (cột `weight_gram` trong bảng `products`) để đưa ra phí ship chính xác nhất, tránh thất thoát chi phí vận chuyển của shop ạ."

### Câu 6: Dự án phòng chống lỗi SQL Injection và lỗi XSS (Cross-Site Scripting) như thế nào?
*   **Cách trả lời:**
    > "Dạ, đối với **SQL Injection**, nhóm em sử dụng cơ chế Prepared Statements của PDO. Các biến đầu vào từ người dùng không được nối trực tiếp vào chuỗi SQL mà được truyền qua các tham số liên kết (Parameter Binding) như `:id`, `:email`.
    > Đối với **XSS**, mọi dữ liệu hiển thị ra màn hình từ CSDL (như đánh giá của khách, mô tả lỗi) đều được xử lý qua hàm `htmlspecialchars()` để chuyển đổi các ký tự đặc biệt như `<`, `>` thành các thực thể HTML an toàn, ngăn chặn việc thực thi mã độc Javascript từ phía hacker ạ."

### Câu 7: CSDL của tính năng "Báo cáo sản phẩm lỗi" (Defect Reports) hoạt động ra sao? Làm thế nào để đồng bộ trạng thái sang Timeline?
*   **Cách trả lời:**
    > "Dạ, tính năng này dựa trên bảng `defect_reports` liên kết Foreign Key với `users` (người báo), `products` (sản phẩm lỗi) và `orders` (đơn hàng đã mua).
    > Bảng này có cột `status` chứa các trạng thái xử lý (`pending`, `checking`, `approved`, `shipped`, `rejected`).
    > Khi Admin cập nhật trạng thái lỗi này trong trang quản trị, hệ thống sẽ lưu trạng thái mới và tự động tạo thông báo (`Notification`) gửi cho người dùng. Khi khách hàng xem chi tiết đơn hàng, giao diện sẽ đọc cột `status` từ CSDL và hiển thị thanh tiến trình Timeline tương ứng dưới dạng đồ họa trực quan ạ."

### Câu 8: Tính năng "Báo khi có hàng" (Back-in-stock Notification) hoạt động tự động như thế nào?
*   **Cách trả lời:**
    > "Dạ, quy trình gồm 2 bước:
    > 1. Khi sản phẩm có tồn kho `stock = 0`, nút mua hàng đổi thành 'Báo khi có hàng'. Khách hàng click vào sẽ lưu một dòng đăng ký vào bảng `back_in_stock_subscriptions` (gồm `user_id` và `product_id`).
    > 2. Khi Admin cập nhật tăng số lượng kho (`stock > 0`) trong trang quản lý sản phẩm, hệ thống kích hoạt hàm `triggerBackInStockNotifications()` quét bảng đăng ký nhận tin. Hệ thống tự động tạo các thông báo (`Notification`) gửi trực tiếp vào tài khoản các khách hàng này kèm link xem sản phẩm, sau đó xóa các bản ghi đăng ký cũ đi để giải phóng dữ liệu ạ."

### Câu 9: Tích hợp cổng thanh toán VNPAY như thế nào?
*   **Cách trả lời:**
    > "Dạ, nhóm em tích hợp VNPAY qua phương thức Redirect URL. Khi khách hàng chọn thanh toán qua VNPAY, backend sẽ tạo các tham số theo yêu cầu của VNPAY (mã đơn, số tiền, thông tin thanh toán), mã hóa bảo mật tạo chữ ký Checksum bằng thuật toán SHA256 rồi chuyển hướng người dùng sang cổng thanh toán ngân hàng của VNPAY.
    > Sau khi thanh toán xong, VNPAY chuyển hướng về URL phản hồi của shop. Hệ thống sẽ so khớp chữ ký bảo mật để xác thực giao dịch thành công, cập nhật trạng thái đơn hàng sang 'confirmed', trạng thái thanh toán là 'paid' và thực hiện trừ kho tự động ạ."

### Câu 10: Biểu đồ doanh thu Admin Dashboard hoạt động ra sao và "Granularity" (độ chia mốc dữ liệu) là gì?
*   **Cách trả lời:**
    > "Dạ, biểu đồ sử dụng thư viện **Chart.js** kết hợp gọi AJAX để lấy dữ liệu thống kê từ CSDL không gây tải chậm trang.
    > Điểm thông minh của biểu đồ là tính năng **Granularity**: hệ thống tự động nhận diện khoảng thời gian lọc:
    > - Nếu lọc trong 1 ngày: Gom nhóm doanh thu theo từng giờ (24 giờ) để biết giờ nào bán chạy nhất.
    > - Lọc dưới 90 ngày: Gom nhóm theo từng ngày.
    > - Lọc trên 90 ngày (ví dụ: cả năm): Tự động gom nhóm theo từng tháng để biểu đồ đường uốn cong hiển thị thông thoáng, không bị chen chúc dữ liệu ạ."

---

## 🛠 4. LỜI KHUYÊN ĐỂ ĐẠT ĐIỂM TỐI ĐA (TIPS FOR MAXIMUM GRADE)

1.  **Về mặt giao diện:** Nhấn mạnh rằng trang web được thiết kế theo phong cách hiện đại với hiệu ứng làm mờ kính (Glassmorphism), có chế độ Sáng / Tối (Dark & Light Mode) mượt mà bằng CSS Variables lưu trữ trạng thái tại LocalStorage.
2.  **Về mặt dữ liệu:** Chuẩn bị sẵn một tài khoản Admin (`admin@gunplashop.com` / `admin123`) và một tài khoản khách hàng test để lúc demo không bị lúng túng.
3.  **Tác phong:** Trả lời dứt khoát, nếu gặp câu hỏi khó hoặc chưa code phần đó sâu, hãy khéo léo nói: *"Dạ phần này nhóm em đã tìm hiểu giải pháp cơ bản và có định hướng phát triển nâng cao hơn trong phiên bản tiếp theo..."* hoặc *"Chúng em tập trung tự xây dựng phần khung MVC cốt lõi nên một số chi tiết phụ sẽ tiếp tục hoàn thiện thêm ạ."*
