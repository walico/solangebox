<form class="stack-form admin-form" method="post" action="<?= e(url('admin/settings')) ?>">
    <?= Csrf::field() ?>
    <label>Shop name
        <input type="text" name="shop_name" value="<?= e($settings['shop_name'] ?? '') ?>" required>
    </label>
    <label>Tagline
        <input type="text" name="tagline" value="<?= e($settings['tagline'] ?? '') ?>">
    </label>
    <label>Phone
        <input type="text" name="phone" value="<?= e($settings['phone'] ?? '') ?>">
    </label>
    <label>Email
        <input type="email" name="email" value="<?= e($settings['email'] ?? '') ?>">
    </label>
    <label>Address
        <input type="text" name="address" value="<?= e($settings['address'] ?? '') ?>">
    </label>
    <label>About
        <textarea name="about" rows="5"><?= e($settings['about'] ?? '') ?></textarea>
    </label>
    <div class="form-grid">
        <label>Currency symbol
            <input type="text" name="currency_symbol" value="<?= e($settings['currency_symbol'] ?? 'MK') ?>" required>
        </label>
        <label>Low-stock line
            <input type="number" name="low_stock_threshold" min="0" max="999" value="<?= e((string) ($settings['low_stock_threshold'] ?? '5')) ?>">
        </label>
    </div>
    <p class="muted">Products at or below the low-stock line show up on the dashboard. Payments stay in sandbox mode until a live gateway is connected.</p>
    <button class="btn btn-gold" type="submit">Save settings</button>
</form>

<form class="stack-form admin-form" method="post" action="<?= e(url('admin/account/password')) ?>">
    <?= Csrf::field() ?>
    <h2>Password</h2>
    <label>Current password
        <input type="password" name="current_password" required autocomplete="current-password">
    </label>
    <label>New password
        <input type="password" name="new_password" required minlength="8" autocomplete="new-password">
    </label>
    <label>Confirm new password
        <input type="password" name="confirm_password" required minlength="8" autocomplete="new-password">
    </label>
    <button class="btn btn-ghost" type="submit">Update password</button>
</form>
