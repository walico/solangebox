<?php
$shop = setting('shop_name', 'Solange Box');
$tagline = setting('tagline', 'class with elegance');
$categories = $categories ?? Category::roots();
$success = flash('success');
$error = flash('error');
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
                <button class="icon-btn nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="Menu">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h12M4 17h16" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                </button>
                <a class="brand" href="<?= e(url()) ?>">
                    <img class="brand-wordmark" src="<?= e(asset('images/logo-wordmark-dark.png')) ?>" alt="<?= e($shop) ?>">
                </a>
                <div class="top-links">
                    <!-- <a class="icon-btn" href="<?= e(url('contact')) ?>" aria-label="Notices">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 16V11a6 6 0 1 1 12 0v5l1.5 2H4.5L6 16z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M10 19a2 2 0 0 0 4 0" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>
                    </a> -->
                    <a class="icon-btn cart-link" href="<?= e(url('cart')) ?>" aria-label="Cart">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.5 8h11l-1 11h-9l-1-11z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M9 8V7.2A3 3 0 0 1 12 4a3 3 0 0 1 3 3.2V8" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>
                        <?php if (Cart::count() > 0): ?><span class="cart-count"><?= (int) Cart::count() ?></span><?php endif; ?>
                    </a>
                    <a class="icon-btn" href="<?= e(url('about')) ?>" aria-label="Account">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M5.5 19.2c1.4-2.6 3.6-3.7 6.5-3.7s5.1 1.1 6.5 3.7" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    </a>
                    <button class="icon-btn search-open" type="button" aria-label="Search" aria-controls="search-modal" aria-expanded="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M16 16.5L20 20.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    </button>
                </div>
            </div>
            <nav class="subnav" id="site-nav">
                <div class="nav-drop">
                    <button class="nav-chip" type="button" aria-expanded="false" aria-controls="site-menu">
                        Menu
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10l5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    </button>
                    <div class="nav-menu" id="site-menu">
                        <a href="<?= e(url()) ?>" class="<?= is_active('/') ? 'is-active' : '' ?>">Home</a>
                        <a href="<?= e(url()) ?>#browse">New Arrivals</a>
                        <a href="<?= e(url()) ?>?sale=1#browse">Sale</a>
                        <a href="<?= e(url('about')) ?>" class="<?= is_active('about') ? 'is-active' : '' ?>">About</a>
                        <a href="<?= e(url('contact')) ?>" class="<?= is_active('contact') ? 'is-active' : '' ?>">Contact</a>
                        <a href="<?= e(url('cart')) ?>" class="<?= is_active('cart') ? 'is-active' : '' ?>">Cart</a>
                    </div>
                </div>
                <a class="nav-chip" href="<?= e(url()) ?>#browse">New Arrivals</a>
                <a class="nav-chip" href="<?= e(url()) ?>?sale=1#browse">Sale</a>
                <?php foreach ($categories as $navCat): ?>
                    <?php if ($navCat['slug'] === 'clothing') continue; ?>
                    <a class="nav-chip <?= is_active('category/' . $navCat['slug']) ? 'is-active' : '' ?>" href="<?= e(url('category/' . $navCat['slug'])) ?>"><?= e($navCat['name']) ?></a>
                <?php endforeach; ?>
            </nav>
        </header>
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

        <footer class="site-footer">
            <div class="container footer-grid">
                <div>
                    <img class="footer-wordmark" src="<?= e(asset('images/logo-wordmark.png')) ?>" alt="<?= e($shop) ?>">
                    <p class="tagline"><?= e($tagline) ?></p>
                </div>
                <div>
                    <h3>Shop</h3>
                    <ul>
                        <?php foreach ($categories as $navCat): ?>
                            <li><a href="<?= e(url('category/' . $navCat['slug'])) ?>"><?= e($navCat['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h3>Visit</h3>
                    <ul>
                        <li><a href="<?= e(url('about')) ?>">About</a></li>
                        <li><a href="<?= e(url('contact')) ?>">Contact</a></li>
                        <li><?= e(setting('phone')) ?></li>
                        <li><?= e(setting('email')) ?></li>
                    </ul>
                </div>
            </div>
            <div class="container footer-base">
                <span>&copy; <?= date('Y') ?> <?= e($shop) ?></span>
                <span>class with elegance</span>
            </div>
        </footer>
    </div>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
