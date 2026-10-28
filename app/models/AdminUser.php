<?php

declare(strict_types=1);

final class AdminUser
{
    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM admin_users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByUsername(string $username): ?array
    {
        $stmt = db()->prepare('SELECT * FROM admin_users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function updatePassword(int $id, string $hash): void
    {
        db()->prepare('UPDATE admin_users SET password_hash = :hash WHERE id = :id')
            ->execute(['id' => $id, 'hash' => $hash]);
    }
}
