<?php
/**
 * app/models/Order.php
 * Xử lý toàn bộ logic liên quan đến đơn hàng (orders) và chi tiết (order_items)
 */

declare(strict_types=1);

class Order
{
    private PDO $db;

    // Phí ship theo tỉnh/thành — đơn vị VNĐ
    // Dựa trên vùng miền, trọng lượng tính thêm ở calcShipping()
    private const SHIP_ZONES = [
        'Bình Dương'          => 15000,
        'TP. Hồ Chí Minh'     => 20000,
        'Đồng Nai'            => 22000,
        'Long An'             => 25000,
        'Hà Nội'              => 35000,
        'Đà Nẵng'             => 30000,
        'default'             => 40000,  // Các tỉnh còn lại
    ];

    public function __construct()
    {
        $this->db = getDB();
    }

    // ─────────────────────────────────────────────
    //  TÍNH PHÍ SHIP
    // ─────────────────────────────────────────────

    /**
     * Tính phí vận chuyển (phí ship) dựa trên tỉnh và tổng trọng lượng
     *
     * @param string $province   Tên tỉnh/thành phố
     * @param int    $totalGrams Tổng trọng lượng đơn hàng (gram)
     * @return int  Phí ship (VNĐ)
     */
    public function calcShipping(string $province, int $totalGrams = 0): int
    {
        $base = self::SHIP_ZONES[$province] ?? self::SHIP_ZONES['default'];

        // Phụ phí trọng lượng: +5.000đ mỗi 500g vượt quá 500g đầu
        $extraWeight = max(0, $totalGrams - 500);
        $weightFee   = (int) ceil($extraWeight / 500) * 5000;

        return $base + $weightFee;
    }

    /**
     * Trả về danh sách tỉnh/thành và phí cơ bản — dùng cho dropdown checkout
     */
    public function getShippingZones(): array
    {
        return self::SHIP_ZONES;
    }

    // ─────────────────────────────────────────────
    //  TẠO ĐƠN HÀNG
    // ─────────────────────────────────────────────

    /**
     * Đặt hàng — tạo đơn + trừ kho, bọc trong transaction
     *
     * @param array $info    Thông tin người nhận: full_name, phone, province, address, note
     * @param array $items   Mảng sản phẩm từ giỏ hàng: [['id'=>1,'qty'=>2,'price'=>250000], ...]
     * @param int|null $userId  null nếu đặt không cần tài khoản
     * @return array  ['success'=>bool, 'order_id'=>int, 'message'=>string]
     */
    public function place(array $info, array $items, ?int $userId = null): array
    {
        if (empty($items)) {
            return ['success' => false, 'message' => 'Giỏ hàng trống'];
        }

        try {
            $this->db->beginTransaction();

            // Kiểm tra tồn kho từng sản phẩm trước khi tạo đơn
            foreach ($items as $item) {
                $stmt = $this->db->prepare(
                    "SELECT stock, name FROM products WHERE id = :id AND is_active = 1 FOR UPDATE"
                );
                $stmt->execute([':id' => $item['id']]);
                $product = $stmt->fetch();

                if (!$product) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => "Sản phẩm ID {$item['id']} không tồn tại"];
                }
                if ($product['stock'] < $item['qty']) {
                    $this->db->rollBack();
                    return [
                        'success' => false,
                        'message' => "Sản phẩm \"{$product['name']}\" chỉ còn {$product['stock']} chiếc",
                    ];
                }
            }

            // Tính tổng trọng lượng
            $totalGrams = $this->calcTotalWeight($items);

            // Tính tiền
            $subtotal    = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $items));
            $shippingFee = $this->calcShipping($info['province'], $totalGrams);
            $total       = $subtotal + $shippingFee;

            // Tạo đơn hàng
            $stmt = $this->db->prepare("
                INSERT INTO orders (user_id, full_name, phone, province, address, note, subtotal, shipping_fee, total, status)
                VALUES (:user_id, :full_name, :phone, :province, :address, :note, :subtotal, :shipping_fee, :total, 'pending')
            ");
            $stmt->execute([
                ':user_id'      => $userId,
                ':full_name'    => $info['full_name'],
                ':phone'        => $info['phone'],
                ':province'     => $info['province'],
                ':address'      => $info['address'],
                ':note'         => $info['note'] ?? null,
                ':subtotal'     => $subtotal,
                ':shipping_fee' => $shippingFee,
                ':total'        => $total,
            ]);
            $orderId = (int) $this->db->lastInsertId();

            // Thêm từng sản phẩm vào order_items và trừ kho
            foreach ($items as $item) {
                // Lấy tên sản phẩm tại thời điểm đặt (snapshot)
                $nameSql  = $this->db->prepare("SELECT name FROM products WHERE id = :id");
                $nameSql->execute([':id' => $item['id']]);
                $prodName = $nameSql->fetchColumn();

                $insertItem = $this->db->prepare("
                    INSERT INTO order_items (order_id, product_id, product_name, quantity, price_at_order)
                    VALUES (:order_id, :product_id, :product_name, :quantity, :price_at_order)
                ");
                $insertItem->execute([
                    ':order_id'       => $orderId,
                    ':product_id'     => $item['id'],
                    ':product_name'   => $prodName,
                    ':quantity'       => $item['qty'],
                    ':price_at_order' => $item['price'],
                ]);

                // Trừ kho
                $this->db->prepare("UPDATE products SET stock = stock - :qty WHERE id = :id")
                         ->execute([':qty' => $item['qty'], ':id' => $item['id']]);
            }

            $this->db->commit();

            return [
                'success'       => true,
                'order_id'      => $orderId,
                'total'         => $total,
                'shipping_fee'  => $shippingFee,
                'message'       => 'Đặt hàng thành công',
            ];

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Order::place() error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại'];
        }
    }

    // ─────────────────────────────────────────────
    //  ĐỌC DỮ LIỆU
    // ─────────────────────────────────────────────

    /**
     * Lấy thông tin 1 đơn hàng kèm danh sách sản phẩm
     */
    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch();
        if (!$order) return false;

        $itemStmt = $this->db->prepare("
            SELECT oi.*, pi.image_path AS thumbnail_path
            FROM order_items oi
            LEFT JOIN product_images pi ON pi.product_id = oi.product_id AND pi.is_primary = 1
            WHERE oi.order_id = :order_id
        ");
        $itemStmt->execute([':order_id' => $id]);
        $order['items'] = $itemStmt->fetchAll();

        return $order;
    }

    /**
     * Lấy danh sách đơn của 1 user
     */
    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC"
        );
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy tất cả đơn (dùng cho Admin) — có phân trang
     */
    public function getAll(string $status = '', int $page = 1, int $perPage = 20): array
    {
        $where  = [];
        $params = [];

        if ($status !== '') {
            $where[]          = 'status = :status';
            $params[':status'] = $status;
        }

        $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset   = ($page - 1) * $perPage;

        $count = $this->db->prepare("SELECT COUNT(*) FROM orders $whereSQL");
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT * FROM orders $whereSQL ORDER BY created_at DESC LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();

        return ['items' => $stmt->fetchAll(), 'total' => $total, 'pages' => (int) ceil($total / $perPage)];
    }

    /**
     * Cập nhật trạng thái đơn hàng (dùng cho Admin)
     */
    public function updateStatus(int $id, string $status): bool
    {
        $allowed = ['pending','confirmed','shipping','delivered','cancelled'];
        if (!in_array($status, $allowed)) return false;

        $stmt = $this->db->prepare("UPDATE orders SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    // ─────────────────────────────────────────────
    //  HELPER
    // ─────────────────────────────────────────────

    private function calcTotalWeight(array $items): int
    {
        $ids = array_column($items, 'id');
        if (empty($ids)) return 0;

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare(
            "SELECT id, COALESCE(weight_gram, 200) AS weight_gram FROM products WHERE id IN ($placeholders)"
        );
        $stmt->execute($ids);
        $weights = array_column($stmt->fetchAll(), 'weight_gram', 'id');

        $total = 0;
        foreach ($items as $item) {
            $total += ($weights[$item['id']] ?? 200) * $item['qty'];
        }
        return $total;
    }
}
