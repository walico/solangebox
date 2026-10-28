<div class="admin-split order-layout">
    <div>
        <p>Placed <?= e($order['created_at']) ?></p>
        <p>Payment <?= e($order['payment_status']) ?> · <?= e($order['payment_provider']) ?> · <?= e($order['payment_ref'] ?? '—') ?></p>
        <table class="data">
            <thead><tr><th>Item</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['name']) ?><?= $item['variant_label'] ? '<br><span class="muted">' . e($item['variant_label']) . '</span>' : '' ?></td>
                        <td><?= (int) $item['qty'] ?></td>
                        <td><?= e(money($item['unit_price'])) ?></td>
                        <td><?= e(money($item['line_total'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong>Total <?= e(money($order['total'])) ?></strong></p>
    </div>
    <aside class="summary-card">
        <h2>Delivery</h2>
        <form method="post" action="<?= e(url('admin/orders/' . $order['id'] . '/contact')) ?>" class="stack-form">
            <?= Csrf::field() ?>
            <label>Name
                <input type="text" name="customer_name" required value="<?= e($order['customer_name']) ?>">
            </label>
            <label>Phone
                <input type="text" name="customer_phone" required value="<?= e($order['customer_phone']) ?>">
            </label>
            <label>Email
                <input type="email" name="customer_email" value="<?= e($order['customer_email'] ?? '') ?>">
            </label>
            <label>Address
                <textarea name="customer_address" rows="3" required><?= e($order['customer_address']) ?></textarea>
            </label>
            <label>City
                <input type="text" name="customer_city" value="<?= e($order['customer_city'] ?? '') ?>">
            </label>
            <label>Notes
                <textarea name="notes" rows="3"><?= e($order['notes'] ?? '') ?></textarea>
            </label>
            <button class="btn btn-ghost" type="submit">Save details</button>
        </form>
        <?php if (!empty($order['customer_id'])): ?>
            <p><a href="<?= e(url('admin/customers/' . $order['customer_id'])) ?>">Customer record</a></p>
        <?php endif; ?>
        <form method="post" action="<?= e(url('admin/orders/' . $order['id'] . '/status')) ?>" class="stack-form">
            <?= Csrf::field() ?>
            <label>Order status
                <select name="status">
                    <?php foreach (['pending', 'paid', 'shipped', 'delivered', 'cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button class="btn btn-gold" type="submit">Update status</button>
        </form>
        <p><a href="<?= e(url('order/' . $order['token'])) ?>" target="_blank" rel="noopener">Customer confirmation page</a></p>
    </aside>
</div>
