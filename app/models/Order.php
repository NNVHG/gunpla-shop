<?php

/**
 * app/models/Order.php
 * Xử lý toàn bộ logic liên quan đến đơn hàng (orders) và chi tiết (order_items)
 */

declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;

class Order
{
    private PDO $db;

    private const SHIP_ZONES = [
        'Bình Dương'          => 15000,
        'TP. Hồ Chí Minh'     => 20000,
        'Đồng Nai'            => 22000,
        'Long An'             => 25000,
        'Hà Nội'              => 35000,
        'Đà Nẵng'             => 30000,
        'default'             => 40000,
    ];

    public function __construct()
    {
        $this->db = getDB();
    }

    public function calcShipping(string $province, int $totalGrams = 0): int
    {
        $base = self::SHIP_ZONES[$province] ?? self::SHIP_ZONES['default'];

        $extraWeight = max(0, $totalGrams - 500);
        $weightFee   = (int) ceil($extraWeight / 500) * 5000;

        return $base + $weightFee;
    }

    public function getShippingZones(): array
    {
        return self::SHIP_ZONES;
    }

    public function place(array $info, array $items, ?int $userId = null): array
    {
        if (empty($items)) {
            return ['success' => false, 'message' => 'Giỏ hàng trống'];
        }

        try {
            $this->db->beginTransaction();

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

            $totalGrams = $this->calcTotalWeight($items);

            $subtotal    = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $items));
            $shippingFee = $this->calcShipping($info['province'], $totalGrams);
            $total       = $subtotal + $shippingFee;

            $paymentMethod = $info['payment_method'] ?? 'cod';

            $stmt = $this->db->prepare("
                INSERT INTO orders (user_id, full_name, phone, province, address, note, subtotal, shipping_fee, total, status, payment_method)
                VALUES (:user_id, :full_name, :phone, :province, :address, :note, :subtotal, :shipping_fee, :total, 'pending', :payment_method)
            ");
            $stmt->execute([
                ':user_id'        => $userId,
                ':full_name'      => $info['full_name'],
                ':phone'          => $info['phone'],
                ':province'       => $info['province'],
                ':address'        => $info['address'],
                ':note'           => $info['note'] ?? null,
                ':subtotal'       => $subtotal,
                ':shipping_fee'   => $shippingFee,
                ':total'          => $total,
                ':payment_method' => $paymentMethod,
            ]);
            $orderId = (int) $this->db->lastInsertId();

            foreach ($items as $item) {
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

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC"
        );
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

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

    public function updateStatus(int $id, string $status): bool
    {
        $allowed = ['pending', 'confirmed', 'shipping', 'delivered', 'cancelled'];
        if (!in_array($status, $allowed)) return false;

        $stmt = $this->db->prepare("UPDATE orders SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function updatePaymentStatus(int $id, string $status, ?string $transactionId = null): bool
    {
        $allowed = ['unpaid', 'paid', 'failed'];
        if (!in_array($status, $allowed)) return false;

        $stmt = $this->db->prepare("UPDATE orders SET payment_status = :status, transaction_id = :txn_id WHERE id = :id");
        return $stmt->execute([
            ':status' => $status,
            ':txn_id' => $transactionId,
            ':id'     => $id
        ]);
    }

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
