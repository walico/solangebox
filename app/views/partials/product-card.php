<?php
$images = $product['images'] ?? [];
$image = $images[0]['path'] ?? null;
$onSale = !empty($product['sale_price']) && (int) $product['sale_price'] < (int) $product['price'];
$blurb = trim((string) ($product['description'] ?? ''));
if (strlen($blurb) > 40) {
    $blurb = rtrim(substr($blurb, 0, 40)) . '...';
}
$filter = $product['filter'] ?? ($product['category_slug'] ?? '');
?>
<article class="product-card" data-category="<?= e($filter) ?>" data-sale="<?= $onSale ? '1' : '0' ?>">
    <a class="card-media" href="<?= e(url('product/' . $product['slug'])) ?>">
        <?php if ($image): ?>
            <img src="<?= e(uploaded($image)) ?>" alt="<?= e($product['name']) ?>">
        <?php else: ?>
            <div class="media-fallback"><?= e(substr($product['name'], 0, 1)) ?></div>
        <?php endif; ?>
    </a>
    <div class="card-body">
        <h3><a href="<?= e(url('product/' . $product['slug'])) ?>"><?= e($product['name']) ?></a></h3>
        <?php if ($blurb !== ''): ?><p class="card-blurb"><?= e($blurb) ?></p><?php endif; ?>
        <div class="card-meta">
            <span class="price-pill"><?= e(money($onSale ? $product['sale_price'] : $product['price'])) ?></span>
            <div class="card-actions">
                <button class="wish-btn" type="button" aria-label="Save <?= e($product['name']) ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 19s-7-4.4-7-9a4 4 0 0 1 7-2 4 4 0 0 1 7 2c0 4.6-7 9-7 9z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                </button>
                <a class="bag-btn" href="<?= e(url('product/' . $product['slug'])) ?>" aria-label="View <?= e($product['name']) ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.5 8h11l-1 11h-9l-1-11z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M9 8V7.2A3 3 0 0 1 12 4a3 3 0 0 1 3 3.2V8" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>
                </a>
            </div>
        </div>
    </div>
</article>
