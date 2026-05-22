<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class Notification
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /**
     * Tạo thông báo mới cho người dùng
     */
    public function create(int $userId, string $title, string $message, ?string $link = null): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, title, message, link, is_read, created_at)
            VALUES (?, ?, ?, ?, 0, NOW())
        ");
        return $stmt->execute([$userId, $title, $message, $link]);
    }

    /**
     * Lấy danh sách tất cả thông báo của người dùng
     */
    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm số thông báo chưa đọc của người dùng
     */
    public function getUnreadCount(int $userId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM notifications 
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Đánh dấu toàn bộ thông báo của người dùng là đã đọc
     */
    public function markAllAsRead(int $userId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE notifications 
            SET is_read = 1 
            WHERE user_id = ?
        ");
        return $stmt->execute([$userId]);
    }

    /**
     * Đánh dấu một thông báo cụ thể của người dùng là đã đọc
     */
    public function markAsRead(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE notifications 
            SET is_read = 1 
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$id, $userId]);
    }
}
