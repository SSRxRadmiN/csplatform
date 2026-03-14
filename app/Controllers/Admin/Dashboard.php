<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use App\Models\SettingModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $orderModel   = new OrderModel();
        $productModel = new ProductModel();
        $userModel    = new UserModel();

        // Статистика
        $totalOrders   = $orderModel->countAllResults(false);
        $totalRevenue  = $orderModel->where('status', 'delivered')->selectSum('amount')->first()['amount'] ?? 0;
        $totalUsers    = $userModel->countAllResults(false);
        $totalProducts = $productModel->countAllResults(false);

        // Останні 10 замовлень
        $recentOrders = $orderModel
            ->select('orders.*, users.username, users.email')
            ->join('users', 'users.id = orders.user_id', 'left')
            ->orderBy('orders.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        return view('layouts/main', [
            'page'          => 'admin/dashboard',
            'title'         => 'Адмін-панель — CS Headshot',
            'totalOrders'   => $totalOrders,
            'totalRevenue'  => $totalRevenue,
            'totalUsers'    => $totalUsers,
            'totalProducts' => $totalProducts,
            'recentOrders'  => $recentOrders,
        ]);
    }
}
