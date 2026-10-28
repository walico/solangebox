<?php
Auth::requireLogin();
$success = flash('success');
$error = flash('error');

if (!function_exists('admin_icon')) {
    function admin_icon(string $name): string
    {
        $paths = [
            'dashboard' => '<rect x="3.5" y="3.5" width="7" height="7" rx="1.4"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.4"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.4"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.4"/>',
            'products' => '<path d="M12 3.5 20 8 12 12.5 4 8 12 3.5Z"/><path d="M4 8v8l8 4.5 8-4.5V8"/><path d="M12 12.5V20.5"/>',
            'inventory' => '<rect x="6" y="3.5" width="12" height="17" rx="2"/><path d="M9 3.5h6v3H9z"/><path d="M9 11h6M9 15h4"/>',
            'categories' => '<path d="M4 7.5h6l2 2h8v8.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 4 18V7.5Z"/><path d="M4 7.5V6.2A1.7 1.7 0 0 1 5.7 4.5H10l2 2"/>',
            'orders' => '<path d="M7 3.5h10a1 1 0 0 1 1 1V20l-2-1.2-2 1.2-2-1.2-2 1.2-2-1.2L6 20V4.5a1 1 0 0 1 1-1Z"/><path d="M9 8h6M9 12h6"/>',
            'customers' => '<circle cx="12" cy="8" r="3.2"/><path d="M5.5 19.2c1.2-3 3.4-4.5 6.5-4.5s5.3 1.5 6.5 4.5"/>',
            'settings' => '<circle cx="12" cy="12" r="3"/><path d="M12 3.5v2.2M12 18.3v2.2M3.5 12h2.2M18.3 12h2.2M6 6l1.6 1.6M16.4 16.4 18 18M18 6l-1.6 1.6M7.6 16.4 6 18"/>',
            'logout' => '<path d="M14 7V5.8A1.8 1.8 0 0 1 15.8 4h2.4A1.8 1.8 0 0 1 20 5.8v12.4a1.8 1.8 0 0 1-1.8 1.8h-2.4A1.8 1.8 0 0 1 14 18.2V17"/><path d="M11 12H4"/><path d="m7 9-3 3 3 3"/>',
        ];
        $inner = $paths[$name] ?? '';
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" stroke-linecap="round" aria-hidden="true">' . $inner . '</svg>';
    }
}

$nav = [
    ['dashboard', 'Dashboard', 'admin', current_path() === '/admin'],
    ['products', 'Products', 'admin/products', is_active('admin/products')],
    ['inventory', 'Inventory', 'admin/inventory', is_active('admin/inventory')],
    ['categories', 'Categories', 'admin/categories', is_active('admin/categories')],
    ['orders', 'Orders', 'admin/orders', is_active('admin/orders')],
    ['customers', 'Customers', 'admin/customers', is_active('admin/customers')],
    ['settings', 'Settings', 'admin/settings', is_active('admin/settings')],
];

$sections = [
    '/admin/products' => ['Products', 'admin/products'],
    '/admin/inventory' => ['Inventory', 'admin/inventory'],
    '/admin/categories' => ['Categories', 'admin/categories'],
    '/admin/orders' => ['Orders', 'admin/orders'],
    '/admin/customers' => ['Customers', 'admin/customers'],
    '/admin/settings' => ['Settings', 'admin/settings'],
];
$crumb = null;
foreach ($sections as $prefix => $meta) {
    if (current_path() === $prefix || str_starts_with(current_path(), $prefix . '/')) {
        $crumb = $meta;
        break;
    }
}
$deeper = $crumb && current_path() !== '/' . $crumb[1];
$showPageTitle = current_path() !== '/admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(($title ?? 'Admin') . ' · Solange Box') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
    <link rel="icon" href="<?= e(asset('images/logo-mark.png')) ?>">
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-side">
            <a class="admin-logo" href="<?= e(url('admin')) ?>">solange.</a>
            <nav>
                <?php foreach ($nav as [$icon, $label, $href, $active]): ?>
                    <a href="<?= e(url($href)) ?>" class="<?= $active ? 'is-active' : '' ?>"><?= admin_icon($icon) ?><span><?= e($label) ?></span></a>
                <?php endforeach; ?>
            </nav>
            <form class="admin-logout-form" method="post" action="<?= e(url('admin/logout')) ?>">
                <?= Csrf::field() ?>
                <button class="admin-logout" type="submit"><?= admin_icon('logout') ?><span>Log out</span></button>
            </form>
        </aside>
        <div class="admin-main">
            <header class="admin-top">
                <nav class="crumbs" aria-label="Breadcrumb">
                    <?php if (!$crumb): ?>
                        <span>Dashboard</span>
                    <?php else: ?>
                        <?php if ($deeper): ?>
                            <a href="<?= e(url($crumb[1])) ?>"><?= e($crumb[0]) ?></a>
                            <span class="crumb-sep">&gt;</span>
                            <span><?= e($title ?? $crumb[0]) ?></span>
                        <?php else: ?>
                            <span><?= e($crumb[0]) ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </nav>
                <div class="admin-actions">
                    <a class="btn btn-ghost" href="<?= e(url()) ?>" target="_blank" rel="noopener">View shop</a>
                    <a class="btn btn-gold" href="<?= e(url('admin/products/new')) ?>">Add product</a>
                </div>
            </header>
            <div class="admin-content">
                <?php if ($success || $error): ?>
                    <div class="flashes">
                        <?php if ($success): ?><div class="flash flash-ok"><?= e($success) ?></div><?php endif; ?>
                        <?php if ($error): ?><div class="flash flash-err"><?= e($error) ?></div><?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if ($showPageTitle): ?>
                    <h1 class="page-title"><?= e($title ?? 'Admin') ?></h1>
                <?php endif; ?>
                <?php require ROOT_PATH . '/app/views/' . $content_template . '.php'; ?>
            </div>
        </div>
    </div>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
