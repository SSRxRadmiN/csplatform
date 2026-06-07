<?php

namespace App\Controllers;

use App\Models\ServerModel;
use App\Models\ProductModel;
use App\Models\CategoryModel;

class Shop extends BaseController
{
    public function index()
    {
        $serverModel   = new ServerModel();
        $productModel  = new ProductModel();
        $categoryModel = new CategoryModel();

        $server     = $serverModel->getWithStats(1);
        $products   = $productModel->getAllWithCategory();
        $categories = $categoryModel->getActive();

        return view('layouts/main', [
            'page'            => 'shop/index',
            'title'           => 'Магазин — CS Headshot',
            'metaTitle'       => 'Магазин привілегій CS 1.6 — CS Headshot',
            'metaDescription' => 'VIP статус, моделі гравців, розбан, адмін-права — всі привілеї для сервера Реальні Кабани CS 1.6.',
            'server'          => $server,
            'products'        => $products,
            'categories'      => $categories,
        ]);
    }

    public function show(int $id)
    {
        $productModel = new ProductModel();
        $serverModel  = new ServerModel();

        $product = $productModel
            ->select('products.*, categories.slug as cat_slug, categories.name_ua as cat_name_ua, categories.name_en as cat_name_en, categories.icon as cat_icon, categories.color as cat_color')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->where('products.id', $id)
            ->first();

        if (! $product || ! $product['is_active']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $server = null; // server_id більше не зберігається в товарі — гравець обирає сам при купівлі

        // Усі сервери для вибору на картці товару (товари однакові для обох)
        $servers = $serverModel->findAll();

        $pName = $product['name_ua'];

        return view('layouts/main', [
            'page'            => 'shop/show',
            'title'           => $pName . ' — CS Headshot',
            'metaTitle'       => $pName . ' — Купити на CS Headshot',
            'metaDescription' => $pName . ' для CS 1.6 сервера Реальні Кабани. Автоматична активація після оплати.',
            'product'         => $product,
            'server'          => $server,
            'servers'         => $servers,
        ]);
    }
}
