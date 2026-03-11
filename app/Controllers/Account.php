<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OrderModel;

class Account extends BaseController
{
    public function index()
    {
        $orderModel = new OrderModel();

        $userId     = session()->get('user_id');
        $privileges = $orderModel->getActivePrivileges($userId);
        $orders     = $orderModel->getByUser($userId);

        return view('layouts/main', [
            'page'       => 'account/dashboard',
            'title'      => 'Кабінет — CS Headshot',
            'privileges' => $privileges,
            'orders'     => array_slice($orders, 0, 5),
        ]);
    }

    public function edit()
    {
        $userModel = new UserModel();
        $user = $userModel->find(session()->get('user_id'));

        return view('layouts/main', [
            'page'  => 'account/edit',
            'title' => 'Редагування профілю — CS Headshot',
            'user'  => $user,
        ]);
    }

    public function update()
    {
        $userId = session()->get('user_id');

        $rules = [
            'username' => 'permit_empty|max_length[64]',
            'steam_id' => 'required|regex_match[/^STEAM_[0-5]:[01]:\d+$/]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();

        $data = [
            'username' => $this->request->getPost('username') ?: null,
            'steam_id' => $this->request->getPost('steam_id'),
        ];

        // Зміна пароля (опціонально)
        $newPassword = $this->request->getPost('new_password');
        if (! empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                return redirect()->back()->with('error', 'Пароль мінімум 6 символів');
            }
            $data['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        $userModel->update($userId, $data);

        // Оновити сесію
        session()->set([
            'user_name'  => $data['username'],
            'user_steam' => $data['steam_id'],
        ]);

        return redirect()->to('/account/edit')->with('success', 'Профіль оновлено');
    }

    public function purchases()
    {
        $orderModel = new OrderModel();
        $orders = $orderModel->getByUser(session()->get('user_id'));

        return view('layouts/main', [
            'page'   => 'account/purchases',
            'title'  => 'Історія покупок — CS Headshot',
            'orders' => $orders,
        ]);
    }
}
