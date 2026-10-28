<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    $path = trim($path);
    if ($path === '' || $path === '/') {
        return BASE_PATH === '' ? '/' : BASE_PATH . '/';
    }
    return rtrim(BASE_PATH, '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return rtrim(BASE_PATH, '/') . '/assets/' . ltrim($path, '/');
}

function uploaded(string $path): string
{
    return rtrim(BASE_PATH, '/') . '/' . ltrim($path, '/');
}

function redirect(string $path, int $code = 302): never
{
    header('Location: ' . url($path), true, $code);
    exit;
}

function money(int|float|string|null $amount): string
{
    $value = (int) $amount;
    $symbol = setting('currency_symbol', config('currency_symbol', 'MK'));
    return $symbol . ' ' . number_format($value, 0, '.', ',');
}

function config(string $key, mixed $default = null): mixed
{
    $config = $GLOBALS['app_config'] ?? [];
    $parts = explode('.', $key);
    $value = $config;
    foreach ($parts as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

function setting(string $key, mixed $default = null): mixed
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (db()->query('SELECT key, value FROM settings')->fetchAll() as $row) {
            $cache[$row['key']] = $row['value'];
        }
    }
    return $cache[$key] ?? $default;
}

function setting_flush(): void
{
    // Force next setting() call to reload
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return $message;
    }
    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $value;
}

function old(string $key, mixed $default = ''): mixed
{
    $value = $_SESSION['_old'][$key] ?? $default;
    return $value;
}

function remember_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/i', '-', $value) ?? '';
    return trim($value, '-') ?: 'item';
}

function current_path(): string
{
    return $GLOBALS['request_path'] ?? '/';
}

function is_active(string $path): bool
{
    $current = current_path();
    $path = '/' . ltrim($path, '/');
    if ($path === '/') {
        return $current === '/';
    }
    return $current === $path || str_starts_with($current, rtrim($path, '/') . '/');
}

function view(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $file = ROOT_PATH . '/app/views/' . $template . '.php';
    if (!is_file($file)) {
        throw new RuntimeException('View not found: ' . $template);
    }
    require $file;
}

function layout(string $layoutName, string $template, array $data = []): void
{
    $data['content_template'] = $template;
    view('layouts/' . $layoutName, $data);
}

function request_method(): string
{
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    $override = strtoupper((string) ($_POST['_method'] ?? ''));
    if ($method === 'POST' && in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
        return $override;
    }
    return $method;
}

function unique_token(int $bytes = 16): string
{
    return bin2hex(random_bytes($bytes));
}

function product_price(array $product, ?array $variant = null): int
{
    $base = !empty($product['sale_price']) ? (int) $product['sale_price'] : (int) $product['price'];
    $extra = $variant ? (int) ($variant['extra_price'] ?? 0) : 0;
    return $base + $extra;
}

function low_stock_threshold(): int
{
    $value = (int) setting('low_stock_threshold', '5');
    return max(0, min(999, $value));
}

function stock_class(int $stock): string
{
    if ($stock <= 0) {
        return 'stock-out';
    }
    if ($stock <= low_stock_threshold()) {
        return 'stock-low';
    }
    return 'stock-ok';
}

function variant_label(?array $variant): string
{
    if (!$variant) {
        return '';
    }
    $parts = array_filter([
        $variant['size'] ?? null,
        $variant['color'] ?? null,
    ], static fn ($part) => $part !== null && $part !== '');
    return implode(' / ', $parts);
}
