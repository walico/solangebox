<?php

declare(strict_types=1);

final class StoreController
{
    public function home(): void
    {
        $products = Product::paginate(['status' => 'active', 'sort' => 'newest']);
        $roots = [];
        foreach (Category::all() as $category) {
            $roots[(int) $category['id']] = $category;
        }
        foreach ($products as &$product) {
            $current = $roots[(int) $product['category_id']] ?? null;
            while ($current && !empty($current['parent_id']) && isset($roots[(int) $current['parent_id']])) {
                $current = $roots[(int) $current['parent_id']];
            }
            $product['filter'] = $current['slug'] ?? $product['category_slug'];
        }
        unset($product);

        layout('store', 'store/home', [
            'title' => setting('shop_name', 'Solange Box'),
            'hero' => true,
            'categories' => Category::roots(),
            'products' => $products,
            'feature' => $products[0] ?? null,
            'spotlight' => $products[1] ?? null,
        ]);
    }

    public function about(): void
    {
        layout('store', 'store/about', [
            'title' => 'About',
        ]);
    }

    public function contact(): void
    {
        layout('store', 'store/contact', [
            'title' => 'Contact',
        ]);
    }

    public function search(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $products = $q !== ''
            ? Product::paginate(['status' => 'active', 'q' => $q, 'sort' => $_GET['sort'] ?? 'newest'])
            : [];

        layout('store', 'store/search', [
            'title' => $q !== '' ? 'Search: ' . $q : 'Search',
            'q' => $q,
            'products' => $products,
            'sort' => $_GET['sort'] ?? 'newest',
        ]);
    }

    public function category(string $slug): void
    {
        $category = Category::findBySlug($slug);
        if (!$category) {
            http_response_code(404);
            layout('store', 'store/404', ['title' => 'Category not found']);
            return;
        }

        $sort = (string) ($_GET['sort'] ?? 'newest');
        $inStock = !empty($_GET['in_stock']);
        $products = Product::paginate([
            'status' => 'active',
            'category_ids' => Category::descendantIds((int) $category['id']),
            'sort' => $sort,
            'in_stock' => $inStock,
        ]);

        layout('store', 'store/category', [
            'title' => $category['name'],
            'category' => $category,
            'products' => $products,
            'sort' => $sort,
            'in_stock' => $inStock,
        ]);
    }

    public function product(string $slug): void
    {
        $product = Product::findBySlug($slug);
        if (!$product || $product['status'] !== 'active') {
            http_response_code(404);
            layout('store', 'store/404', ['title' => 'Product not found']);
            return;
        }

        layout('store', 'store/product', [
            'title' => $product['name'],
            'product' => $product,
            'images' => Product::images((int) $product['id']),
            'variants' => Product::variants((int) $product['id']),
            'specs' => Product::specs((int) $product['id']),
        ]);
    }
}
