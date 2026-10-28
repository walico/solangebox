<?php
$shop = setting('shop_name', 'Solange Box');
$tagline = setting('tagline', 'class with elegance');
$categories = $categories ?? Category::roots();
$success = flash('success');
$error = flash('error');
$returnPath = ltrim(current_path(), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(($title ?? $shop) === $shop ? $shop : ($title . ' · ' . $shop)) ?></title>
    <meta name="description" content="<?= e($tagline) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
    <link rel="icon" href="<?= e(asset('images/logo-mark.png')) ?>">
</head>
<body class="store-body">
    <div class="sheet">
        <header class="site-header">
            <div class="topbar">
                <div class="brand-cluster">
                    <button class="icon-btn nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="Menu">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                    <a class="brand" href="<?= e(url()) ?>" aria-label="<?= e($shop) ?>">
                        <img class="brand-wordmark" src="<?= e(asset('images/logo-wordmark-dark.png')) ?>" alt="">
                        <img class="brand-mark" src="<?= e(asset('images/logo-mark.png')) ?>" alt="">
                    </a>
                </div>
                <div class="top-links">
                    <a class="icon-btn cart-link" href="<?= e(url('cart')) ?>" aria-label="Cart">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.5 8h11l-1 11h-9l-1-11z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M9 8V7.2A3 3 0 0 1 12 4a3 3 0 0 1 3 3.2V8" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>
                        <?php if (Cart::count() > 0): ?><span class="cart-count"><?= (int) Cart::count() ?></span><?php endif; ?>
                    </a>
                    <a class="icon-btn account-link" href="<?= e(url('about')) ?>" aria-label="Account">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M5.5 19.2c1.4-2.6 3.6-3.7 6.5-3.7s5.1 1.1 6.5 3.7" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    </a>
                    <button class="icon-btn search-open" type="button" aria-label="Search" aria-controls="search-modal" aria-expanded="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M16 16.5L20 20.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    </button>
                </div>
            </div>
        </header>
            <nav class="site-nav" id="site-nav">
                <div class="drawer-head">
                    <button class="icon-btn nav-close" type="button" aria-label="Close menu">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                    <a href="<?= e(url()) ?>">
                        <img src="<?= e(asset('images/logo-wordmark-dark.png')) ?>" alt="<?= e($shop) ?>">
                    </a>
                </div>
                <div class="drawer-links">
                    <a href="<?= e(url()) ?>" class="<?= is_active('/') ? 'is-active' : '' ?>">Home</a>
                    <a href="<?= e(url('shop')) ?>" class="<?= is_active('shop') ? 'is-active' : '' ?>">Shop</a>
                    <a class="drawer-extra" href="<?= e(url('shop?sale=1')) ?>">Sale</a>
                    <a href="<?= e(url('collections')) ?>" class="<?= is_active('collections') || str_starts_with(current_path(), '/category/') ? 'is-active' : '' ?>">Collections</a>
                    <?php foreach ($categories as $navCat): ?>
                        <a class="drawer-extra <?= is_active('category/' . $navCat['slug']) ? 'is-active' : '' ?>" href="<?= e(url('category/' . $navCat['slug'])) ?>"><?= e($navCat['name']) ?></a>
                    <?php endforeach; ?>
                    <a href="<?= e(url('about')) ?>" class="<?= is_active('about') ? 'is-active' : '' ?>">About Us</a>
                    <a href="<?= e(url('blog')) ?>" class="<?= is_active('blog') ? 'is-active' : '' ?>">Blog</a>
                    <a href="<?= e(url('contact')) ?>" class="<?= is_active('contact') ? 'is-active' : '' ?>">Contact Us</a>
                </div>
            </nav>
            <div class="nav-backdrop" hidden></div>
        <div class="search-modal" id="search-modal" hidden>
            <div class="search-modal-card" role="dialog" aria-modal="true" aria-labelledby="search-modal-title">
                <h2 id="search-modal-title">Search</h2>
                <form action="<?= e(url('search')) ?>" method="get">
                    <input type="search" name="q" placeholder="Search the shop" value="<?= e($_GET['q'] ?? '') ?>" aria-label="Search">
                    <button class="btn btn-gold" type="submit">Search</button>
                </form>
                <button class="search-close" type="button" aria-label="Close search">&times;</button>
            </div>
        </div>

        <main>
            <?php if ($success || $error): ?>
                <div class="container flashes">
                    <?php if ($success): ?><div class="flash flash-ok"><?= e($success) ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="flash flash-err"><?= e($error) ?></div><?php endif; ?>
                </div>
            <?php endif; ?>
            <?php require ROOT_PATH . '/app/views/' . $content_template . '.php'; ?>
        </main>

        <div class="site-end">
            <section class="newsletter" id="newsletter">
                <div class="newsletter-card">
                    <div class="newsletter-visual">
                        <img src="<?= e(asset('images/logo-mark.png')) ?>" alt="">
                    </div>
                    <div class="newsletter-copy">
                        <h2>Newsletter</h2>
                        <p>Subscribe and get 10% off your first purchase.</p>
                        <form class="newsletter-form" action="<?= e(url('newsletter')) ?>" method="post">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="return" value="<?= e($returnPath) ?>">
                            <label class="sr-only" for="newsletter-email">Your email</label>
                            <input id="newsletter-email" type="email" name="email" placeholder="Your Email" required>
                            <button type="submit">Send</button>
                        </form>
                    </div>
                </div>
            </section>

            <footer class="site-footer">
                <div class="container footer-grid">
                    <div class="footer-brand">
                        <img class="footer-wordmark" src="<?= e(asset('images/logo-wordmark.png')) ?>" alt="<?= e($shop) ?>">
                        <p class="tagline"><?= e($tagline) ?></p>
                    </div>
                    <div>
                        <h3>Help &amp; Information</h3>
                        <ul>
                            <li><a href="<?= e(url('contact')) ?>">Help</a></li>
                            <li><a href="<?= e(url('contact')) ?>">Track order</a></li>
                            <li><a href="<?= e(url('delivery')) ?>">Delivery &amp; returns</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3>About <?= e($shop) ?></h3>
                        <ul>
                            <li><a href="<?= e(url('about')) ?>">About Us</a></li>
                            <li><a href="<?= e(url('contact')) ?>">Contact Us</a></li>
                            <li><a href="<?= e(url('collections')) ?>">Collections</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3>More from <?= e($shop) ?></h3>
                        <ul>
                            <li><a href="<?= e(url('shop')) ?>">Shop</a></li>
                            <li><a href="<?= e(url('blog')) ?>">Blog</a></li>
                            <li><a href="#newsletter">Newsletter</a></li>
                        </ul>
                    </div>
                </div>
                <div class="container footer-base">
                    <span>&copy; <?= date('Y') ?> <?= e($shop) ?>. All Rights Reserved.</span>
                    <span class="footer-credit">Design by <a href="https://noninestudio.com" target="_blank" rel="noopener noreferrer">NO9 STUDIO</a></span>
                </div>
            </footer>
        </div>
    </div>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
