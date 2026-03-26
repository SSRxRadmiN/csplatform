<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\ServerModel;
use App\Models\CategoryModel;

class Products extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();

        $products = $productModel
            ->select('products.*, servers.name as server_name')
            ->join('servers', 'servers.id = products.server_id', 'left')
            ->orderBy('products.sort_order', 'ASC')
            ->findAll();

        return view('layouts/main', [
            'page'     => 'admin/products/index',
            'title'    => 'Товари — Адмін',
            'products' => $products,
        ]);
    }

    public function create()
    {
        $serverModel   = new ServerModel();
        $categoryModel = new CategoryModel();

        return view('layouts/main', [
            'page'       => 'admin/products/form',
            'title'      => 'Новий товар — Адмін',
            'product'    => null,
            'servers'    => $serverModel->findAll(),
            'categories' => $categoryModel->where('is_active', 1)->orderBy('sort_order')->findAll(),
        ]);
    }

    public function store()
    {
        $productModel = new ProductModel();

        $data = $this->request->getPost([
            'server_id', 'category_id', 'name_ua', 'name_en',
            'description_ua', 'description_en',
            'price', 'duration_days',
            'amx_access', 'amx_flags',
            'model_te', 'model_ct', 'gender',
            'image_url', 'sort_order', 'is_active',
        ]);

        $data['is_active']  = $data['is_active'] ?? 0;
        $data['sort_order'] = $data['sort_order'] ?: 0;

        if (! $productModel->insert($data)) {
            return redirect()->back()->withInput()
                ->with('errors', $productModel->errors());
        }

        return redirect()->to('/admin/products')
            ->with('success', 'Товар створено');
    }

    public function edit(int $id)
    {
        $productModel  = new ProductModel();
        $serverModel   = new ServerModel();
        $categoryModel = new CategoryModel();

        $product = $productModel->find($id);
        if (! $product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('layouts/main', [
            'page'       => 'admin/products/form',
            'title'      => 'Редагувати: ' . $product['name_ua'] . ' — Адмін',
            'product'    => $product,
            'servers'    => $serverModel->findAll(),
            'categories' => $categoryModel->where('is_active', 1)->orderBy('sort_order')->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $productModel = new ProductModel();

        $product = $productModel->find($id);
        if (! $product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = $this->request->getPost([
            'server_id', 'category_id', 'name_ua', 'name_en',
            'description_ua', 'description_en',
            'price', 'duration_days',
            'amx_access', 'amx_flags',
            'model_te', 'model_ct', 'gender',
            'image_url', 'sort_order', 'is_active',
        ]);

        $data['is_active']  = $data['is_active'] ?? 0;
        $data['sort_order'] = $data['sort_order'] ?: 0;

        if (! $productModel->update($id, $data)) {
            return redirect()->back()->withInput()
                ->with('errors', $productModel->errors());
        }

        return redirect()->to('/admin/products')
            ->with('success', 'Товар оновлено');
    }

    public function delete(int $id)
    {
        $productModel = new ProductModel();

        $product = $productModel->find($id);
        if (! $product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $productModel->update($id, ['is_active' => 0]);

        return redirect()->to('/admin/products')
            ->with('success', 'Товар деактивовано');
    }
}
