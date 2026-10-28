<?php
$productQuery = static function (array $overrides) use ($q, $status, $stock): string {
    $params = array_filter([
        'q' => $q,
        'status' => $status,
        'stock' => $stock,
    ], static fn ($value) => $value !== '');
    foreach ($overrides as $key => $value) {
        if ($value === '') {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }
    $query = http_build_query($params);
    return 'admin/products' . ($query !== '' ? '?' . $query : '');
};
?>
<div class="admin-toolbar">
    <form method="get" action="<?= e(url('admin/products')) ?>">
        <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
        <?php if ($stock !== ''): ?><input type="hidden" name="stock" value="<?= e($stock) ?>"><?php endif; ?>
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search products">
        <button class="btn btn-ghost" type="submit">Search</button>
    </form>
    <a class="btn btn-gold" href="<?= e(url('admin/products/new')) ?>">Add product</a>
</div>
<nav class="pills">
    <a href="<?= e(url($productQuery(['status' => '', 'stock' => '']))) ?>" class="<?= $status === '' && $stock === '' ? 'is-active' : '' ?>">All</a>
    <a href="<?= e(url($productQuery(['status' => 'active', 'stock' => '']))) ?>" class="<?= $status === 'active' && $stock === '' ? 'is-active' : '' ?>">Active</a>
    <a href="<?= e(url($productQuery(['status' => 'hidden', 'stock' => '']))) ?>" class="<?= $status === 'hidden' && $stock === '' ? 'is-active' : '' ?>">Hidden</a>
    <a href="<?= e(url($productQuery(['stock' => 'low', 'status' => '']))) ?>" class="<?= $stock === 'low' ? 'is-active' : '' ?>">Low stock</a>
    <a href="<?= e(url($productQuery(['stock' => 'out', 'status' => '']))) ?>" class="<?= $stock === 'out' ? 'is-active' : '' ?>">Out of stock</a>
</nav>

<table class="data">
    <thead>
        <tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
        <?php if (!$products): ?>
            <tr><td colspan="6" class="empty">No products match this view.</td></tr>
        <?php endif; ?>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= e($product['name']) ?></td>
                <td><?= e($product['category_name']) ?></td>
                <td>
                    <?= e(money($product['sale_price'] ?: $product['price'])) ?>
                    <?php if ($product['sale_price']): ?><span class="muted"><?= e(money($product['price'])) ?></span><?php endif; ?>
                </td>
                <td class="<?= e(stock_class((int) $product['stock'])) ?>"><?= (int) $product['stock'] ?></td>
                <td><?= e($product['status']) ?></td>
                <td class="row-actions">
                    <a href="<?= e(url('admin/products/' . $product['id'] . '/edit')) ?>">Edit</a>
                    <form method="post" action="<?= e(url('admin/products/' . $product['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this product?');">
                        <?= Csrf::field() ?>
                        <button class="linkish" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
