<?php

declare(strict_types=1);

final class Category
{
    public static function all(): array
    {
        return db()->query(
            'SELECT c.*, p.name AS parent_name
             FROM categories c
             LEFT JOIN categories p ON p.id = c.parent_id
             ORDER BY COALESCE(c.parent_id, c.id), c.parent_id IS NOT NULL, c.sort_order, c.name'
        )->fetchAll();
    }

    public static function tree(): array
    {
        $all = self::all();
        $byId = [];
        foreach ($all as $row) {
            $row['children'] = [];
            $byId[(int) $row['id']] = $row;
        }
        $roots = [];
        foreach ($byId as $id => $row) {
            if (!empty($row['parent_id']) && isset($byId[(int) $row['parent_id']])) {
                $byId[(int) $row['parent_id']]['children'][] = &$byId[$id];
            } else {
                $roots[] = &$byId[$id];
            }
        }
        return $roots;
    }

    public static function roots(): array
    {
        $stmt = db()->query(
            'SELECT * FROM categories WHERE parent_id IS NULL ORDER BY sort_order, name'
        );
        return $stmt->fetchAll();
    }

    public static function rootsWithCovers(): array
    {
        $roots = self::roots();
        foreach ($roots as &$category) {
            $ids = self::descendantIds((int) $category['id']);
            $placeholders = [];
            $params = ['status' => 'active'];
            foreach ($ids as $i => $id) {
                $key = 'id' . $i;
                $placeholders[] = ':' . $key;
                $params[$key] = $id;
            }
            $stmt = db()->prepare(
                'SELECT pi.path
                 FROM products p
                 JOIN product_images pi ON pi.product_id = p.id
                 WHERE p.status = :status AND p.category_id IN (' . implode(',', $placeholders) . ')
                 ORDER BY p.id ASC, pi.sort_order ASC
                 LIMIT 1'
            );
            $stmt->execute($params);
            $category['image'] = $stmt->fetchColumn() ?: null;
        }
        unset($category);

        return $roots;
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = db()->prepare('SELECT * FROM categories WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function descendantIds(int $id): array
    {
        $ids = [$id];
        $stmt = db()->prepare('SELECT id FROM categories WHERE parent_id = :id');
        $stmt->execute(['id' => $id]);
        foreach ($stmt->fetchAll() as $child) {
            $ids = array_merge($ids, self::descendantIds((int) $child['id']));
        }
        return $ids;
    }

    public static function create(array $data): int
    {
        $stmt = db()->prepare(
            'INSERT INTO categories (name, slug, parent_id, sort_order)
             VALUES (:name, :slug, :parent_id, :sort_order)'
        );
        $stmt->execute([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'parent_id' => $data['parent_id'] ?: null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = db()->prepare(
            'UPDATE categories
             SET name = :name, slug = :slug, parent_id = :parent_id, sort_order = :sort_order
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'slug' => $data['slug'],
            'parent_id' => $data['parent_id'] ?: null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);
    }

    public static function delete(int $id): void
    {
        $count = db()->prepare('SELECT COUNT(*) FROM products WHERE category_id = :id');
        $count->execute(['id' => $id]);
        if ((int) $count->fetchColumn() > 0) {
            throw new RuntimeException('This category still has products. Move or delete them first.');
        }
        $stmt = db()->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = slugify($slug);
        $try = $base;
        $n = 2;
        while (true) {
            $sql = 'SELECT id FROM categories WHERE slug = :slug';
            $params = ['slug' => $try];
            if ($ignoreId) {
                $sql .= ' AND id != :id';
                $params['id'] = $ignoreId;
            }
            $stmt = db()->prepare($sql);
            $stmt->execute($params);
            if (!$stmt->fetch()) {
                return $try;
            }
            $try = $base . '-' . $n;
            $n++;
        }
    }
}
