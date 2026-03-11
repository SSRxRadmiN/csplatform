<?php

namespace App\Controllers;

use App\Models\ServerModel;
use App\Models\ProductModel;

class Shop extends BaseController
{
    public function index()
    {
        $serverModel  = new ServerModel();
        $productModel = new ProductModel();

        $server   = $serverModel->getWithStats(1);
        $products = $productModel->getByServer(1);

        return view('layouts/main', [
            'page'     => 'shop/index',
            'title'    => 'Магазин — CS Headshot',
            'server'   => $server,
            'products' => $products,
        ]);
    }

    public function show(int $id)
    {
        $productModel = new ProductModel();
        $serverModel  = new ServerModel();

        $product = $productModel->find($id);

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
