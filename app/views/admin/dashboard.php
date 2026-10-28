<?php
$shop = setting('shop_name', 'Solange Box');
$tagline = trim((string) setting('tagline', ''));
$image = $spotlight['images'][0]['path'] ?? null;
$rows = [
    ['Paid revenue', money($revenue), url('admin/orders')],
    ['Orders', (string) (int) $counts['all'], url('admin/orders')],
    ['Awaiting payment', (string) (int) $counts['pending'], url('admin/orders?status=pending')],
    ['Ready to ship', (string) (int) $counts['paid'], url('admin/orders?status=paid')],
    ['Shipped', (string) (int) $counts['shipped'], url('admin/orders?status=shipped')],
    ['Delivered', (string) (int) $counts['delivered'], url('admin/orders?status=delivered')],
    ['Customers', (string) (int) $customer_count, url('admin/customers')],
    ['Active products', (string) (int) $active_count, url('admin/products?status=active')],
    ['Hidden products', (string) (int) $hidden_count, url('admin/products?status=hidden')],
    ['Categories', (string) (int) $category_count, url('admin/categories')],
    ['Low or out of stock', (string) (int) $watch_count, url('admin/inventory?stock=watch')],
    ['Failed payments', (string) (int) $failed_payments, url('admin/orders?payment=failed')],
];
?>
<article class="detail-card">
    <div class="detail-head">
        <div>
            <h1><?= e($shop) ?></h1>
            <p class="detail-kicker"><?= e($tagline !== '' ? $tagline : 'Shop overview') ?></p>
            <p class="meta-line"><span>Status</span> <em class="status-pill">Live</em></p>
            <p class="meta-line"><span>Paid revenue</span> <strong><?= e(money($revenue)) ?></strong></p>
        </div>
        <aside class="code-card">
            <span>Orders</span>
            <strong><?= (int) $counts['all'] ?></strong>
            <span><?= (int) $counts['pending'] ?> awaiting payment</span>
        </aside>
    </div>

    <nav class="detail-tabs">
        <a class="is-active" href="<?= e(url('admin')) ?>">Overview</a>
        <a href="<?= e(url('admin/orders')) ?>">Orders</a>
        <a href="<?= e(url('admin/inventory')) ?>">Inventory</a>
        <a href="<?= e(url('admin/customers')) ?>">Customers</a>
        <a class="btn btn-ghost tab-action" href="<?= e(url('admin/settings')) ?>">Edit</a>
    </nav>

    <div class="detail-body">
        <table class="spec">
            <tbody>
                <?php foreach ($rows as [$label, $value, $href]): ?>
                    <tr>
                        <th><?= e($label) ?></th>
                        <td><a href="<?= e($href) ?>"><?= e($value) ?></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <aside class="detail-side">
            <?php if ($spotlight): ?>
                <a class="side-visual" href="<?= e(url('admin/products/' . $spotlight['id'] . '/edit')) ?>">
                    <?php if ($image): ?>
                        <img src="<?= e(uploaded($image)) ?>" alt="">
                    <?php else: ?>
                        <span class="side-fallback"><?= e($spotlight['name']) ?></span>
                    <?php endif; ?>
                </a>
                <p class="side-caption">
                    <strong><?= e($spotlight['name']) ?></strong>
                    <span><?= e($spotlight['category_name'] ?? '') ?></span>
                </p>
            <?php else: ?>
                <div class="side-visual"><span class="side-fallback">No products yet</span></div>
            <?php endif; ?>

            <div class="history">
                <h3>Recent orders</h3>
                <div class="history-head">No.</div>
                <?php if (!$recent_orders): ?>
                    <p class="history-empty">No orders yet.</p>
                <?php else: ?>
                    <?php foreach ($recent_orders as $order): ?>
                        <a class="history-row" href="<?= e(url('admin/orders/' . $order['id'])) ?>">
                            <span>#<?= (int) $order['id'] ?></span>
                            <span><?= e($order['customer_name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</article>
