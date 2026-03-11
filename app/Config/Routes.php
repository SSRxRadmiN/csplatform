<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================
// Публічні сторінки
// ============================================
$routes->get('/', 'Home::index');
$routes->get('shop', 'Shop::index');
$routes->get('shop/(:num)', 'Shop::show/$1');
$routes->get('lang/(:segment)', 'Home::lang/$1');

// ============================================
// Авторизація
// ============================================
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::attemptRegister');
$routes->get('logout', 'Auth::logout');

// ============================================
// Кабінет гравця (потрібна авторизація)
// ============================================
$routes->group('account', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Account::index');
    $routes->get('edit', 'Account::edit');
    $routes->post('edit', 'Account::update');
    $routes->get('purchases', 'Account::purchases');
});

// ============================================
// Покупка (потрібна авторизація)
// ============================================
$routes->get('buy/(:num)', 'Buy::index/$1', ['filter' => 'auth']);
$routes->post('buy/(:num)', 'Buy::process/$1', ['filter' => 'auth']);
$routes->get('buy/success', 'Buy::success', ['filter' => 'auth']);
$routes->get('buy/failed', 'Buy::failed', ['filter' => 'auth']);

// ============================================
// Payment webhook (без CSRF, без auth!)
// ============================================
$routes->post('payment/callback', 'Payment::callback');

// ============================================
// Адмін-панель (потрібна авторизація + роль admin)
// ============================================
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index');

    $routes->get('products', 'Admin\Products::index');
    $routes->get('products/create', 'Admin\Products::create');
    $routes->post('products/create', 'Admin\Products::store');
    $routes->get('products/edit/(:num)', 'Admin\Products::edit/$1');
    $routes->post('products/edit/(:num)', 'Admin\Products::update/$1');
    $routes->post('products/delete/(:num)', 'Admin\Products::delete/$1');

    $routes->get('orders', 'Admin\Orders::index');
    $routes->get('orders/(:num)', 'Admin\Orders::show/$1');

    $routes->get('users', 'Admin\Users::index');

    $routes->get('settings', 'Admin\Settings::index');
    $routes->post('settings', 'Admin\Settings::update');
});

// Buy
    $routes->get('buy/(:num)',          'Buy::index/$1',   ['filter' => 'auth']);
    $routes->post('buy/(:num)/process', 'Buy::process/$1', ['filter' => 'auth']);
    $routes->get('buy/success',         'Buy::success');
    $routes->get('buy/failed',          'Buy::failed');

    // Payment webhook
    $routes->post('payment/callback',   'Payment::callback');