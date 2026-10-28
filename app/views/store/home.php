<section class="hero-banner" id="hero">
    <div class="hero-slide is-on">
        <img class="hero-photo" src="<?= e(asset('images/hero-rack.png')) ?>" alt="">
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
        <img class="hero-photo hero-photo-alt" src="<?= e(asset('images/hero-rack2.jpg')) ?>" alt="">
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
        <a class="pill-btn" href="<?= e(url('shop')) ?>">
            Start shopping
            <span class="arrow" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M7 17L17 7M9 7h8v8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        </a>
    </div>
</section>

<section class="section shop-cats" id="categories">
    <header class="shop-cats-head">
        <h2>Shop by Category</h2>
        <a class="view-all" href="<?= e(url('collections')) ?>">View all categories</a>
    </header>
    <div class="cat-row">
        <?php foreach ($categories as $category): ?>
            <a class="cat-tile" href="<?= e(url('category/' . $category['slug'])) ?>">
                <span class="cat-icon">
                    <?php require ROOT_PATH . '/app/views/partials/category-icon.php'; ?>
                </span>
                <span class="cat-name"><?= e($category['name']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="promo-row">
        <a class="promo-card promo-sale" href="<?= e(url('shop?sale=1')) ?>">
            <div>
                <h3>On sale now</h3>
                <p>Selected pieces, marked down for a short time.</p>
                <span class="promo-link">Shop deals</span>
            </div>
            <span class="promo-glyph" aria-hidden="true">%</span>
        </a>
        <a class="promo-card promo-fresh" href="<?= e(url('shop')) ?>">
            <div>
                <h3>Fresh finds</h3>
                <p>New arrivals chosen for the box this week.</p>
                <span class="promo-link">Explore now</span>
            </div>
            <span class="promo-glyph" aria-hidden="true">+</span>
        </a>
        <a class="promo-card promo-member" href="#newsletter">
            <div>
                <p class="promo-kicker">Member exclusive</p>
                <h3>Extra 10% off</h3>
                <p>Join the list and save on your first order.</p>
            </div>
            <span class="promo-percent" aria-hidden="true">%</span>
        </a>
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

<section class="mid-banner">
    <img src="<?= e(asset('images/hero-rack.png')) ?>" alt="">
    <div class="mid-banner-shade"></div>
    <div class="mid-banner-copy">
        <p class="eyebrow">The edit</p>
        <h2>Quiet style, modern ease.</h2>
        <p>Clothes, jewelry, electronics, and hair — chosen to sit together, ready for everyday wear.</p>
        <a class="pill-btn" href="<?= e(url('collections')) ?>">
            View collections
            <span class="arrow" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M7 17L17 7M9 7h8v8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        </a>
    </div>
</section>

<?php if (!empty($posts)): ?>
<section class="section our-blog">
    <header class="shop-cats-head">
        <h2>Our blog</h2>
        <a class="view-all" href="<?= e(url('blog')) ?>">View all</a>
    </header>
    <div class="blog-row">
        <?php foreach ($posts as $post): ?>
            <article class="blog-card">
                <a class="blog-media" href="<?= e(url('blog/' . $post['slug'])) ?>">
                    <img src="<?= e($post['image']) ?>" alt="">
                </a>
                <div class="blog-copy">
                    <p class="blog-date"><?= e($post['date']) ?></p>
                    <h3><a href="<?= e(url('blog/' . $post['slug'])) ?>"><?= e($post['title']) ?></a></h3>
                    <p><?= e($post['excerpt']) ?></p>
                    <a class="promo-link" href="<?= e(url('blog/' . $post['slug'])) ?>">Read more</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
