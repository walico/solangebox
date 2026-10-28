# Solange Box

A PHP/SQLite shop for clothing, jewelry, electronics, and hair. Designed for XAMPP.

## Open locally

1. Start Apache in XAMPP (PHP 8.2+).
2. Visit [http://localhost/solangebox/](http://localhost/solangebox/).

The first request creates `database/solange.sqlite` from `schema.sql` and seeds demo products plus an admin user.

Pretty URLs have no `.php` extension (`/cart`, `/product/gold-hoop-earrings`, `/admin/login`). Apache `mod_rewrite` must be on (XAMPP default).

## Admin

- URL: [http://localhost/solangebox/admin/login](http://localhost/solangebox/admin/login)
- Username: `admin`
- Password: `solangebox`

Change this password before you put the shop on a public server.

## Payments

Checkout is online-only. A sandbox payment page lets you **approve** or **fail** an order. Swap in a live gateway later by adding a class next to `app/payments/SandboxGateway.php` and changing `payment.provider` in `app/config.php`.

## Reset demo data

Delete `database/solange.sqlite` (and any `-wal` / `-shm` files) and reload the shop.
