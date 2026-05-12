<?php

namespace App\Controllers;

use App\Models\ServerModel;
use CodeIgniter\Controller;

/**
 * ApiServer — AJAX-ендпоінти для модалок на головній сторінці.
 *
 * Маршрут (Routes.php):
 *   $routes->get('api/server/(:num)/players', 'ApiServer::players/$1');
 *
 * Логіка:
 *   1. Тягне список гравців з VPS API (?action=players) — A2S_PLAYER через UDP
 *   2. Тягне зі своєї БД активні VIP/Admin замовлення з нікнеймами покупців
 *   3. Зливає: на кожному гравці прапорець is_vip=true якщо нік збігається
 *
 * Кешування — не робимо. Кожен клік = свіжий запит до сервера (~300ms).
 */
class ApiServer extends Controller
{
    public function players(int $serverId)
    {
        // CSRF не потрібен — GET, читання
        // Auth теж не потрібен — це публічна інфа (як на gam1ngcs)

        $serverModel = new ServerModel();
        $server = $serverModel->find($serverId);

        if (! $server || empty($server['is_active'])) {
            return $this->jsonError('Server not found', 404);
        }

        $creds = $serverModel->getApiCredentials($serverId);
        if (empty($creds['url']) || empty($creds['token'])) {
            return $this->jsonError('Server API not configured', 503);
        }

        // === Запит #1: список гравців з VPS API ===
        $url = rtrim($creds['url'], '/') . '?action=players&token=' . urlencode($creds['token']);
        $apiResult = $this->fetchJson($url, 5);

        if ($apiResult === null) {
            return $this->jsonError('Game server unavailable', 503);
        }

        if (! ($apiResult['is_online'] ?? false)) {
            return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setJSON([
                    'success'    => true,
                    'is_online'  => false,
                    'count'      => 0,
                    'players'    => [],
                    'message'    => $apiResult['error'] ?? 'Server offline',
                ]);
        }

        $players = $apiResult['players'] ?? [];

        // === Запит #2: активні VIP/Admin нікнейми з нашої БД ===
        $vipNicks = $this->getActiveVipNicknames();

        // === Зливаємо ===
        foreach ($players as &$p) {
            $nickLower    = mb_strtolower($p['name'], 'UTF-8');
            $p['is_vip']  = isset($vipNicks[$nickLower]);
            // Можна додати тип привілею якщо потрібно:
            // $p['privilege'] = $vipNicks[$nickLower] ?? null;
        }
        unset($p);

        return $this->response
            ->setContentType('application/json')
            ->setJSON([
                'success'   => true,
                'is_online' => true,
                'count'     => count($players),
                'players'   => $players,
                'server'    => [
                    'name' => $server['name'],
                    'ip'   => $server['ip'] . ':' . $server['port'],
                ],
            ]);
    }

    /**
     * Активні VIP/Admin нікнейми для маркера в списку гравців.
     *
     * Беремо username гравця з users (а не з orders), бо так чесніше:
     * нік в `orders` міг змінитись після покупки.
     *
     * Повертає: [ lowercase_nick => privilege_slug ]
     */
    private function getActiveVipNicknames(): array
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $rows = $db->table('orders')
            ->select('users.username, categories.slug as cat_slug')
            ->join('users',      'users.id = orders.user_id',         'inner')
            ->join('products',   'products.id = orders.product_id',   'left')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->where('orders.status', 'delivered')
            ->whereIn('categories.slug', ['vip', 'admin'])
            ->groupStart()
                ->where('orders.expires_at IS NULL')
                ->orWhere('orders.expires_at >', $now)
            ->groupEnd()
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $r) {
            $nick = mb_strtolower($r['username'] ?? '', 'UTF-8');
            if ($nick !== '') {
                $map[$nick] = $r['cat_slug'] ?? 'vip';
            }
        }

        return $map;
    }

    // ─────────────────────────────────────────────────────────────────
    // Хелпери
    // ─────────────────────────────────────────────────────────────────

    /**
     * GET-запит з таймаутом, повертає декодований JSON або null при помилці.
     */
    private function fetchJson(string $url, int $timeoutSec = 5): ?array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeoutSec,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => false,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($body === false || $code >= 400) {
            log_message('error', '[ApiServer] fetchJson failed: code={c}, err={e}, url={u}', [
                'c' => $code, 'e' => $err, 'u' => $url,
            ]);
            return null;
        }

        $data = json_decode($body, true);
        return is_array($data) ? $data : null;
    }

    private function jsonError(string $message, int $code = 400)
    {
        return $this->response
            ->setStatusCode($code)
            ->setContentType('application/json')
            ->setJSON([
                'success' => false,
                'error'   => $message,
            ]);
    }
}
