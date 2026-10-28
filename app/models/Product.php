<?php

declare(strict_types=1);

final class Product
{
    public static function find(int $id): ?array
    {
        $stmt = db()->prepare(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p
             JOIN categories c ON c.id = p.category_id
             WHERE p.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = db()->prepare(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p
             JOIN categories c ON c.id = p.category_id
             WHERE p.slug = :slug'
        );
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function paginate(array $filters = []): array
    {
        $rows = self::catalog($filters);
        foreach ($rows as &$row) {
            $row['images'] = self::images((int) $row['id']);
        }
        unset($row);

        return $rows;
    }

    public static function catalog(array $filters = []): array
    {
        [$where, $params, $order] = self::constraints($filters);
        $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                       COALESCE(p.sale_price, p.price) AS effective_price
                FROM products p
                JOIN categories c ON c.id = p.category_id
                WHERE ' . $where . '
                ORDER BY ' . $order;

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function count(array $filters = []): int
    {
        [$where, $params] = self::constraints($filters);
        $sql = 'SELECT COUNT(*)
                FROM products p
                JOIN categories c ON c.id = p.category_id
                WHERE ' . $where;
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public static function needingStock(int $threshold, int $limit = 8): array
    {
        $limit = max(1, min(50, $limit));
        $stmt = db()->prepare(
            'SELECT p.id, p.name, p.stock, p.slug, c.name AS category_name
             FROM products p
             JOIN categories c ON c.id = p.category_id
             WHERE p.stock <= :threshold
             ORDER BY p.stock ASC, p.name ASC
             LIMIT ' . $limit
        );
        $stmt->execute(['threshold' => $threshold]);
        return $stmt->fetchAll();
    }

    /**
     * @param array<int|string, mixed> $stocks
     * @param array<int|string, mixed> $variantStocks
     */
    public static function saveInventory(array $stocks, array $variantStocks): void
    {
        $pdo = db();
        $pdo->beginTransaction();
        try {
            $variantStmt = $pdo->prepare('UPDATE product_variants SET stock = :stock WHERE id = :id');
            $touched = [];
            foreach ($variantStocks as $id => $stock) {
                $variant = self::findVariant((int) $id);
                if (!$variant) {
                    continue;
                }
                $variantStmt->execute([
                    'id' => (int) $id,
                    'stock' => max(0, (int) $stock),
                ]);
                $touched[(int) $variant['product_id']] = true;
            }

            $sumStmt = $pdo->prepare('SELECT COALESCE(SUM(stock), 0) FROM product_variants WHERE product_id = :id');
            $productStmt = $pdo->prepare('UPDATE products SET stock = :stock WHERE id = :id');
            foreach (array_keys($touched) as $productId) {
                $sumStmt->execute(['id' => $productId]);
                $productStmt->execute([
                    'id' => $productId,
                    'stock' => (int) $sumStmt->fetchColumn(),
                ]);
                unset($stocks[$productId], $stocks[(string) $productId]);
            }

            foreach ($stocks as $id => $stock) {
                if (!self::find((int) $id) || self::variants((int) $id)) {
                    continue;
                }
                $productStmt->execute([
                    'id' => (int) $id,
                    'stock' => max(0, (int) $stock),
                ]);
            }

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /** @return array{0:string,1:array<string,mixed>,2:string} */
    private static function constraints(array $filters): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'p.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['category_ids']) && is_array($filters['category_ids'])) {
            $placeholders = [];
            foreach (array_values($filters['category_ids']) as $i => $id) {
                $key = 'cid' . $i;
                $placeholders[] = ':' . $key;
                $params[$key] = (int) $id;
            }
            $where[] = 'p.category_id IN (' . implode(',', $placeholders) . ')';
        }
        if (!empty($filters['q'])) {
            $where[] = '(p.name LIKE :q OR p.description LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['in_stock'])) {
            $where[] = 'p.stock > 0';
        }
        $stock = (string) ($filters['stock'] ?? '');
        if ($stock === 'out') {
            $where[] = 'p.stock <= 0';
        } elseif ($stock === 'low') {
            $where[] = 'p.stock > 0 AND p.stock <= :low_max';
            $params['low_max'] = (int) ($filters['low_threshold'] ?? 5);
        } elseif ($stock === 'watch') {
            $where[] = 'p.stock <= :low_max';
            $params['low_max'] = (int) ($filters['low_threshold'] ?? 5);
        }

        $sort = $filters['sort'] ?? 'newest';
        if ($sort === 'price_asc') {
            $order = 'effective_price ASC, p.name ASC';
        } elseif ($sort === 'price_desc') {
            $order = 'effective_price DESC, p.name ASC';
        } elseif ($sort === 'name') {
            $order = 'p.name ASC';
        } elseif ($sort === 'stock') {
            $order = 'p.stock ASC, p.name ASC';
        } else {
            $order = 'p.created_at DESC, p.id DESC';
        }

        return [implode(' AND ', $where), $params, $order];
    }

    public static function latest(int $limit = 8): array
    {
        $limit = max(1, $limit);
        $stmt = db()->prepare(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p
             JOIN categories c ON c.id = p.category_id
             WHERE p.status = :status
             ORDER BY p.created_at DESC, p.id DESC
             LIMIT ' . $limit
        );
        $stmt->execute(['status' => 'active']);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            $row['images'] = self::images((int) $row['id']);
        }
        unset($row);

        return $rows;
    }

    public static function images(int $productId): array
    {
        $stmt = db()->prepare(
            'SELECT * FROM product_images WHERE product_id = :id ORDER BY sort_order, id'
        );
        $stmt->execute(['id' => $productId]);
        return $stmt->fetchAll();
    }

    public static function variants(int $productId): array
    {
        $stmt = db()->prepare(
            'SELECT * FROM product_variants WHERE product_id = :id ORDER BY id'
        );
        $stmt->execute(['id' => $productId]);
        return $stmt->fetchAll();
    }

    public static function specs(int $productId): array
    {
        $stmt = db()->prepare(
            'SELECT * FROM product_specs WHERE product_id = :id ORDER BY sort_order, id'
        );
        $stmt->execute(['id' => $productId]);
        return $stmt->fetchAll();
    }

    public static function findVariant(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM product_variants WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = slugify($slug);
        $try = $base;
        $n = 2;
        while (true) {
            $sql = 'SELECT id FROM products WHERE slug = :slug';
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

    public static function create(array $data): int
    {
        $stmt = db()->prepare(
            'INSERT INTO products (category_id, name, slug, description, price, sale_price, stock, status)
             VALUES (:category_id, :name, :slug, :description, :price, :sale_price, :stock, :status)'
        );
        $stmt->execute([
            'category_id' => (int) $data['category_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? '',
            'price' => (int) $data['price'],
            'sale_price' => $data['sale_price'] !== '' && $data['sale_price'] !== null ? (int) $data['sale_price'] : null,
            'stock' => (int) ($data['stock'] ?? 0),
            'status' => $data['status'] ?? 'active',
        ]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = db()->prepare(
            'UPDATE products
             SET category_id = :category_id, name = :name, slug = :slug, description = :description,
                 price = :price, sale_price = :sale_price, stock = :stock, status = :status
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'category_id' => (int) $data['category_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? '',
            'price' => (int) $data['price'],
            'sale_price' => $data['sale_price'] !== '' && $data['sale_price'] !== null ? (int) $data['sale_price'] : null,
            'stock' => (int) ($data['stock'] ?? 0),
            'status' => $data['status'] ?? 'active',
        ]);
    }

    public static function addImages(int $productId, array $paths): void
    {
        $stmt = db()->prepare(
            'INSERT INTO product_images (product_id, path, sort_order) VALUES (:product_id, :path, :sort_order)'
        );
        $max = db()->prepare('SELECT COALESCE(MAX(sort_order), -1) FROM product_images WHERE product_id = :id');
        $max->execute(['id' => $productId]);
        $order = (int) $max->fetchColumn() + 1;
        foreach ($paths as $path) {
            $stmt->execute([
                'product_id' => $productId,
                'path' => $path,
                'sort_order' => $order++,
            ]);
        }
    }

    public static function deleteImage(int $imageId, int $productId): void
    {
        $stmt = db()->prepare('SELECT path FROM product_images WHERE id = :id AND product_id = :pid');
        $stmt->execute(['id' => $imageId, 'pid' => $productId]);
        $row = $stmt->fetch();
        if (!$row) {
            return;
        }
        $file = PUBLIC_PATH . '/' . ltrim($row['path'], '/');
        if (is_file($file) && !str_ends_with($row['path'], '.svg')) {
            @unlink($file);
        }
        db()->prepare('DELETE FROM product_images WHERE id = :id')->execute(['id' => $imageId]);
    }

    public static function syncVariants(int $productId, array $variants): void
    {
        db()->prepare('DELETE FROM product_variants WHERE product_id = :id')->execute(['id' => $productId]);
        $stmt = db()->prepare(
            'INSERT INTO product_variants (product_id, size, color, extra_price, stock)
             VALUES (:product_id, :size, :color, :extra_price, :stock)'
        );
        foreach ($variants as $variant) {
            $size = trim((string) ($variant['size'] ?? ''));
            $color = trim((string) ($variant['color'] ?? ''));
            if ($size === '' && $color === '') {
                continue;
            }
            $stmt->execute([
                'product_id' => $productId,
                'size' => $size !== '' ? $size : null,
                'color' => $color !== '' ? $color : null,
                'extra_price' => (int) ($variant['extra_price'] ?? 0),
                'stock' => (int) ($variant['stock'] ?? 0),
            ]);
        }
    }

    public static function syncSpecs(int $productId, array $specs): void
    {
        db()->prepare('DELETE FROM product_specs WHERE product_id = :id')->execute(['id' => $productId]);
        $stmt = db()->prepare(
            'INSERT INTO product_specs (product_id, label, value, sort_order)
             VALUES (:product_id, :label, :value, :sort_order)'
        );
        $order = 0;
        foreach ($specs as $spec) {
            $label = trim((string) ($spec['label'] ?? ''));
            $value = trim((string) ($spec['value'] ?? ''));
            if ($label === '' || $value === '') {
                continue;
            }
            $stmt->execute([
                'product_id' => $productId,
                'label' => $label,
                'value' => $value,
                'sort_order' => $order++,
            ]);
        }
    }

    public static function delete(int $id): void
    {
        foreach (self::images($id) as $image) {
            $file = PUBLIC_PATH . '/' . ltrim($image['path'], '/');
            if (is_file($file) && !str_ends_with($image['path'], '.svg')) {
                @unlink($file);
            }
        }
        db()->prepare('DELETE FROM products WHERE id = :id')->execute(['id' => $id]);
    }
}
?>
