<section class="page-head">
    <div class="container">
        <p class="eyebrow">Collections</p>
        <h1>Shop by category</h1>
    </div>
</section>

<section class="section section-tight">
    <div class="shop-cats">
        <div class="cat-row cat-row-page">
            <?php foreach ($categories as $category): ?>
                <a class="cat-tile" href="<?= e(url('category/' . $category['slug'])) ?>">
                    <span class="cat-icon">
                        <?php require ROOT_PATH . '/app/views/partials/category-icon.php'; ?>
                    </span>
                    <span class="cat-name"><?= e($category['name']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
