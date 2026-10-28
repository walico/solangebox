<section class="page-head">
    <div class="container">
        <p class="eyebrow">Your bag</p>
        <h1>Cart</h1>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <?php if (!$items): ?>
            <p class="empty">Your cart is empty. <a href="<?= e(url()) ?>">Continue shopping</a>.</p>
        <?php else: ?>
            <form method="post" action="<?= e(url('cart/update')) ?>">
                <?= Csrf::field() ?>
                <div class="cart-table">
                    <?php foreach ($items as $item): ?>
                        <div class="cart-row">
                            <div class="cart-media">
                                <?php if ($item['image']): ?>
                                    <img src="<?= e(uploaded($item['image'])) ?>" alt="">
                                <?php endif; ?>
                            </div>
                            <div>
                                <h3><?= e($item['product']['name']) ?></h3>
                                <?php if ($item['label']): ?><p class="muted"><?= e($item['label']) ?></p><?php endif; ?>
                                <p><?= e(money($item['unit_price'])) ?></p>
                            </div>
                            <label class="qty-label">
                                Qty
                                <input type="number" name="qty[<?= e($item['key']) ?>]" min="1" value="<?= (int) $item['qty'] ?>">
                            </label>
                            <div class="cart-line"><?= e(money($item['line_total'])) ?></div>
                            <button class="linkish" type="submit" formaction="<?= e(url('cart/remove')) ?>" name="key" value="<?= e($item['key']) ?>">Remove</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="cart-actions">
                    <button class="btn btn-ghost" type="submit">Update cart</button>
                    <div class="cart-total">
                        <p>Total <strong><?= e(money($total)) ?></strong></p>
                        <a class="btn btn-gold" href="<?= e(url('checkout')) ?>">Checkout</a>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>
