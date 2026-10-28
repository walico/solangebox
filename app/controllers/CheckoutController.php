<?php

declare(strict_types=1);

final class CheckoutController
{
    public function show(): void
    {
        if (Cart::isEmpty()) {
            flash('error', 'Your cart is empty.');
            redirect('cart');
        }

        layout('store', 'store/checkout', [
            'title' => 'Checkout',
            'items' => Cart::items(),
            'total' => Cart::total(),
        ]);
    }

    public function place(): void
    {
        Csrf::verify();

        if (Cart::isEmpty()) {
            flash('error', 'Your cart is empty.');
            redirect('cart');
        }

        $customer = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'address' => trim((string) ($_POST['address'] ?? '')),
            'city' => trim((string) ($_POST['city'] ?? '')),
            'notes' => trim((string) ($_POST['notes'] ?? '')),
        ];

        $errors = [];
        if ($customer['name'] === '') {
            $errors[] = 'Name is required.';
        }
        if ($customer['phone'] === '') {
            $errors[] = 'Phone number is required.';
        }
        if ($customer['address'] === '') {
            $errors[] = 'Delivery address is required.';
        }
        if ($customer['email'] !== '' && !filter_var($customer['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if ($errors) {
            remember_old($customer);
            flash('error', implode(' ', $errors));
            redirect('checkout');
        }

        $items = Cart::items();
        $total = Cart::total();
        $order = Order::createFromCart($customer, $items, $total);
        Cart::clear();
        clear_old();

        $payUrl = payment_gateway()->createCheckout($order);
        header('Location: ' . $payUrl, true, 302);
        exit;
    }

    public function sandbox(string $token): void
    {
        $order = Order::findByToken($token);
        if (!$order) {
            http_response_code(404);
            layout('store', 'store/404', ['title' => 'Payment not found']);
            return;
        }

        layout('store', 'payments/sandbox', [
            'title' => 'Pay for order',
            'order' => $order,
            'items' => Order::items((int) $order['id']),
        ]);
    }

    public function sandboxReturn(): void
    {
        Csrf::verify();
        $result = payment_gateway()->handleReturn();
        $order = $result['order'];
        if ($result['ok']) {
            flash('success', 'Payment received. Thank you.');
        } else {
            flash('error', 'Payment was not completed. You can try again from this page.');
        }
        redirect('order/' . $order['token']);
    }

    public function confirmation(string $token): void
    {
        $order = Order::findByToken($token);
        if (!$order) {
            http_response_code(404);
            layout('store', 'store/404', ['title' => 'Order not found']);
            return;
        }

        layout('store', 'store/order', [
            'title' => 'Order ' . $order['token'],
            'order' => $order,
            'items' => Order::items((int) $order['id']),
        ]);
    }
}
