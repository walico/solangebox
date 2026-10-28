<?php
$orderQuery = static function (array $overrides) use ($status, $payment, $q): string {
    $params = array_filter([
        'status' => $status,
        'payment' => $payment,
        'q' => $q,
    ], static fn ($value) => $value !== '');
    foreach ($overrides as $key => $value) {
        if ($value === '') {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }
    $query = http_build_query($params);
    return 'admin/orders' . ($query !== '' ? '?' . $query : '');
};
?>
<div class="admin-toolbar">
    <form method="get" action="<?= e(url('admin/orders')) ?>">
        <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
        <?php if ($payment !== ''): ?><input type="hidden" name="payment" value="<?= e($payment) ?>"><?php endif; ?>
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Name, phone, email, or order #">
        <button class="btn btn-ghost" type="submit">Search</button>
    </form>
</div>
<nav class="pills">
    <a href="<?= e(url($orderQuery(['status' => '']))) ?>" class="<?= $status === '' ? 'is-active' : '' ?>">All (<?= (int) $counts['all'] ?>)</a>
    <?php foreach (['pending', 'paid', 'shipped', 'delivered', 'cancelled'] as $s): ?>
        <a href="<?= e(url($orderQuery(['status' => $s]))) ?>" class="<?= $status === $s ? 'is-active' : '' ?>"><?= e(ucfirst($s)) ?> (<?= (int) $counts[$s] ?>)</a>
    <?php endforeach; ?>
    <?php if ($payment !== ''): ?>
        <a href="<?= e(url($orderQuery(['payment' => '']))) ?>">Payment: <?= e($payment) ?> ×</a>
    <?php endif; ?>
</nav>

<table class="data">
    <thead>
        <tr><th>#</th><th>Date</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
        <?php if (!$orders): ?>
            <tr><td colspan="7" class="empty">No orders in this view.</td></tr>
        <?php endif; ?>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= (int) $order['id'] ?></td>
                <td><?= e($order['created_at']) ?></td>
                <td><?= e($order['customer_name']) ?><br><span class="muted"><?= e($order['customer_phone']) ?></span></td>
                <td><?= e(money($order['total'])) ?></td>
                <td><?= e($order['payment_status']) ?></td>
                <td><?= e($order['status']) ?></td>
                <td><a href="<?= e(url('admin/orders/' . $order['id'])) ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
