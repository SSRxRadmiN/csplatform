<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * LoginThrottleFilter — захист від brute force атак на логін
 *
 * Блокує IP після MAX_ATTEMPTS невдалих спроб за WINDOW секунд.
 * Зберігає спроби в таблиці login_attempts.
 *
 * Підключається до POST /login в Config/Filters.php
 */
class LoginThrottleFilter implements FilterInterface
{
    /** Максимум спроб за вікно */
    private const MAX_ATTEMPTS = 5;

    /** Вікно в секундах (15 хвилин) */
    private const WINDOW = 900;

    /** Час блокування в секундах (30 хвилин) */
    private const LOCKOUT = 1800;

    public function before(RequestInterface $request, $arguments = null)
    {
        // Тільки POST
        if ($request->getMethod() !== 'post') {
            return;
        }

        $ip = $request->getIPAddress();
        $db = \Config\Database::connect();

        // Очищаємо старі записи (старші за LOCKOUT)
        $cutoff = date('Y-m-d H:i:s', time() - self::LOCKOUT);
        $db->table('login_attempts')
            ->where('attempted_at <', $cutoff)
            ->delete();

        // Рахуємо невдалі спроби за останні WINDOW секунд
        $windowStart = date('Y-m-d H:i:s', time() - self::WINDOW);
        $attempts = $db->table('login_attempts')
            ->where('ip_address', $ip)
            ->where('attempted_at >=', $windowStart)
            ->where('success', 0)
            ->countAllResults();

        if ($attempts >= self::MAX_ATTEMPTS) {
            // Перевіряємо час останньої спроби для lockout
            $lastAttempt = $db->table('login_attempts')
                ->where('ip_address', $ip)
                ->where('success', 0)
                ->orderBy('attempted_at', 'DESC')
                ->get(1)
                ->getRowArray();

            if ($lastAttempt) {
                $lockoutEnd = strtotime($lastAttempt['attempted_at']) + self::LOCKOUT;
                $remaining = $lockoutEnd - time();

                if ($remaining > 0) {
                    $minutes = (int) ceil($remaining / 60);
                    $lang = session()->get('lang') ?? 'ua';
                    $msg = $lang === 'en'
                        ? "Too many login attempts. Please try again in {$minutes} minutes."
                        : "Забагато спроб входу. Спробуйте через {$minutes} хвилин.";

                    return redirect()->back()->with('error', $msg);
                }
            }
        }

        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }

    /**
     * Записати спробу входу (викликається з Auth контролера)
     */
    public static function logAttempt(string $ip, string $email, bool $success): void
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
            log_message('error', 'LoginThrottle log failed: ' . $e->getMessage());
        }
    }
}
