<form method="post" action="<?= e(url('admin/login')) ?>" class="stack-form">
    <?= Csrf::field() ?>
    <label>Username
        <input type="text" name="username" value="<?= e(old('username')) ?>" required autofocus>
    </label>
    <label>Password
        <input type="password" name="password" required>
    </label>
    <button class="btn btn-gold" type="submit">Sign in</button>
</form>
