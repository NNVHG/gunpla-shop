<?php

/**
 * app/controllers/OrderController.php
 * Xử lý đặt hàng và gửi email xác nhận qua PHPMailer + Gmail SMTP
 *
 * Routes:
 *   GET  /orders/checkout   → checkout()  — trang thanh toán
 *   POST /orders/place      → place()     — xử lý đặt hàng
 *   GET  /orders/success    → success()   — trang cảm ơn
 *   GET  /orders/detail/{id}→ detail($id) — tra cứu đơn hàng
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

class OrderController
{
    private Order       $orderModel;
    private CartController $cartCtrl;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->cartCtrl   = new CartController();
    }

    public function checkout(): void
    {
        $items = $this->cartCtrl->getItems();

        if (empty($items)) {
            $this->redirect('/');
            return;
        }

        if (!isset($_SESSION['user']['id'])) {
            $this->redirect('/user/login?redirect=/orders/checkout');
            return;
        }

        $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $items));

        $data = [
            'title'         => 'Thanh toán — GUNPLA SHOP',
            'items'         => $items,
            'subtotal'      => $subtotal,
            'shippingZones' => $this->orderModel->getShippingZones(),
            'user'          => $_SESSION['user'] ?? null,
        ];

        $this->render('orders/checkout', $data);
    }

    public function place(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/orders/checkout');
            return;
        }

        $errors = $this->validateCheckoutForm($_POST);
        if (!empty($errors)) {
            $_SESSION['checkout_errors'] = $errors;
            $_SESSION['checkout_form']   = $_POST;
            $this->redirect('/orders/checkout');
            return;
        }

        $items = $this->cartCtrl->getItems();
        if (empty($items)) {
            $this->redirect('/cart');
            return;
        }

        $info = [
            'full_name'      => htmlspecialchars(trim($_POST['full_name'])),
            'phone'          => htmlspecialchars(trim($_POST['phone'])),
            'province'       => htmlspecialchars(trim($_POST['province'])),
            'address'        => htmlspecialchars(trim($_POST['address'])),
            'note'           => htmlspecialchars(trim($_POST['note'] ?? '')),
            'payment_method' => $_POST['payment_method'] ?? 'cod',
        ];

        $userId = $_SESSION['user']['id'] ?? null;

        $result = $this->orderModel->place($info, $items, $userId);

        if (!$result['success']) {
            $_SESSION['order_error'] = $result['message'];
            $this->redirect('/orders/checkout');
            return;
        }

        if ($info['payment_method'] === 'vnpay') {

            $vnp_Url = VNP_URL;
            $vnp_Returnurl = VNP_RETURNURL;
            $vnp_TmnCode = VNP_TMNCODE;
            $vnp_HashSecret = VNP_HASHSECRET;

            $vnp_TxnRef = (string) $result['order_id'];
            $vnp_OrderInfo = 'Thanh toan don hang ' . $result['order_id'];
            $vnp_OrderType = 'billpayment';

            $vnp_Amount = round((float)$result['total'] * 100);
            $vnp_Locale = 'vn';

            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
            if ($vnp_IpAddr === '::1' || $vnp_IpAddr === '127.0.0.1') {
                $vnp_IpAddr = '127.0.0.1';
            }

            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $startTime = date('YmdHis');
            $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => $startTime,
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
                "vnp_ExpireDate" => $expire
            );

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode((string)$value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode((string)$value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode((string)$value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }

            header('Location: ' . $vnp_Url);
            exit;
        }

        $orderData = $this->orderModel->getById($result['order_id']);
        $this->sendConfirmationEmail($info, $orderData);

        $_SESSION['cart'] = [];

        $_SESSION['last_order_id'] = $result['order_id'];

        $this->redirect('/orders/success');
    }

    public function vnpayReturn(): void
    {
        $vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode((string)$value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode((string)$value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, VNP_HASHSECRET);
        $orderId = (int)($_GET['vnp_TxnRef'] ?? 0);

        if ($secureHash === $vnp_SecureHash) {
            if ($_GET['vnp_ResponseCode'] == '00') {

                $this->orderModel->updatePaymentStatus($orderId, 'paid', $_GET['vnp_TransactionNo']);
                $_SESSION['cart'] = [];
                $_SESSION['last_order_id'] = $orderId;
                $this->redirect('/orders/success');
            } else {

                $this->orderModel->updatePaymentStatus($orderId, 'failed', '');
                $_SESSION['order_error'] = 'Thanh toán không thành công hoặc bị hủy (Mã lỗi: ' . $_GET['vnp_ResponseCode'] . ')!';
                $this->redirect('/orders/checkout');
            }
        } else {
            $_SESSION['order_error'] = 'Chữ ký bảo mật không hợp lệ!';
            $this->redirect('/orders/checkout');
        }
    }

    public function success(): void
    {
        $orderId = $_SESSION['last_order_id'] ?? null;
        if (!$orderId) {
            $this->redirect('/');
            return;
        }
        unset($_SESSION['last_order_id']);

        $order = $this->orderModel->getById((int) $orderId);
        $data  = [
            'title' => 'Đặt hàng thành công — GUNPLA SHOP',
            'order' => $order,
        ];
        $this->render('orders/success', $data);
    }

    public function detail(?string $param): void
    {
        $orderId = (int) ($param ?? 0);
        if (!$orderId) {
            $this->redirect('/');
            return;
        }

        $order = $this->orderModel->getById($orderId);
        if (!$order) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Không tìm thấy đơn hàng']);
            return;
        }

        $userId = $_SESSION['user']['id'] ?? null;
        $isAdmin = ($_SESSION['user']['role'] ?? '') === 'admin';
        if (!$isAdmin && $order['user_id'] !== $userId) {
            http_response_code(403);
            $this->render('errors/403', ['title' => 'Không có quyền truy cập']);
            return;
        }

        $data = [
            'title' => "Đơn hàng #{$orderId} — GUNPLA SHOP",
            'order' => $order,
        ];
        $this->render('orders/detail', $data);
    }

    private function sendConfirmationEmail(array $info, array $order): void
    {
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            error_log('PHPMailer chưa được cài. Chạy: composer require phpmailer/phpmailer');
            return;
        }

        $toEmail = $_SESSION['user']['email'] ?? ($_POST['email'] ?? null);
        if (!$toEmail) return;

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = defined('MAIL_USER') ? MAIL_USER : 'your_gmail@gmail.com';
            $mail->Password   = defined('MAIL_PASS') ? MAIL_PASS : 'your_app_password';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(
                defined('MAIL_USER') ? MAIL_USER : 'your_gmail@gmail.com',
                'GUNPLA SHOP'
            );
            $mail->addAddress($toEmail, $info['full_name']);

            $mail->isHTML(true);
            $mail->Subject = "✅ Xác nhận đơn hàng #{$order['id']} — GUNPLA SHOP";
            $mail->Body    = $this->buildEmailHtml($info, $order);
            $mail->AltBody = $this->buildEmailText($info, $order);

            $mail->send();
        } catch (Exception $e) {
            error_log('Lỗi gửi email: ' . $mail->ErrorInfo);
        }
    }

    private function buildEmailHtml(array $info, array $order): string
    {
        $itemRows = '';
        foreach ($order['items'] as $item) {
            $price = number_format((float)$item['price_at_order'], 0, ',', '.') . 'đ';
            $total = number_format((float)($item['price_at_order'] * $item['quantity']), 0, ',', '.') . 'đ';
            $itemRows .= "
            <tr>
                <td style='padding:12px 16px;border-bottom:1px solid #2a2d31;color:#e8e4dc'>
                    {$item['product_name']}
                </td>
                <td style='padding:12px 16px;border-bottom:1px solid #2a2d31;color:#8a8780;text-align:center'>
                    {$item['quantity']}
                </td>
                <td style='padding:12px 16px;border-bottom:1px solid #2a2d31;color:#c8a85a;text-align:right'>
                    {$price}
                </td>
                <td style='padding:12px 16px;border-bottom:1px solid #2a2d31;color:#c8a85a;text-align:right;font-weight:bold'>
                    {$total}
                </td>
            </tr>";
        }

        $subtotal    = number_format((float)$order['subtotal'],     0, ',', '.') . 'đ';
        $shippingFee = number_format((float)$order['shipping_fee'], 0, ',', '.') . 'đ';
        $totalAmount = number_format((float)$order['total'],        0, ',', '.') . 'đ';

        return "
        <!DOCTYPE html>
        <html lang='vi'>
        <head><meta charset='UTF-8'><meta name='viewport' content='width=device-width'></head>
        <body style='margin:0;padding:0;background:#080909;font-family:\"Segoe UI\",sans-serif'>
          <div style='max-width:600px;margin:0 auto;padding:32px 16px'>

            <!-- Header -->
            <div style='text-align:center;margin-bottom:32px'>
              <h1 style='font-size:32px;letter-spacing:0.12em;color:#e8e4dc;margin:0'>
                GUNPLA<span style='color:#c8a85a'>SHOP</span>
              </h1>
              <p style='color:#8a8780;font-size:12px;letter-spacing:0.15em;margin:8px 0 0'>
                MÔ HÌNH LẮP RÁP CHÍNH HÃNG
              </p>
            </div>

            <!-- Banner xác nhận -->
            <div style='background:#141618;border:1px solid #c8a85a;border-radius:8px;padding:24px;text-align:center;margin-bottom:24px'>
              <div style='font-size:28px;margin-bottom:8px'>✅</div>
              <h2 style='color:#c8a85a;margin:0 0 8px;font-size:20px;letter-spacing:0.08em'>
                ĐẶT HÀNG THÀNH CÔNG
              </h2>
              <p style='color:#8a8780;margin:0;font-size:13px'>
                Mã đơn hàng: <strong style='color:#e8e4dc'>#{$order['id']}</strong>
              </p>
            </div>

            <!-- Thông tin giao hàng -->
            <div style='background:#141618;border:1px solid #2a2d31;border-radius:8px;padding:20px;margin-bottom:16px'>
              <h3 style='color:#c8a85a;font-size:11px;letter-spacing:0.15em;margin:0 0 16px;text-transform:uppercase'>
                // Thông tin giao hàng
              </h3>
              <table style='width:100%;font-size:13px'>
                <tr>
                  <td style='color:#8a8780;padding:4px 0;width:120px'>Người nhận</td>
                  <td style='color:#e8e4dc'>{$info['full_name']}</td>
                </tr>
                <tr>
                  <td style='color:#8a8780;padding:4px 0'>Điện thoại</td>
                  <td style='color:#e8e4dc'>{$info['phone']}</td>
                </tr>
                <tr>
                  <td style='color:#8a8780;padding:4px 0'>Địa chỉ</td>
                  <td style='color:#e8e4dc'>{$info['address']}, {$info['province']}</td>
                </tr>
              </table>
            </div>

            <!-- Danh sách sản phẩm -->
            <div style='background:#141618;border:1px solid #2a2d31;border-radius:8px;overflow:hidden;margin-bottom:16px'>
              <h3 style='color:#c8a85a;font-size:11px;letter-spacing:0.15em;margin:0;padding:16px;text-transform:uppercase;border-bottom:1px solid #2a2d31'>
                // Sản phẩm đã đặt
              </h3>
              <table style='width:100%;border-collapse:collapse;font-size:13px'>
                <thead>
                  <tr style='background:#0f1011'>
                    <th style='padding:10px 16px;text-align:left;color:#4a4844;font-size:10px;letter-spacing:0.1em;font-weight:normal'>TÊN SẢN PHẨM</th>
                    <th style='padding:10px 16px;text-align:center;color:#4a4844;font-size:10px;letter-spacing:0.1em;font-weight:normal'>SL</th>
                    <th style='padding:10px 16px;text-align:right;color:#4a4844;font-size:10px;letter-spacing:0.1em;font-weight:normal'>ĐƠN GIÁ</th>
                    <th style='padding:10px 16px;text-align:right;color:#4a4844;font-size:10px;letter-spacing:0.1em;font-weight:normal'>THÀNH TIỀN</th>
                  </tr>
                </thead>
                <tbody>$itemRows</tbody>
              </table>
              <!-- Tổng cộng -->
              <div style='padding:16px;border-top:1px solid #2a2d31'>
                <div style='display:flex;justify-content:space-between;margin-bottom:8px;font-size:13px'>
                  <span style='color:#8a8780'>Tạm tính</span>
                  <span style='color:#e8e4dc'>$subtotal</span>
                </div>
                <div style='display:flex;justify-content:space-between;margin-bottom:12px;font-size:13px'>
                  <span style='color:#8a8780'>Phí vận chuyển</span>
                  <span style='color:#e8e4dc'>$shippingFee</span>
                </div>
                <div style='display:flex;justify-content:space-between;padding-top:12px;border-top:1px solid #2a2d31'>
                  <span style='color:#e8e4dc;font-size:14px;letter-spacing:0.06em'>TỔNG CỘNG</span>
                  <span style='color:#c8a85a;font-size:20px;font-weight:bold'>$totalAmount</span>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <p style='color:#4a4844;font-size:11px;text-align:center;letter-spacing:0.08em;margin-top:32px'>
              GUNPLA SHOP — Bình Dương, Việt Nam<br>
              Email: info@gunplashop.vn
            </p>
          </div>
        </body>
        </html>";
    }

    private function buildEmailText(array $info, array $order): string
    {
        $lines  = ["GUNPLA SHOP — Xác nhận đơn hàng #{$order['id']}", str_repeat('-', 40)];
        $lines[] = "Người nhận: {$info['full_name']}";
        $lines[] = "Điện thoại: {$info['phone']}";
        $lines[] = "Địa chỉ: {$info['address']}, {$info['province']}";
        $lines[] = str_repeat('-', 40);
        foreach ($order['items'] as $item) {
            $lines[] = "{$item['product_name']} x{$item['quantity']} — "
                . number_format((float)($item['price_at_order'] * $item['quantity']), 0, ',', '.') . "đ";
        }
        $lines[] = str_repeat('-', 40);
        $lines[] = "Tổng cộng: " . number_format((float)$order['total'], 0, ',', '.') . "đ";
        return implode("\n", $lines);
    }

    private function validateCheckoutForm(array $post): array
    {
        $errors = [];

        if (empty(trim($post['full_name'] ?? '')))
            $errors['full_name'] = 'Vui lòng nhập họ tên';

        if (empty(trim($post['phone'] ?? '')))
            $errors['phone'] = 'Vui lòng nhập số điện thoại';
        elseif (!preg_match('/^(0|\+84)[0-9]{9,10}$/', trim($post['phone'])))
            $errors['phone'] = 'Số điện thoại không hợp lệ';

        if (empty(trim($post['province'] ?? '')))
            $errors['province'] = 'Vui lòng chọn tỉnh/thành phố';

        if (empty(trim($post['address'] ?? '')))
            $errors['address'] = 'Vui lòng nhập địa chỉ';

        return $errors;
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        ob_start();
        if (file_exists($viewFile)) include $viewFile;
        $content = ob_get_clean();
        include APP_PATH . '/views/layouts/main.php';
    }

    private function redirect(string $url): void
    {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit;
    }
}
