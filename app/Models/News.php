<?php
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

    // 1. Cập nhật hàm getAll để hỗ trợ 2 tham số phân trang (Limit, Offset)
    public function getAll(int $limit = 1000, int $offset = 0): array
    {
        $stmt = $this->db->prepare("SELECT * FROM news ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Thêm hàm tìm kiếm tin tức theo Slug để hiển thị chi tiết
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM news WHERE slug = :slug AND is_active = 1");
        $stmt->execute([':slug' => $slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // 3. Thêm hàm lấy danh sách tin tức mới nhất (ví dụ: cho Sidebar)
    public function getLatest(int $limit = 5): array
    {
        $stmt = $this->db->prepare("SELECT * FROM news WHERE is_active = 1 ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM news WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO news (title, slug, summary, content, thumbnail, is_active, created_at, updated_at) 
            VALUES (:title, :slug, :summary, :content, :thumbnail, :is_active, NOW(), NOW())
        ");
        return $stmt->execute([
            ':title'     => $data['title'],
            ':slug'      => $data['slug'],
            ':summary'   => $data['summary'],
            ':content'   => $data['content'],
            ':thumbnail' => $data['thumbnail'],
            ':is_active' => $data['is_active']
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE news 
            SET title = :title, 
                slug = :slug, 
                summary = :summary, 
                content = :content, 
                thumbnail = :thumbnail, 
                is_active = :is_active, 
                updated_at = NOW() 
            WHERE id = :id
        ");
        
        return $stmt->execute([
            ':title'     => $data['title'],
            ':slug'      => $data['slug'],
            ':summary'   => $data['summary'],
            ':content'   => $data['content'],
            ':thumbnail' => $data['thumbnail'],
            ':is_active' => $data['is_active'],
            ':id'        => $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM news WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Lấy toàn bộ danh sách bài viết công khai ngoài trang chủ
     * @return array
     */
    public function getAllActive(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM news WHERE is_active = 1 ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm kiếm tin tức theo từ khóa
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function search(string $query, int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM news 
            WHERE is_active = 1 AND (title LIKE :query OR summary LIKE :query2 OR content LIKE :query3) 
            ORDER BY created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':query', '%' . $query . '%');
        $stmt->bindValue(':query2', '%' . $query . '%');
        $stmt->bindValue(':query3', '%' . $query . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}