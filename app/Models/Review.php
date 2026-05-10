<?php
/**
 * app/Models/Review.php
 * Xử lý đánh giá sản phẩm (bảng `reviews`)
 */

declare(strict_types=1);

namespace App\Models;

use PDO;

class Review
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    // ─────────────────────────────────────────────
    //  ĐỌC DỮ LIỆU
    // ─────────────────────────────────────────────

    /**
     * Lấy tất cả đánh giá đã được duyệt (status = 'approved')
     * của một sản phẩm, kèm tên người đánh giá.
     *
     * @param int $productId
     * @return array  Mảng đánh giá — mỗi phần tử có: id, user_id, full_name,
     *                rating, comment, created_at
     */
    public function getByProduct(int $productId): array
    {
        $stmt = $this->db->prepare("
            SELECT r.id,
                   r.user_id,
                   r.rating,
                   r.comment,
                   r.created_at,
                   u.full_name
            FROM   reviews r
            INNER JOIN users u ON u.id = r.user_id
            WHERE  r.product_id = :product_id
              AND  r.status     = 'approved'
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll();
    }

    /**
     * Tính điểm trung bình (trả về float từ 1.0 – 5.0 hoặc 0.0 nếu chưa có đánh giá).
     *
     * @param int $productId
     * @return array{avg: float, total: int}
     */
    public function getAvgRating(int $productId): array
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(AVG(rating), 0) AS avg_rating,
                   COUNT(*)                 AS total
            FROM   reviews
            WHERE  product_id = :product_id
              AND  status     = 'approved'
        ");
        $stmt->execute([':product_id' => $productId]);
        $row = $stmt->fetch();
        return ['avg' => round((float) $row['avg_rating'], 1), 'total' => (int) $row['total']];
    }

    /**
     * Kiểm tra người dùng đã đánh giá sản phẩm này chưa
     * (mỗi user chỉ được đánh giá 1 lần / 1 sản phẩm).
     *
     * @param int $productId
     * @param int $userId
     * @return bool
     */
    public function hasReviewed(int $productId, int $userId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM reviews
            WHERE product_id = :pid AND user_id = :uid
        ");
        $stmt->execute([':pid' => $productId, ':uid' => $userId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    // ─────────────────────────────────────────────
    //  GHI DỮ LIỆU
    // ─────────────────────────────────────────────

    /**
     * Thêm mới một đánh giá.
     * Chỉ gọi hàm này sau khi đã xác thực user đã đăng nhập ở Controller.
     *
     * @param int    $productId
     * @param int    $userId
     * @param int    $rating   1 – 5
     * @param string $comment  Nội dung bình luận
     * @return bool  true nếu thêm thành công
     */
    public function create(int $productId, int $userId, int $rating, string $comment): bool
    {
        // Chặn giá trị rating ngoài phạm vi hợp lệ
        $rating = max(1, min(5, $rating));

        $stmt = $this->db->prepare("
            INSERT INTO reviews (product_id, user_id, rating, comment, status, created_at)
            VALUES (:product_id, :user_id, :rating, :comment, 'approved', NOW())
        ");
        return $stmt->execute([
            ':product_id' => $productId,
            ':user_id'    => $userId,
            ':rating'     => $rating,
            ':comment'    => trim($comment),
        ]);
    }
}
