<?php
/**
 * app/models/Product.php
 * Xử lý tất cả truy vấn liên quan đến bảng products và product_images
 */

declare(strict_types=1);

class Product
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    // ─────────────────────────────────────────────
    //  ĐỌC DỮ LIỆU (READ)
    // ─────────────────────────────────────────────

    /**
     * Lấy danh sách sản phẩm — có lọc, sắp xếp, phân trang
     *
     * @param array $filters  ['grade'=>'HG', 'scale'=>'1/144', 'series'=>'...', 'search'=>'...']
     * @param string $sort    'newest' | 'price_asc' | 'price_desc' | 'bestseller'
     * @param int $page       Trang hiện tại (bắt đầu từ 1)
     * @param int $perPage    Số sản phẩm mỗi trang
     * @return array          ['items'=>[...], 'total'=>int, 'pages'=>int]
     */
    public function getAll(
        array  $filters = [],
        string $sort    = 'newest',
        int    $page    = 1,
        int    $perPage = 12
    ): array {
        $where  = ['p.is_active = 1'];
        $params = [];

        // Lọc theo grade (dòng)
        if (!empty($filters['grade'])) {
            $where[]          = 'p.grade = :grade';
            $params[':grade'] = $filters['grade'];
        }

        // Lọc theo scale (tỷ lệ)
        if (!empty($filters['scale'])) {
            $where[]          = 'p.scale = :scale';
            $params[':scale'] = $filters['scale'];
        }

        // Lọc theo series (dòng phim)
        if (!empty($filters['series'])) {
            $where[]           = 'p.series LIKE :series';
            $params[':series'] = '%' . $filters['series'] . '%';
        }

        // Tìm kiếm theo tên
        if (!empty($filters['search'])) {
            $where[]           = '(p.name LIKE :search OR p.series LIKE :search2)';
            $params[':search']  = '%' . $filters['search'] . '%';
            $params[':search2'] = '%' . $filters['search'] . '%';
        }

        // Lọc theo category_id
        if (!empty($filters['category_id'])) {
            $where[]               = 'p.category_id = :category_id';
            $params[':category_id'] = $filters['category_id'];
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);

        // Sắp xếp
        $orderSQL = match ($sort) {
            'price_asc'  => 'ORDER BY p.price ASC',
            'price_desc' => 'ORDER BY p.price DESC',
            'bestseller' => 'ORDER BY sold_count DESC',
            default      => 'ORDER BY p.created_at DESC',  // newest
        };

        // Đếm tổng (để phân trang)
        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM products p $whereSQL"
        );
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Offset (vị trí bắt đầu lấy)
        $offset = ($page - 1) * $perPage;

        // Query chính — kèm ảnh đại diện và số lượng đã bán
        $sql = "
            SELECT
                p.*,
                pi.image_path AS thumbnail_path,
                COALESCE(sold.qty, 0) AS sold_count
            FROM products p
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

    /**
     * Lấy 1 sản phẩm theo ID, kèm toàn bộ ảnh gallery
     */
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

        // Lấy gallery ảnh
        $imgStmt = $this->db->prepare(
            "SELECT * FROM product_images WHERE product_id = :id ORDER BY is_primary DESC, sort_order ASC"
        );
        $imgStmt->execute([':id' => $id]);
        $product['images'] = $imgStmt->fetchAll();

        return $product;
    }

    /**
     * Lấy sản phẩm theo slug (dùng cho URL thân thiện)
     */
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

    /**
     * Lấy sản phẩm nổi bật (is_featured hoặc bán chạy nhất, tối đa $limit)
     */
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

    // ─────────────────────────────────────────────
    //  VIẾT DỮ LIỆU (WRITE) — dùng cho Admin
    // ─────────────────────────────────────────────

    /**
     * Tạo sản phẩm mới — trả về ID vừa tạo
     */
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

    /**
     * Cập nhật sản phẩm theo ID
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        $allowed = ['name','price','stock','category_id','scale','grade','series','description','weight_gram','is_active'];
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

    /**
     * Xóa mềm (soft delete — chỉ set is_active = 0, không xóa khỏi DB)
     */
    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET is_active = 0 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Cập nhật tồn kho — dùng khi đặt hàng thành công
     * @param int $id    Product ID
     * @param int $delta Số âm để trừ kho, số dương để nhập kho
     */
    public function adjustStock(int $id, int $delta): bool
    {
        $stmt = $this->db->prepare("
            UPDATE products
            SET stock = stock + :delta
            WHERE id = :id AND (stock + :delta2) >= 0
        ");
        return $stmt->execute([':delta' => $delta, ':delta2' => $delta, ':id' => $id]);
    }

    // ─────────────────────────────────────────────
    //  UPLOAD ẢNH
    // ─────────────────────────────────────────────

    /**
     * Lưu ảnh sản phẩm vào bảng product_images và thư mục uploads/
     * Trả về đường dẫn ảnh hoặc false nếu lỗi
     */
    public function uploadImage(int $productId, array $fileData, bool $isPrimary = false): string|false
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($fileData['type'], $allowedTypes)) return false;
        if ($fileData['size'] > 5 * 1024 * 1024) return false; // Giới hạn 5MB

        $ext      = pathinfo($fileData['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . $productId . '_' . uniqid() . '.' . strtolower($ext);
        $dest     = BASE_PATH . '/public/uploads/' . $filename;

        if (!move_uploaded_file($fileData['tmp_name'], $dest)) return false;

        // Nếu đây là ảnh đại diện (is_primary), bỏ flag cũ
        if ($isPrimary) {
            $this->db->prepare("UPDATE product_images SET is_primary = 0 WHERE product_id = :id")
                     ->execute([':id' => $productId]);
        }

        $stmt = $this->db->prepare("
            INSERT INTO product_images (product_id, image_path, is_primary)
            VALUES (:product_id, :image_path, :is_primary)
        ");
        $stmt->execute([
            ':product_id' => $productId,
            ':image_path' => '/public/uploads/' . $filename,
            ':is_primary' => $isPrimary ? 1 : 0,
        ]);

        return '/public/uploads/' . $filename;
    }

    // ─────────────────────────────────────────────
    //  HELPER
    // ─────────────────────────────────────────────

    /**
     * Tạo slug từ tên sản phẩm (tiếng Việt → ASCII)
     * Ví dụ: "HG 1/144 RX-78-2 Gundam" → "hg-1-144-rx-78-2-gundam"
     */
    private function makeSlug(string $name): string
    {
        $slug = mb_strtolower($name, 'UTF-8');
        $slug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $slug);
        $slug = preg_replace('/\s+/', '-', trim($slug));
        $slug = preg_replace('/-+/', '-', $slug);

        // Đảm bảo slug là duy nhất
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
