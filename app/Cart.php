<?php

declare(strict_types=1);

final class Cart
{
    public static function items(): array
    {
        $raw = $_SESSION['cart'] ?? [];
        $items = [];

        foreach ($raw as $key => $row) {
            $product = Product::find((int) $row['product_id']);
            if (!$product || $product['status'] !== 'active') {
                unset($_SESSION['cart'][$key]);
                continue;
            }

            $variant = null;
            if (!empty($row['variant_id'])) {
                $variant = Product::findVariant((int) $row['variant_id']);
                if (!$variant || (int) $variant['product_id'] !== (int) $product['id']) {
                    unset($_SESSION['cart'][$key]);
                    continue;
                }
            }

            $unit = product_price($product, $variant);
            $qty = max(1, (int) $row['qty']);
            $images = Product::images((int) $product['id']);

            $items[] = [
                'key' => $key,
                'product' => $product,
                'variant' => $variant,
                'qty' => $qty,
                'unit_price' => $unit,
                'line_total' => $unit * $qty,
                'image' => $images[0]['path'] ?? null,
                'label' => variant_label($variant),
            ];
        }

        return $items;
    }

    public static function count(): int
    {
        $total = 0;
        foreach ($_SESSION['cart'] ?? [] as $row) {
            $total += max(1, (int) ($row['qty'] ?? 1));
        }
        return $total;
    }

    public static function total(): int
    {
        $sum = 0;
        foreach (self::items() as $item) {
            $sum += $item['line_total'];
        }
        return $sum;
    }

    public static function add(int $productId, ?int $variantId, int $qty): void
    {
        $qty = max(1, $qty);
        $key = $productId . ':' . ($variantId ?? 0);
        if (!isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'qty' => 0,
            ];
        }
        $_SESSION['cart'][$key]['qty'] += $qty;
    }

    public static function update(string $key, int $qty): void
    {
        if (!isset($_SESSION['cart'][$key])) {
            return;
        }
        if ($qty < 1) {
            unset($_SESSION['cart'][$key]);
            return;
        }
        $_SESSION['cart'][$key]['qty'] = $qty;
    }

    public static function remove(string $key): void
    {
        unset($_SESSION['cart'][$key]);
    }

    public static function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    public static function isEmpty(): bool
    {
        return self::items() === [];
    }
}
