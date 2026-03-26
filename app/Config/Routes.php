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
$routes->get('stats', 'Stats::index', ['filter' => 'auth']);
$routes->get('stats/player/(:num)', 'Stats::player/$1', ['filter' => 'auth']);
$routes->get('bans', 'Bans::index');
$routes->get('faq', 'Pages::faq');
$routes->get('privacy', 'Pages::privacy');
$routes->get('sitemap.xml', 'Sitemap::index');
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
    $routes->post('orders/(:num)/status', 'Admin\Orders::updateStatus/$1');
    $routes->post('orders/(:num)/update', 'Admin\Orders::update/$1');

    $routes->get('categories', 'Admin\Categories::index');
    $routes->get('categories/create', 'Admin\Categories::create');
    $routes->post('categories/create', 'Admin\Categories::store');
    $routes->get('categories/edit/(:num)', 'Admin\Categories::edit/$1');
    $routes->post('categories/edit/(:num)', 'Admin\Categories::update/$1');
    $routes->post('categories/delete/(:num)', 'Admin\Categories::delete/$1');

    $routes->get('users', 'Admin\Users::index');
    $routes->get('users/edit/(:num)', 'Admin\Users::edit/$1');
    $routes->post('users/edit/(:num)', 'Admin\Users::update/$1');

    $routes->get('settings', 'Admin\Settings::index');
    $routes->post('settings', 'Admin\Settings::update');

    $routes->get('privileges', 'Admin\Privileges::index');
    $routes->post('privileges/add', 'Admin\Privileges::add');
    $routes->post('privileges/update/(:num)', 'Admin\Privileges::update/$1');
    $routes->post('privileges/delete/(:num)', 'Admin\Privileges::delete/$1');
});

// ============================================
// Cron tasks (POST з ключем в header/body, без CSRF)
// ============================================
$routes->post('cron/expire', 'Cron::expire');
$routes->post('cron/serverstats', 'Cron::serverstats');
$routes->get('cron/health', 'Cron::health');
