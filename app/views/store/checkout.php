<section class="page-head">
    <div class="container">
        <p class="eyebrow">Pay online</p>
        <h1>Checkout</h1>
    </div>
</section>

<section class="section section-tight">
    <div class="container checkout-grid">
        <form class="stack-form" method="post" action="<?= e(url('checkout')) ?>">
            <?= Csrf::field() ?>
            <h2>Delivery details</h2>
            <label>Full name
                <input type="text" name="name" required value="<?= e(old('name')) ?>">
            </label>
            <label>Phone
                <input type="tel" name="phone" required value="<?= e(old('phone')) ?>">
            </label>
            <label>Email
                <input type="email" name="email" value="<?= e(old('email')) ?>">
            </label>
            <label>Address
                <textarea name="address" rows="3" required><?= e(old('address')) ?></textarea>
            </label>
            <label>City
                <input type="text" name="city" value="<?= e(old('city')) ?>">
            </label>
            <label>Notes
                <textarea name="notes" rows="2"><?= e(old('notes')) ?></textarea>
            </label>
            <p class="muted">You will pay online on the next screen. This shop is running a sandbox payment page until a live gateway is connected.</p>
            <button class="btn btn-gold" type="submit">Continue to payment</button>
        </form>

        <aside class="summary-card">
            <h2>Order</h2>
            <?php foreach ($items as $item): ?>
                <div class="summary-row">
                    <span><?= e($item['product']['name']) ?> × <?= (int) $item['qty'] ?></span>
                    <span><?= e(money($item['line_total'])) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="summary-row total">
                <span>Total</span>
                <span><?= e(money($total)) ?></span>
            </div>
        </aside>
    </div>
</section>
