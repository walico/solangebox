<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('APP_PATH', ROOT_PATH . '/app');

$GLOBALS['app_config'] = require APP_PATH . '/config.php';
date_default_timezone_set((string) ($GLOBALS['app_config']['timezone'] ?? 'UTC'));

require APP_PATH . '/helpers.php';
require APP_PATH . '/db.php';
require APP_PATH . '/Router.php';
require APP_PATH . '/Csrf.php';
require APP_PATH . '/Auth.php';
require APP_PATH . '/Cart.php';
require APP_PATH . '/Upload.php';
require APP_PATH . '/models/AdminUser.php';
require APP_PATH . '/models/Setting.php';
require APP_PATH . '/models/Category.php';
require APP_PATH . '/models/Product.php';
require APP_PATH . '/models/Order.php';
require APP_PATH . '/models/Customer.php';
require APP_PATH . '/payments/PaymentGateway.php';
require APP_PATH . '/payments/SandboxGateway.php';
require APP_PATH . '/controllers/StoreController.php';
require APP_PATH . '/controllers/CartController.php';
require APP_PATH . '/controllers/CheckoutController.php';
require APP_PATH . '/controllers/Admin/AuthController.php';
require APP_PATH . '/controllers/Admin/DashboardController.php';
require APP_PATH . '/controllers/Admin/ProductController.php';
require APP_PATH . '/controllers/Admin/CategoryController.php';
require APP_PATH . '/controllers/Admin/OrderController.php';
require APP_PATH . '/controllers/Admin/CustomerController.php';
require APP_PATH . '/controllers/Admin/InventoryController.php';
require APP_PATH . '/controllers/Admin/SettingController.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('solangebox');
    session_start();
}

header('Content-Type: text/html; charset=utf-8');

function detect_base_path(): string
{
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $dir = rtrim(dirname($script), '/');
    if (str_ends_with($dir, '/public')) {
        $dir = substr($dir, 0, -7);
    }
    if ($dir === '/' || $dir === '\\' || $dir === '.') {
        return '';
    }
    return $dir;
}

define('BASE_PATH', detect_base_path());

function request_path(): string
{
    $uri = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
    $uri = rawurldecode($uri);
    $base = BASE_PATH;

    if ($base !== '' && str_starts_with($uri, $base)) {
        $uri = substr($uri, strlen($base)) ?: '/';
    }

    if (str_starts_with($uri, '/public/')) {
        $uri = substr($uri, 7) ?: '/';
    }

    $uri = '/' . trim($uri, '/');
    if ($uri === '/index.php' || str_starts_with($uri, '/index.php/')) {
        $clean = substr($uri, strlen('/index.php')) ?: '/';
        redirect(ltrim($clean, '/'), 301);
    }

    return $uri === '' ? '/' : $uri;
}

$GLOBALS['request_path'] = request_path();
db();
