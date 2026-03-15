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

    /**
     * Оновити статус замовлення
     */
    public function updateStatus(int $id)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->find($id);

        if (! $order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $newStatus = $this->request->getPost('status');
        $validStatuses = ['pending', 'paid', 'delivered', 'failed', 'expired'];

        if (! $newStatus || ! in_array($newStatus, $validStatuses)) {
            return redirect()->to('/admin/orders/' . $id)->with('error', 'Невірний статус');
        }

        $updateData = ['status' => $newStatus];

        // Автозаповнення дат при зміні статусу
        if ($newStatus === 'paid' && empty($order['paid_at'])) {
            $updateData['paid_at'] = date('Y-m-d H:i:s');
        }

        if ($newStatus === 'delivered' && empty($order['delivered_at'])) {
            $updateData['delivered_at'] = date('Y-m-d H:i:s');
            // Встановити expires_at якщо є duration_days
            if (! empty($order['duration_days']) && (int) $order['duration_days'] > 0 && empty($order['expires_at'])) {
                $updateData['expires_at'] = date('Y-m-d H:i:s', strtotime('+' . (int) $order['duration_days'] . ' days'));
            }
        }

        // Додати запис в delivery_log
        $logEntry = date('Y-m-d H:i:s') . ' | Admin: статус ' . $order['status'] . ' → ' . $newStatus;
        $existingLog = $order['delivery_log'] ?? '';
        $updateData['delivery_log'] = $existingLog ? $existingLog . "\n" . $logEntry : $logEntry;

        $orderModel->update($id, $updateData);

        return redirect()->to('/admin/orders/' . $id)->with('success', 'Статус оновлено на "' . $newStatus . '"');
    }

    /**
     * Оновити поля замовлення вручну
     */
    public function update(int $id)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->find($id);

        if (! $order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $updateData = [];

        $steam_id = $this->request->getPost('steam_id');
        if ($steam_id !== null) {
            $updateData['steam_id'] = trim($steam_id);
        }

        $delivery_log = $this->request->getPost('delivery_log');
        if ($delivery_log !== null) {
            $updateData['delivery_log'] = trim($delivery_log);
        }

        $expires_at = $this->request->getPost('expires_at');
        if ($expires_at !== null) {
            $updateData['expires_at'] = $expires_at ?: null;
        }

        if (! empty($updateData)) {
            $orderModel->update($id, $updateData);
            return redirect()->to('/admin/orders/' . $id)->with('success', 'Замовлення оновлено');
        }

        return redirect()->to('/admin/orders/' . $id);
    }
}
