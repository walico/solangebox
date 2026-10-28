<?php

declare(strict_types=1);

final class Order
{
    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM orders WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByToken(string $token): ?array
    {
        $stmt = db()->prepare('SELECT * FROM orders WHERE token = :token');
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByPaymentRef(string $ref): ?array
    {
        $stmt = db()->prepare('SELECT * FROM orders WHERE payment_ref = :ref');
        $stmt->execute(['ref' => $ref]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function items(int $orderId): array
    {
        $stmt = db()->prepare('SELECT * FROM order_items WHERE order_id = :id ORDER BY id');
        $stmt->execute(['id' => $orderId]);
        return $stmt->fetchAll();
    }

    public static function all(array $filters = []): array
    {
        $where = ['1=1'];
        $params = [];
        if (!empty($filters['status'])) {
            $where[] = 'status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['payment'])) {
            $where[] = 'payment_status = :payment';
            $params['payment'] = $filters['payment'];
        }
        if (!empty($filters['q'])) {
            $where[] = '(customer_name LIKE :q OR customer_phone LIKE :q OR IFNULL(customer_email, \'\') LIKE :q OR CAST(id AS TEXT) LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }
        $limitSql = '';
        if (!empty($filters['limit'])) {
            $limitSql = ' LIMIT ' . max(1, min(100, (int) $filters['limit']));
        }
        $sql = 'SELECT * FROM orders WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC, id DESC' . $limitSql;
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function revenue(): int
    {
        return (int) db()->query(
            "SELECT COALESCE(SUM(total), 0) FROM orders WHERE payment_status = 'paid'"
        )->fetchColumn();
    }

    public static function countPayment(string $status): int
    {
        $stmt = db()->prepare('SELECT COUNT(*) FROM orders WHERE payment_status = :status');
        $stmt->execute(['status' => $status]);
        return (int) $stmt->fetchColumn();
    }

    public static function counts(): array
    {
        $rows = db()->query(
            "SELECT status, COUNT(*) AS n FROM orders GROUP BY status"
        )->fetchAll();
        $out = ['pending' => 0, 'paid' => 0, 'shipped' => 0, 'delivered' => 0, 'cancelled' => 0, 'all' => 0];
        foreach ($rows as $row) {
            $out[$row['status']] = (int) $row['n'];
            $out['all'] += (int) $row['n'];
        }
        return $out;
    }

    public static function createFromCart(array $customer, array $cartItems, int $total): array
    {
        $pdo = db();
        $pdo->beginTransaction();

        $custStmt = $pdo->prepare(
            'INSERT INTO customers (name, email, phone, address, city)
             VALUES (:name, :email, :phone, :address, :city)'
        );
        $custStmt->execute([
            'name' => $customer['name'],
            'email' => $customer['email'] ?: null,
            'phone' => $customer['phone'],
            'address' => $customer['address'],
            'city' => $customer['city'] ?: null,
        ]);
        $customerId = (int) $pdo->lastInsertId();

        $token = unique_token(16);
        $orderStmt = $pdo->prepare(
            'INSERT INTO orders (
                customer_id, token, status, payment_provider, payment_status,
                subtotal, total, customer_name, customer_email, customer_phone,
                customer_address, customer_city, notes
             ) VALUES (
                :customer_id, :token, :status, :payment_provider, :payment_status,
                :subtotal, :total, :customer_name, :customer_email, :customer_phone,
                :customer_address, :customer_city, :notes
             )'
        );
        $orderStmt->execute([
            'customer_id' => $customerId,
            'token' => $token,
            'status' => 'pending',
            'payment_provider' => config('payment.provider', 'sandbox'),
            'payment_status' => 'pending',
            'subtotal' => $total,
            'total' => $total,
            'customer_name' => $customer['name'],
            'customer_email' => $customer['email'] ?: null,
            'customer_phone' => $customer['phone'],
            'customer_address' => $customer['address'],
            'customer_city' => $customer['city'] ?: null,
            'notes' => $customer['notes'] ?: null,
        ]);
        $orderId = (int) $pdo->lastInsertId();

        $itemStmt = $pdo->prepare(
            'INSERT INTO order_items (order_id, product_id, variant_id, name, variant_label, unit_price, qty, line_total)
             VALUES (:order_id, :product_id, :variant_id, :name, :variant_label, :unit_price, :qty, :line_total)'
        );
        foreach ($cartItems as $item) {
            $itemStmt->execute([
                'order_id' => $orderId,
                'product_id' => $item['product']['id'],
                'variant_id' => $item['variant']['id'] ?? null,
                'name' => $item['product']['name'],
                'variant_label' => $item['label'] ?: null,
                'unit_price' => $item['unit_price'],
                'qty' => $item['qty'],
                'line_total' => $item['line_total'],
            ]);
        }

        $pdo->commit();
        $order = self::find($orderId);
        return $order ?: throw new RuntimeException('Order could not be created.');
    }

    public static function markPaid(int $id, string $ref): void
    {
        $stmt = db()->prepare(
            "UPDATE orders
             SET payment_status = 'paid', status = 'paid', payment_ref = :ref
             WHERE id = :id AND payment_status != 'paid'"
        );
        $stmt->execute(['id' => $id, 'ref' => $ref]);
    }

    public static function markFailed(int $id, ?string $ref = null): void
    {
        $stmt = db()->prepare(
            "UPDATE orders
             SET payment_status = 'failed', payment_ref = COALESCE(:ref, payment_ref)
             WHERE id = :id AND payment_status = 'pending'"
        );
        $stmt->execute(['id' => $id, 'ref' => $ref]);
    }

    public static function setStatus(int $id, string $status): void
    {
        $allowed = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            throw new InvalidArgumentException('Invalid order status.');
        }
        $stmt = db()->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $stmt->execute(['id' => $id, 'status' => $status]);
        if ($status === 'paid') {
            db()->prepare("UPDATE orders SET payment_status = 'paid' WHERE id = :id")
                ->execute(['id' => $id]);
        }
        if ($status === 'cancelled') {
            db()->prepare("UPDATE orders SET payment_status = 'failed' WHERE id = :id AND payment_status = 'pending'")
                ->execute(['id' => $id]);
        }
    }

    public static function setPaymentRef(int $id, string $ref): void
    {
        db()->prepare('UPDATE orders SET payment_ref = :ref WHERE id = :id')
            ->execute(['id' => $id, 'ref' => $ref]);
    }

    public static function updateContact(int $id, array $data): void
    {
        $order = self::find($id);
        if (!$order) {
            throw new RuntimeException('Order not found.');
        }

        $email = trim((string) ($data['email'] ?? ''));
        $city = trim((string) ($data['city'] ?? ''));
        $notes = trim((string) ($data['notes'] ?? ''));
        $payload = [
            'id' => $id,
            'name' => trim((string) ($data['name'] ?? '')),
            'email' => $email !== '' ? $email : null,
            'phone' => trim((string) ($data['phone'] ?? '')),
            'address' => trim((string) ($data['address'] ?? '')),
            'city' => $city !== '' ? $city : null,
            'notes' => $notes !== '' ? $notes : null,
        ];

        db()->prepare(
            'UPDATE orders
             SET customer_name = :name, customer_email = :email, customer_phone = :phone,
                 customer_address = :address, customer_city = :city, notes = :notes
             WHERE id = :id'
        )->execute($payload);

        if (!empty($order['customer_id'])) {
            db()->prepare(
                'UPDATE customers
                 SET name = :name, email = :email, phone = :phone, address = :address, city = :city
                 WHERE id = :customer_id'
            )->execute([
                'customer_id' => (int) $order['customer_id'],
                'name' => $payload['name'],
                'email' => $payload['email'],
                'phone' => $payload['phone'],
                'address' => $payload['address'],
                'city' => $payload['city'],
            ]);
        }
    }
}
