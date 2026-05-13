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

    public function hasReviewed(int $productId, int $userId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM reviews
            WHERE product_id = :pid AND user_id = :uid
        ");
        $stmt->execute([':pid' => $productId, ':uid' => $userId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(int $productId, int $userId, int $rating, string $comment): bool
    {
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
