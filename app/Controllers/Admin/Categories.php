<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Categories extends BaseController
{
    public function index()
    {
        $categoryModel = new CategoryModel();

        $categories = $categoryModel
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        return view('layouts/main', [
            'page'       => 'admin/categories/index',
            'title'      => 'Категорії — Адмін',
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return view('layouts/main', [
            'page'     => 'admin/categories/form',
            'title'    => 'Нова категорія — Адмін',
            'category' => null,
        ]);
    }

    public function store()
    {
        $categoryModel = new CategoryModel();

        $data = $this->request->getPost([
            'slug', 'name_ua', 'name_en', 'icon',
            'color', 'sort_order', 'is_active',
        ]);

        $data['is_active']  = $data['is_active'] ?? 0;
        $data['sort_order'] = $data['sort_order'] ?: 0;

        if (! $categoryModel->insert($data)) {
            return redirect()->back()->withInput()
                ->with('errors', $categoryModel->errors());
        }

        return redirect()->to('/admin/categories')
            ->with('success', 'Категорію створено');
    }

    public function edit(int $id)
    {
        $categoryModel = new CategoryModel();

        $category = $categoryModel->find($id);
        if (! $category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('layouts/main', [
            'page'     => 'admin/categories/form',
            'title'    => 'Редагувати: ' . $category['name_ua'] . ' — Адмін',
            'category' => $category,
        ]);
    }

    public function update(int $id)
    {
        $categoryModel = new CategoryModel();

        $category = $categoryModel->find($id);
        if (! $category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = $this->request->getPost([
            'slug', 'name_ua', 'name_en', 'icon',
            'color', 'sort_order', 'is_active',
        ]);

        $data['is_active']  = $data['is_active'] ?? 0;
        $data['sort_order'] = $data['sort_order'] ?: 0;

        if (! $categoryModel->update($id, $data)) {
            return redirect()->back()->withInput()
                ->with('errors', $categoryModel->errors());
        }

        return redirect()->to('/admin/categories')
            ->with('success', 'Категорію оновлено');
    }

    public function delete(int $id)
    {
        $categoryModel = new CategoryModel();

        $category = $categoryModel->find($id);
        if (! $category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $categoryModel->update($id, ['is_active' => 0]);

        return redirect()->to('/admin/categories')
            ->with('success', 'Категорію деактивовано');
    }
}
