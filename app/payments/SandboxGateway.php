<?php

declare(strict_types=1);

final class SandboxGateway implements PaymentGateway
{
    public function createCheckout(array $order): string
    {
        $ref = 'sbx_' . unique_token(10);
        Order::setPaymentRef((int) $order['id'], $ref);
        return url('pay/sandbox/' . $order['token']);
    }

    public function handleReturn(): array
    {
        $token = (string) ($_POST['token'] ?? $_GET['token'] ?? '');
        $result = (string) ($_POST['result'] ?? $_GET['result'] ?? '');
        $order = Order::findByToken($token);

        if (!$order) {
            throw new RuntimeException('Order not found.');
        }

        if ($result === 'success') {
            $ref = $order['payment_ref'] ?: 'sbx_' . unique_token(10);
            Order::markPaid((int) $order['id'], $ref);
            return ['ok' => true, 'order' => Order::find((int) $order['id'])];
        }

        Order::markFailed((int) $order['id'], $order['payment_ref']);
        return ['ok' => false, 'order' => Order::find((int) $order['id'])];
    }
}
