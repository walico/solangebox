<?php
$heading = $heading ?? 'Shop';
$kicker = $kicker ?? 'Collections';
$bannerLead = $bannerLead ?? 'Explore the collection';
$bannerText = $bannerText ?? 'Clothing, jewelry, electronics, and hair — chosen to sit together.';
$action = $action ?? url('shop');
$currentSlug = $currentSlug ?? '';
$on_sale = !empty($on_sale);
$in_stock = !empty($in_stock);
$showSale = !empty($showSale);
$sort = $sort ?? 'newest';
$products = $products ?? [];
?>
<section class="list-banner">
    <div class="list-banner-media">
        <img src="<?= e(asset('images/hero-rack.png')) ?>" alt="">
    </div>
    <div class="list-banner-copy">
        <p class="eyebrow"><?= e($kicker) ?></p>
        <h1><?= e($bannerLead) ?></h1>
        <p><?= e($bannerText) ?></p>
    </div>
</section>

<section class="catalog">
    <aside class="catalog-side">
        <p class="catalog-crumb"><a href="<?= e(url()) ?>">Home</a> / <a href="<?= e(url('shop')) ?>">Shop</a></p>
        <h2><?= e($heading) ?></h2>
        <h3>Category</h3>
        <nav class="catalog-nav">
            <a href="<?= e(url('shop')) ?>" class="<?= $currentSlug === '' && empty($on_sale) ? 'is-active' : '' ?>">All products</a>
            <?php foreach ($categories as $navCat): ?>
                <a href="<?= e(url('category/' . $navCat['slug'])) ?>" class="<?= $currentSlug === $navCat['slug'] ? 'is-active' : '' ?>"><?= e($navCat['name']) ?></a>
            <?php endforeach; ?>
        </nav>
        <form class="catalog-filters" method="get" action="<?= e($action) ?>">
            <h3>Filter</h3>
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
            <?php if ($showSale): ?>
                <label class="check">
                    <input type="checkbox" name="sale" value="1" <?= $on_sale ? 'checked' : '' ?> onchange="this.form.submit()">
                    On sale
                </label>
            <?php endif; ?>
        </form>
    </aside>

    <div class="catalog-main">
        <?php if (!$products): ?>
            <p class="empty">Nothing to show yet.</p>
        <?php else: ?>
            <div class="product-grid catalog-grid">
                <?php foreach ($products as $product): ?>
                    <?php require ROOT_PATH . '/app/views/partials/product-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
