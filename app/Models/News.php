<?php

/**
 * app/Models/News.php
 * Xử lý truy vấn liên quan đến bảng news
 */

declare(strict_types=1);

namespace App\Models;

use PDO;

class News
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll(int $page = 1, int $perPage = 10, bool $onlyPublished = true): array
    {
        $where = $onlyPublished ? 'WHERE is_published = 1' : '';
        
        $countStmt = $this->db->query("SELECT COUNT(*) FROM news $where");
        $total = (int) $countStmt->fetchColumn();
        
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT * FROM news 
            $where 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'pages' => (int) ceil($total / $perPage),
            'page'  => $page,
        ];
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM news WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getBySlug(string $slug): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM news WHERE slug = :slug AND is_published = 1");
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch();
    }

    public function getLatest(int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM news 
            WHERE is_published = 1 
            ORDER BY created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
