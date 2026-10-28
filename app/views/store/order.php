<section class="page-head">
    <div class="container">
        <p class="eyebrow">Order confirmation</p>
        <h1><?= $order['payment_status'] === 'paid' ? 'Thank you' : 'Order received' ?></h1>
        <p class="muted">Reference <?= e($order['token']) ?></p>
    </div>
</section>

<section class="section section-tight">
    <div class="container checkout-grid">
        <div>
            <p>
                Status:
                <strong><?= e(ucfirst($order['status'])) ?></strong>
                · Payment:
                <strong><?= e(ucfirst($order['payment_status'])) ?></strong>
            </p>
            <?php if ($order['payment_status'] !== 'paid'): ?>
                <p>Payment was not completed.
                    <a class="btn btn-gold" href="<?= e(url('pay/sandbox/' . $order['token'])) ?>">Try payment again</a>
                </p>
            <?php else: ?>
                <p>We have your order and will prepare it for delivery to <?= e($order['customer_address']) ?>.</p>
            <?php endif; ?>

            <h2>Items</h2>
            <?php foreach ($items as $item): ?>
                <div class="summary-row">
                    <span><?= e($item['name']) ?><?= $item['variant_label'] ? ' · ' . e($item['variant_label']) : '' ?> × <?= (int) $item['qty'] ?></span>
                    <span><?= e(money($item['line_total'])) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="summary-row total">
                <span>Total</span>
                <span><?= e(money($order['total'])) ?></span>
            </div>
        </div>
        <aside class="summary-card">
            <h2>Deliver to</h2>
            <p><?= e($order['customer_name']) ?><br>
                <?= e($order['customer_phone']) ?><br>
                <?= e($order['customer_email']) ?><br>
                <?= nl2br(e($order['customer_address'])) ?><br>
                <?= e($order['customer_city']) ?></p>
        </aside>
    </div>
</section>
