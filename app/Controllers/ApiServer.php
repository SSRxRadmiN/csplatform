<?php

namespace App\Controllers;

use App\Libraries\ServerQuery;
use App\Models\ServerModel;
use CodeIgniter\Controller;

/**
 * ApiServer — AJAX-ендпоінти для модалок на головній сторінці.
 *
 * v2: повністю переписано на ПРЯМЕ UDP до HLDS, без VPS API.
 *
 * Маршрут (Routes.php):
 *   $routes->get('api/server/(:num)/players', 'ApiServer::players/$1');
 *
 * Логіка:
 *   1. ServerQuery::getPlayers() — пряме UDP A2S_PLAYER до HLDS
 *   2. Зі своєї БД (s-host) забирає активні VIP/Admin нікнейми
 *   3. Зливає: на кожному гравці прапорець is_vip=true якщо нік збігається
 *
 * Кешування — не робимо. Кожен клік = свіжий UDP-запит (~50-150мс).
 */
class ApiServer extends Controller
{
    public function players(int $serverId)
    {
        $serverModel = new ServerModel();
        $server = $serverModel->find($serverId);

        if (! $server || empty($server['is_active'])) {
            return $this->jsonError('Server not found', 404);
        }

        $ip   = $server['ip'];
        $port = (int) $server['port'];

        // === Пряме UDP до ігрового сервера ===
        $playersRaw = ServerQuery::getPlayers($ip, $port);

        if ($playersRaw === null) {
            return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setJSON([
                    'success'   => true,
                    'is_online' => false,
                    'count'     => 0,
                    'players'   => [],
                    'message'   => 'Server did not respond',
                ]);
        }

        // === VIP-маркер: список активних VIP-нікнеймів з нашої БД ===
        $vipNicks = $this->getActiveVipNicknames();

        foreach ($playersRaw as &$p) {
            $nickLower   = mb_strtolower($p['name'], 'UTF-8');
            $p['is_vip'] = isset($vipNicks[$nickLower]);
        }
        unset($p);

        return $this->response
            ->setContentType('application/json')
            ->setJSON([
                'success'   => true,
                'is_online' => true,
                'count'     => count($playersRaw),
                'players'   => $playersRaw,
                'server'    => [
                    'name' => $server['name'],
                    'ip'   => $ip . ':' . $port,
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
