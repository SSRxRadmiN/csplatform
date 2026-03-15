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
        $products   = $productModel->getByServerWithCategory(1);
        $categories = $categoryModel->getActive();

        return view('layouts/main', [
            'page'       => 'shop/index',
            'title'      => 'Магазин — CS Headshot',
            'server'     => $server,
            'products'   => $products,
            'categories' => $categories,
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

        $server = $serverModel->getWithStats($product['server_id']);

        return view('layouts/main', [
            'page'    => 'shop/show',
            'title'   => $product['name_ua'] . ' — CS Headshot',
            'product' => $product,
            'server'  => $server,
        ]);
    }
}
