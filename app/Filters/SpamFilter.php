<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * SpamFilter — Захист форм від ботів
 *
 * 1) Honeypot — приховане поле "website". Людина не бачить його (CSS display:none).
 *    Бот заповнює всі поля → ловиться.
 *
 * 2) Часова перевірка — при рендері форми генерується токен з timestamp.
 *    Якщо форма відправлена за < MIN_SECONDS секунд → бот.
 *
 * Підключається до POST-маршрутів login/register через Config/Filters.php
 */
class SpamFilter implements FilterInterface
{
    /**
     * Мінімальний час заповнення форми (секунди).
     * Людина не заповнить логін+пароль за 2 секунди.
     */
    private const MIN_SECONDS = 3;

    /**
     * Максимальний вік токена (секунди).
     * Форма старша за 1 годину — перезавантаж.
     */
    private const MAX_SECONDS = 3600;

    /**
     * Сіль для підпису токена. Береться з .env або fallback.
     */
    private function getSalt(): string
    {
        return env('antispam.salt', 'csh3adsh0t_sp4m_s4lt_2026');
    }

    // ------------------------------------------------------------------
    //  BEFORE — перевірка POST-запитів
    // ------------------------------------------------------------------
    public function before(RequestInterface $request, $arguments = null)
    {
        // Фільтруємо тільки POST
        if ($request->getMethod() !== 'post') {
            return;
        }

        $ip = $request->getIPAddress();

        // --- 1. Honeypot ---
        $honeypot = $request->getPost('website');
        if (! empty($honeypot)) {
            $this->logSpam($ip, 'honeypot', (string) current_url());
            return $this->blockResponse();
        }

        // --- 2. Часова перевірка ---
        $token = $request->getPost('_formtoken');

        if (empty($token)) {
            $this->logSpam($ip, 'missing_token', (string) current_url());
            return $this->blockResponse();
        }

        $timestamp = $this->validateToken($token);

        if ($timestamp === false) {
            $this->logSpam($ip, 'missing_token', (string) current_url());
            return $this->blockResponse();
        }

        $elapsed = time() - $timestamp;

        if ($elapsed < self::MIN_SECONDS) {
            $this->logSpam($ip, 'too_fast', (string) current_url());
            return $this->blockResponse();
        }

        if ($elapsed > self::MAX_SECONDS) {
            // Форма протухла — повертаємо назад з повідомленням
            return redirect()->back()->with('error', 'Форма застаріла. Спробуйте ще раз.');
        }

        // Все ок — пропускаємо далі
        return;
    }

    // ------------------------------------------------------------------
    //  AFTER — нічого не робимо
    // ------------------------------------------------------------------
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Не потрібно
    }

    // ------------------------------------------------------------------
    //  Генерація токена (викликається з хелпера)
    // ------------------------------------------------------------------
    public static function generateToken(): string
    {
        $filter    = new self();
        $timestamp = time();
        $signature = hash_hmac('sha256', (string) $timestamp, $filter->getSalt());

        // Формат: timestamp.signature (base64 для компактності)
        return base64_encode($timestamp . '.' . $signature);
    }

    // ------------------------------------------------------------------
    //  Валідація токена — повертає timestamp або false
    // ------------------------------------------------------------------
    private function validateToken(string $token): int|false
    {
        $decoded = base64_decode($token, true);
        if ($decoded === false) {
            return false;
        }

        $parts = explode('.', $decoded);
        if (count($parts) !== 2) {
            return false;
        }

        [$timestamp, $signature] = $parts;

        if (! ctype_digit($timestamp)) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp, $this->getSalt());

        if (! hash_equals($expected, $signature)) {
            return false;
        }

        return (int) $timestamp;
    }

    // ------------------------------------------------------------------
    //  Логування підозрілої спроби
    // ------------------------------------------------------------------
    private function logSpam(string $ip, string $reason, string $uri): void
    {
        try {
            $db = \Config\Database::connect();
            $db->table('spam_log')->insert([
                'ip_address' => $ip,
                'reason'     => $reason,
                'uri'        => mb_substr($uri, 0, 255),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Якщо таблиця ще не створена — не ламаємо сайт
            log_message('error', 'SpamFilter log failed: ' . $e->getMessage());
        }
    }

    // ------------------------------------------------------------------
    //  Відповідь для ботів — 403 без деталей
    // ------------------------------------------------------------------
    private function blockResponse(): ResponseInterface
    {
        $response = service('response');
        $response->setStatusCode(403);
        $response->setBody('Access denied.');
        return $response;
    }
}
