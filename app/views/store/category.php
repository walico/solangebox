<section class="page-head">
    <div class="container">
        <p class="eyebrow">Shop</p>
        <h1><?= e($category['name']) ?></h1>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <form class="toolbar" method="get">
            <label>
                Sort
                <select name="sort" onchange="this.form.submit()">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: low to high</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: high to low</option>
                    <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name</option>
                </select>
            </label>
            <label class="check">
                <input type="checkbox" name="in_stock" value="1" <?= $in_stock ? 'checked' : '' ?> onchange="this.form.submit()">
                In stock
            </label>
        </form>

        <?php if (!$products): ?>
            <p class="empty">No pieces in this category yet.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <?php require ROOT_PATH . '/app/views/partials/product-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
