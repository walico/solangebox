<?php
$success = flash('success');
$error = flash('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(($title ?? 'Sign in') . ' · Solange Box') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
    <link rel="icon" href="<?= e(asset('images/logo-mark.png')) ?>">
</head>
<body class="auth-body">
    <div class="auth-card">
        <a class="admin-logo" href="<?= e(url('admin/login')) ?>">solange.</a>
        <p class="detail-kicker">Admin</p>
        <?php if ($success): ?><div class="flash flash-ok"><?= e($success) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="flash flash-err"><?= e($error) ?></div><?php endif; ?>
        <?php require ROOT_PATH . '/app/views/' . $content_template . '.php'; ?>
    </div>
</body>
</html>
