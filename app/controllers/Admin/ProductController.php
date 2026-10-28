<?php

declare(strict_types=1);

final class AdminProductController
{
    public function index(): void
    {
        Auth::requireLogin();
        $q = (string) ($_GET['q'] ?? '');
        $status = (string) ($_GET['status'] ?? '');
        $stock = (string) ($_GET['stock'] ?? '');
        $threshold = low_stock_threshold();
        $filters = ['q' => $q];
        if (in_array($status, ['active', 'hidden'], true)) {
            $filters['status'] = $status;
        } else {
            $status = '';
        }
        if (in_array($stock, ['low', 'out'], true)) {
            $filters['stock'] = $stock;
            $filters['low_threshold'] = $threshold;
        } else {
            $stock = '';
        }

        layout('admin', 'admin/products/index', [
            'title' => 'Products',
            'products' => Product::paginate($filters),
            'q' => $q,
            'status' => $status,
            'stock' => $stock,
            'threshold' => $threshold,
        ]);
    }

    public function create(): void
    {
        Auth::requireLogin();
        layout('admin', 'admin/products/form', [
            'title' => 'Add product',
            'product' => null,
            'categories' => Category::all(),
            'images' => [],
            'variants' => [],
            'specs' => [],
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $data = $this->payload();
        $data['slug'] = Product::uniqueSlug($data['slug'] ?: $data['name']);

        try {
            $id = Product::create($data);
            $this->saveRelated($id);
            flash('success', 'Product added.');
            redirect('admin/products/' . $id . '/edit');
        } catch (Throwable $e) {
            remember_old($_POST);
            flash('error', $e->getMessage());
            redirect('admin/products/new');
        }
    }

    public function edit(string $id): void
    {
        Auth::requireLogin();
        $product = Product::find((int) $id);
        if (!$product) {
            http_response_code(404);
            layout('admin', 'admin/404', ['title' => 'Not found']);
            return;
        }
        layout('admin', 'admin/products/form', [
            'title' => 'Edit product',
            'product' => $product,
            'categories' => Category::all(),
            'images' => Product::images((int) $product['id']),
            'variants' => Product::variants((int) $product['id']),
            'specs' => Product::specs((int) $product['id']),
        ]);
    }

    public function update(string $id): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $product = Product::find((int) $id);
        if (!$product) {
            redirect('admin/products');
        }

        $data = $this->payload();
        $data['slug'] = Product::uniqueSlug($data['slug'] ?: $data['name'], (int) $id);

        try {
            Product::update((int) $id, $data);
            $this->saveRelated((int) $id);
            flash('success', 'Product saved.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('admin/products/' . $id . '/edit');
    }

    public function destroy(string $id): void
    {
        Auth::requireLogin();
        Csrf::verify();
        Product::delete((int) $id);
        flash('success', 'Product deleted.');
        redirect('admin/products');
    }

    public function deleteImage(string $id): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $imageId = (int) ($_POST['image_id'] ?? 0);
        Product::deleteImage($imageId, (int) $id);
        flash('success', 'Image removed.');
        redirect('admin/products/' . $id . '/edit');
    }

    private function payload(): array
    {
        return [
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'name' => trim((string) ($_POST['name'] ?? '')),
            'slug' => trim((string) ($_POST['slug'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'price' => (int) ($_POST['price'] ?? 0),
            'sale_price' => trim((string) ($_POST['sale_price'] ?? '')),
            'stock' => (int) ($_POST['stock'] ?? 0),
            'status' => ($_POST['status'] ?? 'active') === 'hidden' ? 'hidden' : 'active',
        ];
    }

    private function saveRelated(int $id): void
    {
        if (!empty($_FILES['images'])) {
            $paths = Upload::products($_FILES['images']);
            if ($paths) {
                Product::addImages($id, $paths);
            }
        }
        Product::syncVariants($id, $_POST['variants'] ?? []);
        Product::syncSpecs($id, $_POST['specs'] ?? []);
    }
}
