<p><a href="<?= e(url('admin/customers')) ?>">All customers</a></p>

<div class="admin-split">
    <aside class="summary-card">
        <h2>Contact</h2>
        <p><?= e($customer['name']) ?><br>
            <?= e($customer['phone']) ?><br>
            <?= e($customer['email'] ?: 'No email') ?><br>
            <?= nl2br(e($customer['address'])) ?><br>
            <?= e($customer['city']) ?></p>
        <p class="muted">Added <?= e($customer['created_at']) ?>. Delivery details on an order can be corrected from that order.</p>
    </aside>
    <div>
        <h2>Orders</h2>
        <?php if (!$orders): ?>
            <p class="empty">No orders linked to this customer.</p>
        <?php else: ?>
            <table class="data">
                <thead>
                    <tr><th>#</th><th>Date</th><th>Total</th><th>Payment</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= (int) $order['id'] ?></td>
                            <td><?= e($order['created_at']) ?></td>
                            <td><?= e(money($order['total'])) ?></td>
                            <td><?= e($order['payment_status']) ?></td>
                            <td><?= e($order['status']) ?></td>
                            <td><a href="<?= e(url('admin/orders/' . $order['id'])) ?>">Open</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
