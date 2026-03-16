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

        // Отримуємо наш сервер (id=1) з статистикою
        $server = $serverModel->getWithStats(1);

        // Отримуємо активні товари з категоріями
        $products = $productModel->getByServerWithCategory(1);

        return view('layouts/main', [
            'page'     => 'home/index',
            'title'    => 'CS Headshot — Привілеї для CS 1.6',
            'server'   => $server,
            'products' => $products,
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

            // Якщо юзер залогінений — зберегти в БД
            if (session()->get('user_id')) {
                $userModel = new \App\Models\UserModel();
                $userModel->update(session()->get('user_id'), ['language' => $locale]);
            }
        }

        return redirect()->back();
    }
}
