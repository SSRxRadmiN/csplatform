<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/account');
        }

        return view('layouts/main', [
            'page'  => 'auth/login',
            'title' => 'Вхід — CS Headshot',
        ]);
    }

    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($this->request->getPost('email'));

        if (! $user || ! password_verify($this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Невірний email або пароль');
        }

        if (! $user['is_active']) {
            return redirect()->back()->with('error', 'Акаунт деактивовано');
        }

        // Зберігаємо в сесію
        session()->set([
            'user_id'   => $user['id'],
            'user_email' => $user['email'],
            'user_name'  => $user['username'],
            'user_role'  => $user['role'],
            'user_steam' => $user['steam_id'],
            'lang'       => $user['language'],
        ]);

        $userModel->updateLastLogin($user['id']);

        // Редірект на попередню сторінку або кабінет
        $redirect = session()->getFlashdata('redirect_url') ?? '/account';
        return redirect()->to($redirect)->with('success', 'Ласкаво просимо!');
    }

    public function register()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/account');
        }

        return view('layouts/main', [
            'page'  => 'auth/register',
            'title' => 'Реєстрація — CS Headshot',
        ]);
    }

    public function attemptRegister()
    {
        $rules = [
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
            'steam_id'         => 'required|regex_match[/^STEAM_[0-5]:[01]:\d+$/]',
        ];

        $messages = [
            'email' => [
                'is_unique' => 'Цей email вже зареєстрований',
            ],
            'password_confirm' => [
                'matches' => 'Паролі не співпадають',
            ],
            'steam_id' => [
                'regex_match' => 'Невірний формат Steam ID (напр. STEAM_0:1:12345)',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();

        $userId = $userModel->insert([
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'steam_id' => $this->request->getPost('steam_id'),
            'username' => $this->request->getPost('username') ?: null,
            'role'     => 'player',
            'language' => session()->get('lang') ?? 'ua',
        ]);

        if (! $userId) {
            return redirect()->back()->withInput()->with('error', 'Помилка реєстрації');
        }

        $user = $userModel->find($userId);

        // Автоматичний логін після реєстрації
        session()->set([
            'user_id'    => $user['id'],
            'user_email' => $user['email'],
            'user_name'  => $user['username'],
            'user_role'  => $user['role'],
            'user_steam' => $user['steam_id'],
            'lang'       => $user['language'],
        ]);

        return redirect()->to('/account')->with('success', 'Реєстрація успішна!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('success', 'Ви вийшли з акаунту');
    }
}
