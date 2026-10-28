<?php

declare(strict_types=1);

final class CartController
{
    public function show(): void
    {
        layout('store', 'store/cart', [
            'title' => 'Cart',
            'items' => Cart::items(),
            'total' => Cart::total(),
        ]);
    }

    public function add(): void
    {
        Csrf::verify();
        $productId = (int) ($_POST['product_id'] ?? 0);
        $variantId = ($_POST['variant_id'] ?? '') !== '' ? (int) $_POST['variant_id'] : null;
        $qty = max(1, (int) ($_POST['qty'] ?? 1));

        $product = Product::find($productId);
        if (!$product || $product['status'] !== 'active') {
            flash('error', 'That product is no longer available.');
            redirect('cart');
        }

        $variants = Product::variants($productId);
        if ($variants && !$variantId) {
            flash('error', 'Please choose an option.');
            redirect('product/' . $product['slug']);
        }

        Cart::add($productId, $variantId, $qty);
        flash('success', $product['name'] . ' has been added to your cart.');
        redirect('cart');
    }

    public function update(): void
    {
        Csrf::verify();
        $qtys = $_POST['qty'] ?? [];
        if (is_array($qtys)) {
            foreach ($qtys as $key => $qty) {
                Cart::update((string) $key, (int) $qty);
            }
        }
        flash('success', 'Cart updated.');
        redirect('cart');
    }

    public function remove(): void
    {
        Csrf::verify();
        Cart::remove((string) ($_POST['key'] ?? ''));
        flash('success', 'Item removed.');
        redirect('cart');
    }
}
