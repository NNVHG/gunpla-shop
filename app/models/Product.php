<?php

/**
 * app/models/Product.php
 * Xử lý tất cả truy vấn liên quan đến bảng products và product_images
 */

declare(strict_types=1);

namespace App\Models;

use PDO;

class Product
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getFilteredProducts(array $filters, int $page = 1, int $perPage = 12): array
    {
        // Use the existing robust getAll method to fetch filtered products
        return $this->getAll($filters, 'newest', $page, $perPage);
    }

    public function getAll(
        array  $filters = [],
        string $sort    = 'newest',
        int    $page    = 1,
        int    $perPage = 12
    ): array {
        $where  = ['p.is_active = 1'];
        $params = [];

        if (!empty($filters['grade'])) {
            $where[]          = 'p.grade = :grade';
            $params[':grade'] = $filters['grade'];
        }

        if (!empty($filters['scale'])) {
            $where[]          = 'p.scale = :scale';
            $params[':scale'] = $filters['scale'];
        }

        if (!empty($filters['series'])) {
            $where[]           = 'p.series LIKE :series';
            $params[':series'] = '%' . $filters['series'] . '%';
        }

        if (!empty($filters['search'])) {
            $where[]           = '(p.name LIKE :search OR p.series LIKE :search2)';
            $params[':search']  = '%' . $filters['search'] . '%';
            $params[':search2'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['category_id'])) {
            $where[]               = 'p.category_id = :category_id';
            $params[':category_id'] = $filters['category_id'];
        }

        if (!empty($filters['type'])) {
            $where[] = 'c.type = :type';
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['group'])) {
            if ($filters['group'] === 'gunpla') {
                $where[] = "c.type IN ('scale', 'grade', 'series')";
            } elseif ($filters['group'] === 'tools') {
                $where[] = "c.type IN ('tool', 'accessory', 'chemical', 'combo')";
            }
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);

        $orderSQL = match ($sort) {
            'price_asc'  => 'ORDER BY p.price ASC',
            'price_desc' => 'ORDER BY p.price DESC',
            'bestseller' => 'ORDER BY sold_count DESC',
            default      => 'ORDER BY p.created_at DESC',
        };

        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id = c.id $whereSQL"
        );
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT
                p.*,
                c.name as category_name,
                c.type as category_type,
                pi.image_path AS thumbnail_path,
                COALESCE(sold.qty, 0) AS sold_count
            FROM products p
            JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_images pi
                   ON pi.product_id = p.id AND pi.is_primary = 1
            LEFT JOIN (
                SELECT oi.product_id, SUM(oi.quantity) AS qty
                FROM order_items oi
                JOIN orders o ON o.id = oi.order_id
                WHERE o.status != 'cancelled'
                GROUP BY oi.product_id
            ) sold ON sold.product_id = p.id
            $whereSQL
            $orderSQL
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
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
        $stmt = $this->db->prepare("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.id = :id AND p.is_active = 1
        ");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();

        if (!$product) return false;

        $imgStmt = $this->db->prepare(
            "SELECT * FROM product_images WHERE product_id = :id ORDER BY is_primary DESC, sort_order ASC"
        );
        $imgStmt->execute([':id' => $id]);
        $product['images'] = $imgStmt->fetchAll();

        return $product;
    }

    public function getBySlug(string $slug): array|false
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.slug = :slug AND p.is_active = 1
        ");
        $stmt->execute([':slug' => $slug]);
        $product = $stmt->fetch();

        if (!$product) return false;

        $imgStmt = $this->db->prepare(
            "SELECT * FROM product_images WHERE product_id = :id ORDER BY is_primary DESC, sort_order ASC"
        );
        $imgStmt->execute([':id' => $product['id']]);
        $product['images'] = $imgStmt->fetchAll();

        return $product;
    }

    public function getFeatured(int $limit = 8): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, pi.image_path AS thumbnail_path
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
            WHERE p.is_active = 1
            ORDER BY p.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO products
                (name, slug, price, stock, category_id, scale, grade, series, description, weight_gram, is_active)
            VALUES
                (:name, :slug, :price, :stock, :category_id, :scale, :grade, :series, :description, :weight_gram, :is_active)
        ");

        $stmt->execute([
            ':name'        => $data['name'],
            ':slug'        => $this->makeSlug($data['name']),
            ':price'       => $data['price'],
            ':stock'       => $data['stock']       ?? 0,
            ':category_id' => $data['category_id'],
            ':scale'       => $data['scale']       ?? null,
            ':grade'       => $data['grade']       ?? null,
            ':series'      => $data['series']      ?? null,
            ':description' => $data['description'] ?? null,
            ':weight_gram' => $data['weight_gram'] ?? null,
            ':is_active'   => $data['is_active']   ?? 1,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        $allowed = ['name', 'price', 'stock', 'category_id', 'scale', 'grade', 'series', 'description', 'weight_gram', 'is_active'];
        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $fields[]         = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }

        if (empty($fields)) return false;

        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET is_active = 0 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function adjustStock(int $id, int $delta): bool
    {
        $stmt = $this->db->prepare("
            UPDATE products
            SET stock = stock + :delta
            WHERE id = :id AND (stock + :delta2) >= 0
        ");
        return $stmt->execute([':delta' => $delta, ':delta2' => $delta, ':id' => $id]);
    }

public function uploadImage(int $productId, array $fileData, bool $isPrimary = false): string|false
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($fileData['type'], $allowedTypes)) return false;
        if ($fileData['size'] > 5 * 1024 * 1024) return false;

        $ext      = pathinfo($fileData['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . $productId . '_' . uniqid() . '.' . strtolower($ext);
        
        $dest     = BASE_PATH . '/public/uploads/img-gundam/' . $filename;

        if (!move_uploaded_file($fileData['tmp_name'], $dest)) return false;

        if ($isPrimary) {
            $this->db->prepare("UPDATE product_images SET is_primary = 0 WHERE product_id = :id")
                ->execute([':id' => $productId]);
        }

        $imagePath = 'uploads/img-gundam/' . $filename;

        $stmt = $this->db->prepare("
            INSERT INTO product_images (product_id, image_path, is_primary)
            VALUES (:product_id, :image_path, :is_primary)
        ");
        $stmt->execute([
            ':product_id' => $productId,
            ':image_path' => $imagePath,
            ':is_primary' => $isPrimary ? 1 : 0,
        ]);

        if ($isPrimary) {
            $updateProduct = $this->db->prepare("UPDATE products SET thumbnail = :thumbnail WHERE id = :id");
            $updateProduct->execute([
                ':thumbnail' => $imagePath,
                ':id' => $productId
            ]);
        }

        return $imagePath;
    }

    private function makeSlug(string $name): string
    {
        $slug = mb_strtolower($name, 'UTF-8');
        $slug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $slug);
        $slug = preg_replace('/\s+/', '-', trim($slug));
        $slug = preg_replace('/-+/', '-', $slug);

        $base  = $slug;
        $count = 1;
        while ($this->slugExists($slug)) {
            $slug = $base . '-' . $count++;
        }
        return $slug;
    }

    private function slugExists(string $slug): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE slug = :slug");
        $stmt->execute([':slug' => $slug]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
