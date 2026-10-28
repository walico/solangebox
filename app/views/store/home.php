<?php
$featureImage = $feature['images'][0]['path'] ?? null;
$spotImage = $spotlight['images'][0]['path'] ?? null;
$year = date('Y');
?>
<section class="hero-banner" id="hero">
    <div class="hero-slide is-on">
        <img class="hero-photo" src="<?= e(asset('images/hero-rack.jpg')) ?>" alt="">
        <div class="hero-shade"></div>
        <div class="hero-copy">
            <h1>Solange Box.</h1>
            <p class="hero-lead">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7l10 10M17 9v8H9" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>Discover clothing, jewelry, electronics, and hair chosen to sit together. Pieces with a quiet finish, ready for everyday wear.</span>
            </p>
        </div>
    </div>
    <div class="hero-slide">
        <img class="hero-photo hero-photo-alt" src="<?= e(asset('images/hero-rack.jpg')) ?>" alt="">
        <div class="hero-shade"></div>
        <div class="hero-copy">
            <h1>New arrivals.</h1>
            <p class="hero-lead">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7l10 10M17 9v8H9" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>A smaller edit of clothes and jewelry, just in. Shop the newest pieces before they leave the box.</span>
            </p>
        </div>
    </div>
    <div class="hero-navs">
        <button class="hero-nav prev" type="button" data-hero="-1" aria-label="Previous">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 6l-6 6 6 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button class="hero-nav next" type="button" data-hero="1" aria-label="Next">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
    </div>
    <div class="hero-cta">
        <a class="pill-btn" href="<?= e(url('category/clothing')) ?>">
            Start shopping
            <span class="arrow" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M7 17L17 7M9 7h8v8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        </a>
        <a class="hero-more" href="#browse">Top Collection</a>
    </div>
</section>

<section class="section editorial">
    <header class="section-head center">
        <h2>Quiet style, modern ease</h2>
        <p>The box is updated with new arrivals, so what you need is already here. Shop now and let the newest pieces do the rest.</p>
    </header>
    <div class="editorial-grid">
        <?php if ($feature): ?>
            <a class="feature-card feature-large" href="<?= e(url('product/' . $feature['slug'])) ?>">
                <?php if ($featureImage): ?>
                    <img src="<?= e(uploaded($featureImage)) ?>" alt="<?= e($feature['name']) ?>">
                <?php endif; ?>
                <div class="feature-caption">
                    <h3>Discover the collection</h3>
                    <p>Everyday pieces with a considered finish.</p>
                </div>
            </a>
        <?php endif; ?>
        <div class="feature-stack">
            <article class="note-card">
                <h3>New pieces, quietly chosen.</h3>
                <p>A small edit of clothes, jewelry, and things you will actually use.</p>
            </article>
            <?php if ($spotlight): ?>
                <a class="feature-card feature-small" href="<?= e(url('product/' . $spotlight['slug'])) ?>">
                    <?php if ($spotImage): ?>
                        <img src="<?= e(uploaded($spotImage)) ?>" alt="<?= e($spotlight['name']) ?>">
                    <?php endif; ?>
                    <span class="year-badge"><?= e($year) ?><br>New arrival</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section browse" id="browse">
    <header class="section-head center">
        <h2>Browse all you need.</h2>
    </header>
    <div class="filter-pills" role="tablist">
        <button type="button" class="filter-pill is-active" data-filter="all">All</button>
        <?php foreach ($categories as $category): ?>
            <button type="button" class="filter-pill" data-filter="<?= e($category['slug']) ?>"><?= e($category['name']) ?></button>
        <?php endforeach; ?>
    </div>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <?php require ROOT_PATH . '/app/views/partials/product-card.php'; ?>
        <?php endforeach; ?>
    </div>
    <p class="empty filter-empty" hidden>Nothing in this category yet.</p>
</section>
