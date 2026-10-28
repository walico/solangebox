<div class="admin-toolbar">
    <form method="get" action="<?= e(url('admin/customers')) ?>">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Name, phone, email, or city">
        <button class="btn btn-ghost" type="submit">Search</button>
    </form>
    <p class="muted toolbar-note"><?= (int) $customer_count ?> customer records</p>
</div>

<table class="data">
    <thead>
        <tr><th>Customer</th><th>Contact</th><th>City</th><th>Orders</th><th>Paid</th><th>Last order</th><th></th></tr>
    </thead>
    <tbody>
        <?php if (!$customers): ?>
            <tr><td colspan="7" class="empty">No customers yet. A record is created at checkout.</td></tr>
        <?php endif; ?>
        <?php foreach ($customers as $customer): ?>
            <tr>
                <td><?= e($customer['name']) ?></td>
                <td><?= e($customer['phone']) ?><?php if ($customer['email']): ?><br><span class="muted"><?= e($customer['email']) ?></span><?php endif; ?></td>
                <td><?= e($customer['city'] ?: '—') ?></td>
                <td><?= (int) $customer['order_count'] ?></td>
                <td><?= e(money($customer['spent'])) ?></td>
                <td><?= e($customer['last_order_at'] ?: '—') ?></td>
                <td><a href="<?= e(url('admin/customers/' . $customer['id'])) ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
