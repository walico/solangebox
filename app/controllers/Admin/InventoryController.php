<?php

declare(strict_types=1);

final class AdminInventoryController
{
    public function index(): void
    {
        Auth::requireLogin();
        $stock = $this->stockFilter((string) ($_GET['stock'] ?? ''));
        $threshold = low_stock_threshold();
        $filters = ['sort' => $stock !== '' ? 'stock' : 'name'];
        if ($stock !== '') {
            $filters['stock'] = $stock;
            $filters['low_threshold'] = $threshold;
        }

        $products = Product::catalog($filters);
        foreach ($products as &$product) {
            $product['variants'] = Product::variants((int) $product['id']);
        }
        unset($product);

        layout('admin', 'admin/inventory/index', [
            'title' => 'Inventory',
            'products' => $products,
            'stock' => $stock,
            'threshold' => $threshold,
            'counts' => [
                'all' => Product::count(),
                'low' => Product::count(['stock' => 'low', 'low_threshold' => $threshold]),
                'out' => Product::count(['stock' => 'out']),
                'watch' => Product::count(['stock' => 'watch', 'low_threshold' => $threshold]),
            ],
        ]);
    }

    public function update(): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $stocks = $_POST['stock'] ?? [];
        $variantStocks = $_POST['variant_stock'] ?? [];
        Product::saveInventory(is_array($stocks) ? $stocks : [], is_array($variantStocks) ? $variantStocks : []);
        flash('success', 'Stock levels saved.');
        $stock = $this->stockFilter((string) ($_POST['return_stock'] ?? ''));
        redirect('admin/inventory' . ($stock !== '' ? '?stock=' . $stock : ''));
    }

    private function stockFilter(string $stock): string
    {
        return in_array($stock, ['low', 'out', 'watch'], true) ? $stock : '';
    }
}
