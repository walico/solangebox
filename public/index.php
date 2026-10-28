<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

$router = new Router();

$router->get('/', [StoreController::class, 'home']);
$router->get('/shop', [StoreController::class, 'shop']);
$router->get('/collections', [StoreController::class, 'collections']);
$router->get('/blog', [StoreController::class, 'blog']);
$router->get('/blog/{slug}', [StoreController::class, 'blogPost']);
$router->get('/about', [StoreController::class, 'about']);
$router->get('/contact', [StoreController::class, 'contact']);
$router->post('/contact', [StoreController::class, 'sendContact']);
$router->get('/delivery', [StoreController::class, 'delivery']);
$router->post('/newsletter', [StoreController::class, 'newsletter']);
$router->get('/search', [StoreController::class, 'search']);
$router->get('/category/{slug}', [StoreController::class, 'category']);
$router->get('/product/{slug}', [StoreController::class, 'product']);

$router->get('/cart', [CartController::class, 'show']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);

$router->get('/checkout', [CheckoutController::class, 'show']);
$router->post('/checkout', [CheckoutController::class, 'place']);
$router->get('/pay/sandbox/{token}', [CheckoutController::class, 'sandbox']);
$router->post('/pay/sandbox/{token}', [CheckoutController::class, 'sandboxReturn']);
$router->get('/order/{token}', [CheckoutController::class, 'confirmation']);

$router->get('/admin/login', [AdminAuthController::class, 'showLogin']);
$router->post('/admin/login', [AdminAuthController::class, 'login']);
$router->post('/admin/logout', [AdminAuthController::class, 'logout']);
$router->get('/admin', [AdminDashboardController::class, 'index']);

$router->get('/admin/products', [AdminProductController::class, 'index']);
$router->get('/admin/products/new', [AdminProductController::class, 'create']);
$router->post('/admin/products', [AdminProductController::class, 'store']);
$router->get('/admin/products/{id}/edit', [AdminProductController::class, 'edit']);
$router->post('/admin/products/{id}', [AdminProductController::class, 'update']);
$router->post('/admin/products/{id}/delete', [AdminProductController::class, 'destroy']);
$router->post('/admin/products/{id}/images/delete', [AdminProductController::class, 'deleteImage']);

$router->get('/admin/categories', [AdminCategoryController::class, 'index']);
$router->post('/admin/categories', [AdminCategoryController::class, 'store']);
$router->post('/admin/categories/{id}', [AdminCategoryController::class, 'update']);
$router->post('/admin/categories/{id}/delete', [AdminCategoryController::class, 'destroy']);

$router->get('/admin/orders', [AdminOrderController::class, 'index']);
$router->get('/admin/orders/{id}', [AdminOrderController::class, 'show']);
$router->post('/admin/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
$router->post('/admin/orders/{id}/contact', [AdminOrderController::class, 'updateContact']);

$router->get('/admin/customers', [AdminCustomerController::class, 'index']);
$router->get('/admin/customers/{id}', [AdminCustomerController::class, 'show']);

$router->get('/admin/inventory', [AdminInventoryController::class, 'index']);
$router->post('/admin/inventory', [AdminInventoryController::class, 'update']);

$router->get('/admin/settings', [AdminSettingController::class, 'edit']);
$router->post('/admin/settings', [AdminSettingController::class, 'update']);
$router->post('/admin/account/password', [AdminSettingController::class, 'password']);

$router->dispatch(request_method(), current_path());
