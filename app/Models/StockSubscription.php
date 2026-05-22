<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class StockSubscription
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /**
     * Đăng ký nhận thông báo khi có hàng trở lại
     */
    public function subscribe(int $userId, int $productId): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO back_in_stock_subscriptions (user_id, product_id, created_at)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE created_at = NOW()
        ");
        return $stmt->execute([$userId, $productId]);
    }

    /**
     * Kiểm tra người dùng đã đăng ký nhận thông báo cho sản phẩm này chưa
     */
    public function isSubscribed(int $userId, int $productId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM back_in_stock_subscriptions
            WHERE user_id = ? AND product_id = ?
        ");
        $stmt->execute([$userId, $productId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Lấy danh sách ID người dùng đã đăng ký nhận thông báo cho sản phẩm này
     */
    public function getSubscribers(int $productId): array
    {
        $stmt = $this->db->prepare("
            SELECT user_id FROM back_in_stock_subscriptions
            WHERE product_id = ?
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Xóa các đăng ký sau khi đã gửi thông báo xong
     */
    public function deleteSubscriptions(int $productId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM back_in_stock_subscriptions
            WHERE product_id = ?
        ");
        return $stmt->execute([$productId]);
    }
}
