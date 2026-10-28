<section class="page-head">
    <div class="container">
        <p class="eyebrow">Sandbox gateway</p>
        <h1>Complete payment</h1>
    </div>
</section>
<section class="section section-tight">
    <div class="container narrow sandbox-box">
        <p>This is a local test payment page. Approve to mark the order paid, or fail to simulate a declined card or mobile-money error.</p>
        <p>Amount due <strong><?= e(money($order['total'])) ?></strong></p>
        <p class="muted">Order <?= e($order['token']) ?></p>
        <ul>
            <?php foreach ($items as $item): ?>
                <li><?= e($item['name']) ?> × <?= (int) $item['qty'] ?> — <?= e(money($item['line_total'])) ?></li>
            <?php endforeach; ?>
        </ul>
        <form method="post" action="<?= e(url('pay/sandbox/' . $order['token'])) ?>" class="sandbox-actions">
            <?= Csrf::field() ?>
            <input type="hidden" name="token" value="<?= e($order['token']) ?>">
            <button class="btn btn-gold" type="submit" name="result" value="success">Approve payment</button>
            <button class="btn btn-ghost" type="submit" name="result" value="fail">Fail payment</button>
        </form>
    </div>
</section>
