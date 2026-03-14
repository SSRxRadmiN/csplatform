<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;

class Orders extends BaseController
{
    public function index()
    {
        $orderModel = new OrderModel();

        $status = $this->request->getGet('status');

        $builder = $orderModel
            ->select('orders.*, users.username, users.email')
            ->join('users', 'users.id = orders.user_id', 'left');

        if ($status && in_array($status, ['pending', 'paid', 'delivered', 'failed', 'expired'])) {
            $builder->where('orders.status', $status);
        }

        $orders = $builder
            ->orderBy('orders.created_at', 'DESC')
            ->findAll();

        return view('layouts/main', [
            'page'          => 'admin/orders/index',
            'title'         => 'Замовлення — Адмін',
            'orders'        => $orders,
            'currentStatus' => $status,
        ]);
    }

    public function show(int $id)
    {
        $orderModel = new OrderModel();

        $order = $orderModel
            ->select('orders.*, users.username, users.email, users.steam_id as user_steam')
            ->join('users', 'users.id = orders.user_id', 'left')
            ->find($id);

        if (! $order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('layouts/main', [
            'page'  => 'admin/orders/show',
            'title' => 'Замовлення #' . $id . ' — Адмін',
            'order' => $order,
        ]);
    }
}
