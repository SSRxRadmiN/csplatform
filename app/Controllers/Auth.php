<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    /** Max failed attempts per window */
    private const MAX_ATTEMPTS = 5;

    /** Window in seconds (15 min) */
    private const WINDOW = 900;

    /** Lockout in seconds (30 min) */
    private const LOCKOUT = 1800;

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

        $email = $this->request->getPost('email');
        $ip    = $this->request->getIPAddress();

        // --- Rate Limiting ---
        $blocked = $this->checkThrottle($ip);
        if ($blocked) {
            return redirect()->back()->withInput()->with('error', $blocked);
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (! $user || ! password_verify($this->request->getPost('password'), $user['password'])) {
            $this->logAttempt($ip, $email, false);
            return redirect()->back()->withInput()->with('error', lang('Auth.error_credentials') ?: 'Невірний email або пароль');
        }

        if (! $user['is_active']) {
            return redirect()->back()->with('error', lang('Auth.error_inactive') ?: 'Акаунт деактивовано');
        }

        // Логуємо успішну спробу
        $this->logAttempt($ip, $email, true);

        // Регенерація session ID
        session()->regenerate();

        session()->set([
            'user_id'   => $user['id'],
            'user_email' => $user['email'],
            'user_name'  => $user['username'],
            'user_role'  => $user['role'],
            'user_steam' => $user['steam_id'],
            'lang'       => $user['language'],
        ]);

        $userModel->updateLastLogin($user['id']);

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
            'steam_id'         => 'required|regex_match[/^STEAM_[0-5]:[01]:\d+$/]|is_unique[users.steam_id]',
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
                'is_unique'   => 'Цей Steam ID вже зареєстрований',
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

        session()->regenerate();

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

    // ------------------------------------------------------------------
    //  Rate Limiting
    // ------------------------------------------------------------------

    private function checkThrottle(string $ip): ?string
    {
        try {
            $db = \Config\Database::connect();

            // Очищаємо старі записи
            $cutoff = date('Y-m-d H:i:s', time() - self::LOCKOUT);
            $db->table('login_attempts')
                ->where('attempted_at <', $cutoff)
                ->delete();

            // Рахуємо невдалі спроби за вікно
            $windowStart = date('Y-m-d H:i:s', time() - self::WINDOW);
            $attempts = $db->table('login_attempts')
                ->where('ip_address', $ip)
                ->where('attempted_at >=', $windowStart)
                ->where('success', 0)
                ->countAllResults();

            if ($attempts >= self::MAX_ATTEMPTS) {
                $minutes = (int) ceil(self::LOCKOUT / 60);

                $lang = session()->get('lang') ?? 'ua';
                return $lang === 'en'
                    ? "Too many login attempts. Try again in {$minutes} minutes."
                    : "Забагато спроб входу. Спробуйте через {$minutes} хвилин.";
            }
        } catch (\Throwable $e) {
            log_message('error', 'Throttle check failed: ' . $e->getMessage());
        }

        return null;
    }

    private function logAttempt(string $ip, string $email, bool $success): void
    {
        try {
            $db = \Config\Database::connect();
            $db->table('login_attempts')->insert([
                'ip_address'   => $ip,
                'email'        => $email,
                'success'      => $success ? 1 : 0,
                'attempted_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Login attempt log failed: ' . $e->getMessage());
        }
    }
}
