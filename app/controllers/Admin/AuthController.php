<?php

declare(strict_types=1);

final class AdminAuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('admin');
        }
        layout('admin-auth', 'admin/login', [
            'title' => 'Admin sign in',
        ]);
    }

    public function login(): void
    {
        Csrf::verify();
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (Auth::attempt($username, $password)) {
            flash('success', 'Welcome back.');
            redirect('admin');
        }

        remember_old(['username' => $username]);
        flash('error', 'Those details did not match.');
        redirect('admin/login');
    }

    public function logout(): void
    {
        Csrf::verify();
        Auth::logout();
        flash('success', 'You have been signed out.');
        redirect('admin/login');
    }
}
