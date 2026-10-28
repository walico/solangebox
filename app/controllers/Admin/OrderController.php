<?php

declare(strict_types=1);

final class AdminOrderController
{
    public function index(): void
    {
        Auth::requireLogin();
        $status = (string) ($_GET['status'] ?? '');
        $payment = (string) ($_GET['payment'] ?? '');
        $q = trim((string) ($_GET['q'] ?? ''));
        $allowedStatus = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
        $allowedPayment = ['pending', 'paid', 'failed'];
        if (!in_array($status, $allowedStatus, true)) {
            $status = '';
        }
        if (!in_array($payment, $allowedPayment, true)) {
            $payment = '';
        }
        $filters = [];
        if ($status !== '') {
            $filters['status'] = $status;
        }
        if ($payment !== '') {
            $filters['payment'] = $payment;
        }
        if ($q !== '') {
            $filters['q'] = $q;
        }
        layout('admin', 'admin/orders/index', [
            'title' => 'Orders',
            'orders' => Order::all($filters),
            'status' => $status,
            'payment' => $payment,
            'q' => $q,
            'counts' => Order::counts(),
        ]);
    }

    public function show(string $id): void
    {
        Auth::requireLogin();
        $order = Order::find((int) $id);
        if (!$order) {
            http_response_code(404);
            layout('admin', 'admin/404', ['title' => 'Not found']);
            return;
        }
        layout('admin', 'admin/orders/show', [
            'title' => 'Order #' . $order['id'],
            'order' => $order,
            'items' => Order::items((int) $order['id']),
        ]);
    }

    public function updateStatus(string $id): void
    {
        Auth::requireLogin();
        Csrf::verify();
        try {
            Order::setStatus((int) $id, (string) ($_POST['status'] ?? ''));
            flash('success', 'Order status updated.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('admin/orders/' . $id);
    }

    public function updateContact(string $id): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $order = Order::find((int) $id);
        if (!$order) {
            redirect('admin/orders');
        }

        $data = [
            'name' => trim((string) ($_POST['customer_name'] ?? '')),
            'email' => trim((string) ($_POST['customer_email'] ?? '')),
            'phone' => trim((string) ($_POST['customer_phone'] ?? '')),
            'address' => trim((string) ($_POST['customer_address'] ?? '')),
            'city' => trim((string) ($_POST['customer_city'] ?? '')),
            'notes' => trim((string) ($_POST['notes'] ?? '')),
        ];
        if ($data['name'] === '' || $data['phone'] === '' || $data['address'] === '') {
            flash('error', 'Name, phone, and delivery address are required.');
            redirect('admin/orders/' . $id);
        }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please enter a valid email address.');
            redirect('admin/orders/' . $id);
        }

        Order::updateContact((int) $id, $data);
        flash('success', 'Delivery details saved.');
        redirect('admin/orders/' . $id);
    }
}
