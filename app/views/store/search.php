<section class="page-head">
    <div class="container">
        <p class="eyebrow">Search</p>
        <h1><?= $q !== '' ? e('Results for “' . $q . '”') : 'Search the shop' ?></h1>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <form class="toolbar" method="get" action="<?= e(url('search')) ?>">
            <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search products">
            <label>
                Sort
                <select name="sort" onchange="this.form.submit()">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: low to high</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: high to low</option>
                </select>
            </label>
            <button class="btn btn-ghost" type="submit">Search</button>
        </form>

        <?php if ($q === ''): ?>
            <p class="empty">Type a name or description to find a piece.</p>
        <?php elseif (!$products): ?>
            <p class="empty">Nothing matched that search.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <?php require ROOT_PATH . '/app/views/partials/product-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
