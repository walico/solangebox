<?php

declare(strict_types=1);

final class Setting
{
    public static function all(): array
    {
        $rows = db()->query('SELECT key, value FROM settings')->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $out[$row['key']] = $row['value'];
        }
        return $out;
    }

    public static function put(array $values): void
    {
        $stmt = db()->prepare(
            'INSERT INTO settings (key, value) VALUES (:key, :value)
             ON CONFLICT(key) DO UPDATE SET value = excluded.value'
        );
        foreach ($values as $key => $value) {
            $stmt->execute(['key' => $key, 'value' => (string) $value]);
        }
    }
}
