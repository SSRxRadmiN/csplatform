<?php

namespace App\Controllers;

use App\Models\ServerModel;
use App\Models\ProductModel;

class Home extends BaseController
{
    public function index()
    {
        $serverModel  = new ServerModel();
        $productModel = new ProductModel();

        $server   = $serverModel->getWithStats(1);
        $products = $productModel->getByServerWithCategory(1);

        // Групуємо товари по секціях для нового layout
        $vipProducts     = [];
        $modelProductsF  = [];
        $modelProductsM  = [];
        $serviceProducts = [];

        foreach ($products as $p) {
            $slug = $p['cat_slug'] ?? '';
            if ($slug === 'vip') {
                $vipProducts[] = $p;
            } elseif ($slug === 'models') {
                $gender = $p['gender'] ?? 'male';
                if ($gender === 'female') {
                    $modelProductsF[] = $p;
                } else {
                    $modelProductsM[] = $p;
                }
            } elseif (in_array($slug, ['unban', 'xp', 'other', 'admin', 'services'])) {
                $serviceProducts[] = $p;
            }
        }

        return view('layouts/main', [
            'page'            => 'home/index',
            'title'           => 'CS Headshot — Привілеї для CS 1.6',
            'server'          => $server,
            'products'        => $products,
            'vipProducts'     => $vipProducts,
            'modelProductsF'  => $modelProductsF,
            'modelProductsM'  => $modelProductsM,
            'serviceProducts' => $serviceProducts,
        ]);
    }

    /**
     * Перемикання мови
     */
    public function lang(string $locale)
    {
        $allowed = ['ua', 'en'];
        if (in_array($locale, $allowed)) {
            session()->set('lang', $locale);

            if (session()->get('user_id')) {
                $userModel = new \App\Models\UserModel();
                $userModel->update(session()->get('user_id'), ['language' => $locale]);
            }
        }

        return redirect()->back();
    }
}
