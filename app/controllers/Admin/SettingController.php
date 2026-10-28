<?php

declare(strict_types=1);

final class AdminSettingController
{
    public function edit(): void
    {
        Auth::requireLogin();
        layout('admin', 'admin/settings', [
            'title' => 'Settings',
            'settings' => Setting::all(),
        ]);
    }

    public function update(): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $threshold = (int) ($_POST['low_stock_threshold'] ?? 5);
        $symbol = trim((string) ($_POST['currency_symbol'] ?? ''));
        Setting::put([
            'shop_name' => trim((string) ($_POST['shop_name'] ?? 'Solange Box')),
            'tagline' => trim((string) ($_POST['tagline'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'address' => trim((string) ($_POST['address'] ?? '')),
            'about' => trim((string) ($_POST['about'] ?? '')),
            'currency_symbol' => $symbol !== '' ? $symbol : 'MK',
            'low_stock_threshold' => (string) max(0, min(999, $threshold)),
            'payment_mode' => 'sandbox',
        ]);
        flash('success', 'Settings saved.');
        redirect('admin/settings');
    }

    public function password(): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $user = Auth::user();
        $current = (string) ($_POST['current_password'] ?? '');
        $next = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');

        if (!$user || !password_verify($current, $user['password_hash'])) {
            flash('error', 'Current password is incorrect.');
            redirect('admin/settings');
        }
        if (strlen($next) < 8) {
            flash('error', 'New password must be at least 8 characters.');
            redirect('admin/settings');
        }
        if (!hash_equals($next, $confirm)) {
            flash('error', 'New passwords do not match.');
            redirect('admin/settings');
        }

        AdminUser::updatePassword((int) $user['id'], password_hash($next, PASSWORD_DEFAULT));
        flash('success', 'Password updated.');
        redirect('admin/settings');
    }
}
