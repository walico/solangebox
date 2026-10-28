<?php

declare(strict_types=1);

final class AdminCustomerController
{
    public function index(): void
    {
        Auth::requireLogin();
        $q = trim((string) ($_GET['q'] ?? ''));
        layout('admin', 'admin/customers/index', [
            'title' => 'Customers',
            'customers' => Customer::all($q),
            'q' => $q,
            'customer_count' => Customer::count(),
        ]);
    }

    public function show(string $id): void
    {
        Auth::requireLogin();
        $customer = Customer::find((int) $id);
        if (!$customer) {
            http_response_code(404);
            layout('admin', 'admin/404', ['title' => 'Not found']);
            return;
        }

        layout('admin', 'admin/customers/show', [
            'title' => $customer['name'],
            'customer' => $customer,
            'orders' => Customer::orders((int) $customer['id']),
        ]);
    }
}
