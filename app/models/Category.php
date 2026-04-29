<?php
declare(strict_types=1);

class Category
{
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function getAll(): array
    {
        return $this->db->query("SELECT c.*, p.name AS parent_name FROM categories c LEFT JOIN categories p ON p.id = c.parent_id ORDER BY c.sort_order ASC, c.id ASC")->fetchAll();
    }

    public function getTopLevel(): array
    {
        return $this->db->query("SELECT c.*, COUNT(p.id) AS product_count FROM categories c LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1 WHERE c.parent_id IS NULL GROUP BY c.id ORDER BY c.sort_order ASC")->fetchAll();
    }

    public function getChildren(int $parentId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE parent_id = :pid ORDER BY sort_order ASC");
        $stmt->execute([':pid' => $parentId]);
        return $stmt->fetchAll();
    }

    public function getTree(): array
    {
        $all = $this->getAll();
        $byParent = [];
        foreach ($all as $cat) { $byParent[$cat['parent_id'] ?? 'root'][] = $cat; }
        $build = function($pk) use (&$build, &$byParent) {
            $nodes = $byParent[$pk] ?? [];
            foreach ($nodes as &$n) { $n['children'] = $build($n['id']); }
            return $nodes;
        };
        return $build('root');
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findBySlug(string $slug): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = :slug");
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch();
    }

    public function getGroupedByType(): array
    {
        $all = $this->getAll();
        $grouped = [];
        foreach ($all as $cat) {
            $grouped[$cat['type']][] = $cat;
        }
        return $grouped;
    }
}
