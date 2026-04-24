<?php
/**
 * app/controllers/CartController.php
 * Giỏ hàng lưu trong $_SESSION['cart']
 *
 * Cấu trúc session cart:
 * $_SESSION['cart'] = [
 *     product_id => [
 *         'id'    => int,
 *         'name'  => string,
 *         'price' => int,
 *         'qty'   => int,
 *         'scale' => string,
 *         'grade' => string,
 *         'thumb' => string,
 *     ],
 *     ...
 * ]
 *
 * Routes:
 *   POST /cart/add       → add()
 *   POST /cart/update    → update()
 *   POST /cart/remove    → remove()
 *   GET  /cart           → view()
 *   POST /cart/clear     → clear()
 *   GET  /cart/count     → count() [AJAX]
 *   GET  /cart/shipping  → shipping() [AJAX — tính phí ship]
 */

declare(strict_types=1);

class CartController
{
    private Product $productModel;
    private Order   $orderModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->orderModel   = new Order();
    }

    // ─────────────────────────────────────────────
    //  THÊM VÀO GIỎ
    // ─────────────────────────────────────────────

    /**
     * POST /cart/add
     * Body: { product_id: int, qty: int }
     */
    public function add(): void
    {
        $this->requirePost();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $qty       = max(1, (int) ($_POST['qty'] ?? 1));

        if (!$productId) {
            $this->jsonError('Thiếu product_id');
            return;
        }

        $product = $this->productModel->getById($productId);

        if (!$product) {
            $this->jsonError('Sản phẩm không tồn tại');
            return;
        }

        if ($product['stock'] <= 0) {
            $this->jsonError('Sản phẩm đã hết hàng');
            return;
        }

        // Tổng qty trong giỏ + qty thêm mới không được vượt stock
        $currentQty = $_SESSION['cart'][$productId]['qty'] ?? 0;
        if ($currentQty + $qty > $product['stock']) {
            $this->jsonError("Chỉ còn {$product['stock']} sản phẩm trong kho");
            return;
        }

        // Thêm hoặc cộng dồn số lượng
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$productId] = [
                'id'    => $product['id'],
                'name'  => $product['name'],
                'price' => (int) $product['price'],
                'qty'   => $qty,
                'scale' => $product['scale'],
                'grade' => $product['grade'],
                'thumb' => $product['thumbnail_path'] ?? null,
            ];
        }

        $this->jsonSuccess([
            'message'    => 'Đã thêm vào giỏ hàng',
            'cart_count' => $this->totalItems(),
            'cart_total' => $this->totalAmount(),
        ]);
    }

    // ─────────────────────────────────────────────
    //  CẬP NHẬT SỐ LƯỢNG
    // ─────────────────────────────────────────────

    /**
     * POST /cart/update
     * Body: { product_id: int, qty: int }
     */
    public function update(): void
    {
        $this->requirePost();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $qty       = (int) ($_POST['qty'] ?? 0);

        if (!isset($_SESSION['cart'][$productId])) {
            $this->jsonError('Sản phẩm không có trong giỏ');
            return;
        }

        if ($qty <= 0) {
            // qty = 0 → xóa khỏi giỏ
            unset($_SESSION['cart'][$productId]);
        } else {
            // Kiểm tra tồn kho
            $product = $this->productModel->getById($productId);
            if ($product && $qty > $product['stock']) {
                $this->jsonError("Chỉ còn {$product['stock']} sản phẩm trong kho");
                return;
            }
            $_SESSION['cart'][$productId]['qty'] = $qty;
        }

        $this->jsonSuccess([
            'cart_count' => $this->totalItems(),
            'cart_total' => $this->totalAmount(),
            'items'      => $this->getItems(),
        ]);
    }

    // ─────────────────────────────────────────────
    //  XÓA KHỎI GIỎ
    // ─────────────────────────────────────────────

    /**
     * POST /cart/remove
     * Body: { product_id: int }
     */
    public function remove(): void
    {
        $this->requirePost();
        $productId = (int) ($_POST['product_id'] ?? 0);
        unset($_SESSION['cart'][$productId]);

        $this->jsonSuccess([
            'cart_count' => $this->totalItems(),
            'cart_total' => $this->totalAmount(),
        ]);
    }

    // ─────────────────────────────────────────────
    //  XEM GIỎ HÀNG (trang checkout)
    // ─────────────────────────────────────────────

    public function view(): void
    {
        $data = [
            'title'         => 'Giỏ hàng — GUNPLA SHOP',
            'items'         => $this->getItems(),
            'subtotal'      => $this->totalAmount(),
            'shippingZones' => $this->orderModel->getShippingZones(),
        ];
        $this->render('cart/index', $data);
    }

    // ─────────────────────────────────────────────
    //  XÓA TOÀN BỘ GIỎ
    // ─────────────────────────────────────────────

    public function clear(): void
    {
        $_SESSION['cart'] = [];
        $this->jsonSuccess(['message' => 'Đã xóa giỏ hàng']);
    }

    // ─────────────────────────────────────────────
    //  ĐẾM SỐ SẢN PHẨM (AJAX — cập nhật badge navbar)
    // ─────────────────────────────────────────────

    public function count(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['count' => $this->totalItems()]);
        exit;
    }

    // ─────────────────────────────────────────────
    //  TÍNH PHÍ SHIP (AJAX)
    // ─────────────────────────────────────────────

    /**
     * GET /cart/shipping?province=Bình+Dương
     */
    public function shipping(): void
    {
        $province = htmlspecialchars($_GET['province'] ?? 'default');
        $fee      = $this->orderModel->calcShipping($province);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'province'     => $province,
            'shipping_fee' => $fee,
            'total'        => $this->totalAmount() + $fee,
        ]);
        exit;
    }

    // ─────────────────────────────────────────────
    //  HELPER — Đọc / Tính giỏ hàng
    // ─────────────────────────────────────────────

    /** Trả về mảng sản phẩm trong giỏ */
    public function getItems(): array
    {
        return array_values($_SESSION['cart'] ?? []);
    }

    /** Trả về JSON danh sách items — dùng cho cart sidebar JS */
    public function items(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->getItems());
        exit;
    }

    /** Tổng số lượng (số item, không phải số dòng) */
    private function totalItems(): int
    {
        return array_sum(array_column($_SESSION['cart'] ?? [], 'qty'));
    }

    /** Tổng tiền hàng (chưa tính ship) */
    private function totalAmount(): int
    {
        $total = 0;
        foreach ($_SESSION['cart'] ?? [] as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }

    // ─────────────────────────────────────────────
    //  HELPER — Response
    // ─────────────────────────────────────────────

    private function jsonSuccess(array $data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true] + $data);
        exit;
    }

    private function jsonError(string $message, int $code = 400): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }

    private function requirePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }
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
}
