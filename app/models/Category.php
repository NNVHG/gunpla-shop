<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Category
{
    private PDO $db;
    public function __construct()
    {
        $this->db = getDB();
    }

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
        foreach ($all as $cat) {
            $byParent[$cat['parent_id'] ?? 'root'][] = $cat;
        }
        $build = function ($pk) use (&$build, &$byParent) {
            $nodes = $byParent[$pk] ?? [];
            foreach ($nodes as &$n) {
                $n['children'] = $build($n['id']);
            }
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

    public function getByType(string $type): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, COUNT(p.id) AS product_count 
            FROM categories c 
            LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1 
            WHERE c.type = :type 
            GROUP BY c.id 
            ORDER BY c.sort_order ASC, c.name ASC
        ");
        $stmt->execute([':type' => $type]);
        return $stmt->fetchAll();
    }

    public function getByGroup(string $group): array
    {
        if ($group === 'gunpla') {
            $types = "('scale', 'grade', 'series')";
        } elseif ($group === 'tools') {
            $types = "('tool', 'accessory', 'chemical', 'combo')";
        } else {
            return $this->getTopLevel();
        }

        $stmt = $this->db->prepare("
            SELECT c.*, COUNT(p.id) AS product_count 
            FROM categories c 
            LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1 
            WHERE c.type IN $types 
            GROUP BY c.id 
            ORDER BY c.sort_order ASC, c.name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function cloneGroupedByType(): array
    {
        return $this->getGroupedByType();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("INSERT INTO categories (name, slug, parent_id, type) VALUES (:name, :slug, :parent_id, :type)");
        return $stmt->execute([
            ':name' => trim($data['name']),
            ':slug' => $this->makeSlug($data['name']),
            ':parent_id' => !empty($data['parent_id']) ? (int)$data['parent_id'] : null,
            ':type' => $data['type']
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE categories SET name = :name, slug = :slug, parent_id = :parent_id, type = :type WHERE id = :id");
        return $stmt->execute([
            ':name' => trim($data['name']),
            ':slug' => $this->makeSlug($data['name']),
            ':parent_id' => !empty($data['parent_id']) ? (int)$data['parent_id'] : null,
            ':type' => $data['type'],
            ':id' => $id
        ]);
    }

    public function delete(int $id): bool
    {
        $this->db->prepare("UPDATE categories SET parent_id = NULL WHERE parent_id = :id")->execute([':id' => $id]);
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    private function makeSlug(string $name): string
    {
        $slug = mb_strtolower($name, 'UTF-8');
        $slug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $slug);
        $slug = preg_replace('/\s+/', '-', trim($slug));
        $slug = preg_replace('/-+/', '-', $slug);
        return $slug;
    }
}
