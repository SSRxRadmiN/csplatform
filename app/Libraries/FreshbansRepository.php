<?php

namespace App\Libraries;

use Config\Database;

/**
 * FreshbansRepository — прямий запис привілегій у базу freshbans (amx_amxadmins / amx_bans).
 *
 * Замінює HTTP-виклики до Python privilege_api.py (action=deliver/unban/revoke).
 * Логіка портована один-в-один з handle_deliver / handle_unban / handle_revoke.
 *
 * Працює через окрему DB-групу 'freshbans' (віддалений MySQL на 185.252.24.118).
 * RCON/amx_reloadadmins НЕ потрібен: AMX підхоплює amx_amxadmins при реконекті
 * гравця або зміні карти — так само, як це робив Python (він теж не рілоадив).
 *
 * Категорія 'models' тут НЕ обробляється — це запис у файл ultimate_models.ini
 * на FS ігрового сервера, видається вручну.
 */
class FreshbansRepository
{
    private \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = Database::connect('freshbans');
    }

    /**
     * Резолвить freshbans server_id (amx_serverinfo.id) за адресою сервера.
     *
     * Потрібен, бо нумерація серверів у CI4-таблиці `servers` (РК=1, УЕ=2) НЕ
     * збігається з freshbans `amx_serverinfo` (РК=3, УЕ=4). Спільний природний
     * ключ — address (IP:port), однаковий в обох базах. Мапимо по ньому, щоб
     * не хардкодити id і не тримати окреме поле-мапу в синхроні.
     *
     * @return int|null freshbans server_id, або null якщо сервер не знайдено
     *                   (не підключався до freshbans → привілей нікуди писати)
     */
    public function resolveServerId(string $ip, int $port): ?int
    {
        $address = $ip . ':' . $port;

        $row = $this->db->table('amx_serverinfo')
            ->select('id')
            ->where('address', $address)
            ->limit(1)
            ->get()
            ->getRowArray();

        return $row ? (int) $row['id'] : null;
    }

    /**
     * Видати/продовжити VIP або Admin привілегію на КОНКРЕТНИЙ сервер.
     *
     * Схема freshbans (підтверджена логікою CSBans + живими даними):
     *   - amx_amxadmins: один рядок на адміна. access = куплені права (напр. 't'),
     *     flags = тип авторизації 'ce' (c=по steamid, e=без пароля) — однаково для всіх
     *     куплених привілеїв. password = ''.
     *   - amx_admins_servers: прив'язка адміна до сервера. РІВНО цей рядок робить
     *     привілей активним на сервері. server_id = 3 (РК) або 4 (УЕ).
     *     custom_flags = права на цьому сервері (= access).
     *
     * Привілей діє ТІЛЬКИ там, де є рядок у amx_admins_servers → пишемо лише для
     * вибраного $serverId, інші сервери не чіпаємо.
     *
     * @param string     $steamId   STEAM_0:1:...
     * @param string     $access    куплені права AMX (напр. 't' VIP, 'tm', 'abcde...')
     * @param int        $serverId  3 = РЕАЛЬНІ КАБАНИ, 4 = УКРАЇНСЬКА ЕЛІТА
     * @param int        $duration  днів; 0 = безстроково
     * @param string     $nickname
     * @param int|string $orderId
     * @param string     $authFlags тип авторизації для amx_amxadmins.flags (дефолт 'ce')
     */
    public function deliver(string $steamId, string $access, int $serverId, int $duration, string $nickname, $orderId, string $authFlags = 'ce'): array
    {
        if ($steamId === '' || $access === '') {
            return ['success' => false, 'message' => 'Missing steam_id or access'];
        }
        if ($serverId <= 0) {
            return ['success' => false, 'message' => 'Missing/invalid server_id'];
        }

        $now      = time();
        $username = $orderId !== '' && $orderId !== null ? "hs:{$orderId}" : 'hs:' . $now;
        $nick     = $nickname !== '' ? $nickname : $steamId;

        // --- Крок 1: знайти/створити адміна в amx_amxadmins ---
        $existing = $this->db->table('amx_amxadmins')
            ->select('id, expired, days, created')
            ->where('steamid', $steamId)
            ->where('access', $access)
            ->groupStart()
                ->where('expired', 0)
                ->orWhere('expired >', $now)
            ->groupEnd()
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($existing) {
            $adminId       = (int) $existing['id'];
            $oldExpired    = (int) ($existing['expired'] ?? 0);
            $oldDays       = (int) ($existing['days'] ?? 0);
            $extendSeconds = $duration > 0 ? $duration * 86400 : 0;

            if ($duration === 0) {
                $newExpired = 0;
                $newDays    = 0;
            } elseif ($oldExpired === 0) {
                $newExpired = 0;
                $newDays    = 0;
            } else {
                $base       = max($oldExpired, $now);
                $newExpired = $base + $extendSeconds;
                $newDays    = $oldDays + $duration;
            }

            $this->db->table('amx_amxadmins')
                ->where('id', $adminId)
                ->update([
                    'expired'  => $newExpired,
                    'days'     => $newDays,
                    'flags'    => $authFlags,
                    'nickname' => $nick,
                    'username' => $username,
                ]);

            $action = 'extended';
        } else {
            $created = $now;
            $expired = $duration > 0 ? $created + ($duration * 86400) : 0;
            $days    = $duration > 0 ? $duration : 0;

            $this->db->table('amx_amxadmins')->insert([
                'username' => $username,
                'password' => '',
                'access'   => $access,
                'flags'    => $authFlags,
                'steamid'  => $steamId,
                'nickname' => $nick,
                'created'  => $created,
                'expired'  => $expired,
                'days'     => $days,
            ]);
            $adminId = (int) $this->db->insertID();
            $action  = 'created';
        }

        // --- Крок 2: прив'язка до вибраного сервера в amx_admins_servers ---
        // Цей рядок робить привілей активним САМЕ на цьому сервері.
        $link = $this->db->table('amx_admins_servers')
            ->where('admin_id', $adminId)
            ->where('server_id', $serverId)
            ->get()
            ->getRowArray();

        if ($link) {
            $this->db->table('amx_admins_servers')
                ->where('admin_id', $adminId)
                ->where('server_id', $serverId)
                ->update(['custom_flags' => $access]);
            $linkAction = 'link-updated';
        } else {
            $this->db->table('amx_admins_servers')->insert([
                'admin_id'           => $adminId,
                'server_id'          => $serverId,
                'custom_flags'       => $access,
                'use_static_bantime' => 'no',
            ]);
            $linkAction = 'link-created';
        }

        return [
            'success'  => true,
            'action'   => $action,
            'admin_id' => $adminId,
            'username' => $username,
            'message'  => "Privilege {$action} (#{$adminId}) on server {$serverId}, {$linkAction}, +{$duration}d",
        ];
    }

    /**
     * Розбан гравця. Порт handle_unban.
     */
    public function unban(string $steamId): array
    {
        if ($steamId === '') {
            return ['success' => false, 'message' => 'Missing steam_id'];
        }

        $this->db->table('amx_bans')
            ->where('player_id', $steamId)
            ->where('expired', 0)
            ->update(['expired' => 1]);

        return [
            'success' => true,
            'message' => "Unbanned {$steamId}: {$this->db->affectedRows()} rows",
        ];
    }

    /**
     * Відкликати привілегію за маркером замовлення.
     * Чистить і прив'язки до серверів (amx_admins_servers), і сам запис (amx_amxadmins).
     * MyISAM не має FK/каскадів — видаляємо зв'язки вручну, щоб не лишити сиріт.
     * Тип 'model' тут не обробляється (FS-файл, ручна видача).
     */
    public function revoke($orderId, string $type = 'admin'): array
    {
        if ($orderId === '' || $orderId === null) {
            return ['success' => false, 'message' => 'Missing order_id'];
        }

        $revoked = [];

        if (in_array($type, ['admin', 'vip', 'all'], true)) {
            $username = "hs:{$orderId}";

            // Знаходимо admin_id за маркером замовлення
            $admins = $this->db->table('amx_amxadmins')
                ->select('id')
                ->where('username', $username)
                ->get()
                ->getResultArray();

            $ids = array_map(static fn ($r) => (int) $r['id'], $admins);

            if ($ids) {
                // Спершу прив'язки до серверів
                $this->db->table('amx_admins_servers')->whereIn('admin_id', $ids)->delete();
                $revoked[] = "amx_admins_servers: {$this->db->affectedRows()} rows";
            }

            // Потім сам запис адміна
            $this->db->table('amx_amxadmins')->where('username', $username)->delete();
            $revoked[] = "amx_amxadmins: {$this->db->affectedRows()} rows";
        }

        if ($type === 'model') {
            $revoked[] = 'model: manual (ultimate_models.ini на сервері)';
        }

        return ['success' => true, 'revoked' => $revoked, 'message' => implode('; ', $revoked)];
    }
}
