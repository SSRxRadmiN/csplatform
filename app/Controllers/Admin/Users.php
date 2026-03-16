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

    public function edit(int $id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('layouts/main', [
            'page'     => 'admin/users/form',
            'title'    => 'Редагувати: ' . ($user['username'] ?? $user['email']) . ' — Адмін',
            'editUser' => $user,
        ]);
    }

    public function update(int $id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $updateData = [];

        // Username
        $username = $this->request->getPost('username');
        if ($username !== null) {
            $updateData['username'] = trim($username) ?: null;
        }

        // Email (з перевіркою унікальності)
        $email = $this->request->getPost('email');
        if ($email !== null && trim($email) !== $user['email']) {
            $existing = $userModel->where('email', trim($email))
                                  ->where('id !=', $id)
                                  ->first();
            if ($existing) {
                return redirect()->back()->withInput()
                    ->with('error', 'Цей email вже зайнятий іншим користувачем');
            }
            $updateData['email'] = trim($email);
        }

        // Steam ID
        $steam_id = $this->request->getPost('steam_id');
        if ($steam_id !== null) {
            $updateData['steam_id'] = trim($steam_id) ?: null;
        }

        // Role
        $role = $this->request->getPost('role');
        $validRoles = ['player', 'admin', 'owner', 'moderator'];
        if ($role && in_array($role, $validRoles)) {
            // Захист: не можна зняти admin з себе
            $currentUserId = (int) session()->get('user_id');
            if ($id === $currentUserId && $role !== 'admin') {
                return redirect()->back()->withInput()
                    ->with('error', 'Не можна змінити роль самому собі');
            }
            $updateData['role'] = $role;
        }

        // is_active (блокування)
        $is_active = $this->request->getPost('is_active');
        if ($is_active !== null) {
            // Захист: не можна заблокувати себе
            $currentUserId = (int) session()->get('user_id');
            if ($id === $currentUserId && (int) $is_active === 0) {
                return redirect()->back()->withInput()
                    ->with('error', 'Не можна заблокувати самого себе');
            }
            $updateData['is_active'] = (int) $is_active;
        }

        // Новий пароль (опціонально)
        $newPassword = $this->request->getPost('new_password');
        if (! empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                return redirect()->back()->withInput()
                    ->with('error', 'Пароль повинен містити мінімум 6 символів');
            }
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        if (! empty($updateData)) {
            // Вимикаємо валідацію бо оновлюємо вибірково (без required password)
            $userModel->setValidationRules([]);
            $userModel->update($id, $updateData);
            return redirect()->to('/admin/users')->with('success', 'Користувача оновлено');
        }

        return redirect()->to('/admin/users');
    }
}
