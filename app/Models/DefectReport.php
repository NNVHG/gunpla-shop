<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class DefectReport
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /**
     * Tạo báo cáo sản phẩm lỗi mới
     */
    public function create(int $userId, int $productId, int $orderId, string $description, string $imageProof, string $videoProof): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO defect_reports (user_id, product_id, order_id, description, image_proof, video_proof, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
        ");
        return $stmt->execute([$userId, $productId, $orderId, $description, $imageProof, $videoProof]);
    }

    /**
     * Lấy thông tin chi tiết báo cáo lỗi theo ID
     */
    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT dr.*, p.name as product_name,
                   u.full_name as user_name, u.email as user_email,
                   o.created_at as order_date
            FROM defect_reports dr
            JOIN products p ON dr.product_id = p.id
            JOIN users u ON dr.user_id = u.id
            JOIN orders o ON dr.order_id = o.id
            WHERE dr.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách phân trang tất cả báo cáo lỗi cho Admin
     */
    public function getAllForAdmin(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        $countStmt = $this->db->query("SELECT COUNT(*) FROM defect_reports");
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT dr.*, p.name as product_name, u.full_name as user_name
            FROM defect_reports dr
            JOIN products p ON dr.product_id = p.id
            JOIN users u ON dr.user_id = u.id
            ORDER BY dr.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'items' => $items,
            'total' => $total,
            'pages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Cập nhật trạng thái báo cáo lỗi sản phẩm
     */
    public function updateStatus(int $id, string $status, ?string $adminComment = null): bool
    {
        $allowed = ['pending', 'checking', 'approved', 'shipped', 'rejected'];
        if (!in_array($status, $allowed)) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE defect_reports 
            SET status = ?, admin_comment = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$status, $adminComment, $id]);
    }
}
