<?php

declare(strict_types=1);

final class Auth
{
    public static function check(): bool
    {
        return !empty($_SESSION['admin_id']);
    }

    public static function id(): ?int
    {
        return isset($_SESSION['admin_id']) ? (int) $_SESSION['admin_id'] : null;
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return AdminUser::find((int) $_SESSION['admin_id']);
    }

    public static function attempt(string $username, string $password): bool
    {
        $user = AdminUser::findByUsername($username);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $user['id'];
        return true;
    }

    public static function logout(): void
    {
        unset($_SESSION['admin_id']);
        session_regenerate_id(true);
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            flash('error', 'Please sign in to continue.');
            redirect('admin/login');
        }
    }
}
