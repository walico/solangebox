<?php

declare(strict_types=1);

final class Customer
{
    public static function count(): int
    {
        return (int) db()->query('SELECT COUNT(*) FROM customers')->fetchColumn();
    }

    public static function all(string $q = ''): array
    {
        $where = '1=1';
        $params = [];
        if ($q !== '') {
            $where = '(c.name LIKE :q OR IFNULL(c.email, \'\') LIKE :q OR IFNULL(c.phone, \'\') LIKE :q OR IFNULL(c.city, \'\') LIKE :q)';
            $params['q'] = '%' . $q . '%';
        }

        $sql = 'SELECT c.*,
                       (SELECT COUNT(*) FROM orders o WHERE o.customer_id = c.id) AS order_count,
                       (SELECT COALESCE(SUM(o.total), 0) FROM orders o WHERE o.customer_id = c.id AND o.payment_status = \'paid\') AS spent,
                       (SELECT MAX(o.created_at) FROM orders o WHERE o.customer_id = c.id) AS last_order_at
                FROM customers c
                WHERE ' . $where . '
                ORDER BY c.created_at DESC, c.id DESC';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM customers WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function orders(int $id): array
    {
        $stmt = db()->prepare(
            'SELECT * FROM orders WHERE customer_id = :id ORDER BY created_at DESC, id DESC'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }
}
