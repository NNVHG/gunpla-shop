<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Favorite
{
    private PDO $db;

    public function __construct(PDO $dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function toggle(int $userId, int $productId): array
    {
        $stmt = $this->db->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $exists = $stmt->fetchColumn();

        if ($exists) {
            $stmt = $this->db->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
            return ['status' => 'removed'];
        } else {
            $stmt = $this->db->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
            $stmt->execute([$userId, $productId]);
            return ['status' => 'added'];
        }
    }

    public function getUserFavoriteIds(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT product_id FROM favorites WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getUserFavorites(int $userId): array
    {
        $sql = "SELECT p.* FROM products p 
                INNER JOIN favorites f ON p.id = f.product_id 
                WHERE f.user_id = ? 
                ORDER BY f.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
