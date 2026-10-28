<?php

declare(strict_types=1);

final class AdminDashboardController
{
    public function index(): void
    {
        Auth::requireLogin();
        $threshold = low_stock_threshold();
        $low = Product::needingStock($threshold, 1);
        $spotlight = $low ? Product::find((int) $low[0]['id']) : null;
        if (!$spotlight) {
            $latest = Product::latest(1);
            $spotlight = $latest[0] ?? null;
        }
        if ($spotlight && empty($spotlight['images'])) {
            $spotlight['images'] = Product::images((int) $spotlight['id']);
        }
        layout('admin', 'admin/dashboard', [
            'title' => 'Dashboard',
            'counts' => Order::counts(),
            'revenue' => Order::revenue(),
            'failed_payments' => Order::countPayment('failed'),
            'customer_count' => Customer::count(),
            'active_count' => Product::count(['status' => 'active']),
            'hidden_count' => Product::count(['status' => 'hidden']),
            'watch_count' => Product::count(['stock' => 'watch', 'low_threshold' => $threshold]),
            'category_count' => count(Category::all()),
            'recent_orders' => Order::all(['limit' => 5]),
            'spotlight' => $spotlight,
        ]);
    }
}
