<div class="admin-toolbar">
    <nav class="pills">
        <a href="<?= e(url('admin/inventory')) ?>" class="<?= $stock === '' ? 'is-active' : '' ?>">All (<?= (int) $counts['all'] ?>)</a>
        <a href="<?= e(url('admin/inventory?stock=low')) ?>" class="<?= $stock === 'low' ? 'is-active' : '' ?>">Low, 1–<?= (int) $threshold ?> (<?= (int) $counts['low'] ?>)</a>
        <a href="<?= e(url('admin/inventory?stock=out')) ?>" class="<?= $stock === 'out' ? 'is-active' : '' ?>">Out (<?= (int) $counts['out'] ?>)</a>
        <a href="<?= e(url('admin/inventory?stock=watch')) ?>" class="<?= $stock === 'watch' ? 'is-active' : '' ?>">Low or out (<?= (int) $counts['watch'] ?>)</a>
    </nav>
</div>

<p class="muted">The shop uses the product total for Add to cart. When a product has variants, that total is the sum of the variant stocks.</p>

<form method="post" action="<?= e(url('admin/inventory')) ?>">
    <?= Csrf::field() ?>
    <input type="hidden" name="return_stock" value="<?= e($stock) ?>">
    <div class="admin-toolbar">
        <button class="btn btn-gold" type="submit">Save stock</button>
    </div>
    <table class="data">
        <thead>
            <tr><th>Product</th><th>Status</th><th>Stock</th></tr>
        </thead>
        <tbody>
            <?php if (!$products): ?>
                <tr><td colspan="3" class="empty">Nothing in this stock view.</td></tr>
            <?php endif; ?>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <a href="<?= e(url('admin/products/' . $product['id'] . '/edit')) ?>"><?= e($product['name']) ?></a>
                        <br><span class="muted"><?= e($product['category_name']) ?></span>
                        <?php if ($product['variants']): ?>
                            <div class="variant-lines">
                                <?php foreach ($product['variants'] as $variant): ?>
                                    <label class="variant-line">
                                        <span><?= e(variant_label($variant) ?: 'Variant') ?></span>
                                        <input class="stock-input" type="number" min="0" name="variant_stock[<?= (int) $variant['id'] ?>]" value="<?= (int) $variant['stock'] ?>">
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?= e($product['status']) ?></td>
                    <td>
                        <?php if ($product['variants']): ?>
                            <strong class="<?= e(stock_class((int) $product['stock'])) ?>"><?= (int) $product['stock'] ?></strong>
                            <span class="muted">total</span>
                        <?php else: ?>
                            <input class="stock-input <?= e(stock_class((int) $product['stock'])) ?>" type="number" min="0" name="stock[<?= (int) $product['id'] ?>]" value="<?= (int) $product['stock'] ?>">
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>
