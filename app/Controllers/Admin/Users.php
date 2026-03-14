<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();

        $users = $userModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('layouts/main', [
            'page'  => 'admin/users/index',
            'title' => 'Користувачі — Адмін',
            'users' => $users,
        ]);
    }
}
