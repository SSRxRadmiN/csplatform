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
     * Видати/продовжити VIP або Admin привілегію.
     * Порт handle_deliver.
     *
     * @param string $steamId  STEAM_0:1:...
     * @param string $access   рядок доступу AMX (напр. 'abcdefghijklmnopqrstu')
     * @param string $flags    флаги (напр. 't' VIP / 'tm' / 'ce')
     * @param int    $duration днів; 0 = безстроково
     * @param string $nickname
     * @param int|string $orderId
     * @return array{success:bool, action?:string, message:string}
     */
    public function deliver(string $steamId, string $access, string $flags, int $duration, string $nickname, $orderId): array
    {
        if ($steamId === '' || $access === '') {
            return ['success' => false, 'message' => 'Missing steam_id or access'];
        }

        $now      = time();
        $username = $orderId !== '' && $orderId !== null ? "hs:{$orderId}" : 'hs:' . $now;
        $nick     = $nickname !== '' ? $nickname : $steamId;

        // Шукаємо активну привілегію з тим самим steamid + access
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
            $oldExpired    = (int) ($existing['expired'] ?? 0);
            $oldDays       = (int) ($existing['days'] ?? 0);
            $extendSeconds = $duration > 0 ? $duration * 86400 : 0;

            if ($duration === 0) {
                // Купив безстроковий — робимо безстроковим
                $newExpired = 0;
                $newDays    = 0;
            } elseif ($oldExpired === 0) {
                // Була безстрокова — залишаємо безстроковою
                $newExpired = 0;
                $newDays    = 0;
            } else {
                // Від поточного expired (або зараз, якщо вже минув) додаємо нові дні
                $base       = max($oldExpired, $now);
                $newExpired = $base + $extendSeconds;
                $newDays    = $oldDays + $duration;
            }

            $this->db->table('amx_amxadmins')
                ->where('id', $existing['id'])
                ->update([
                    'expired'  => $newExpired,
                    'days'     => $newDays,
                    'flags'    => $flags,
                    'nickname' => $nick,
                    'username' => $username,
                ]);

            return [
                'success'     => true,
                'action'      => 'extended',
                'existing_id' => $existing['id'],
                'message'     => "Extended privilege #{$existing['id']}: +{$duration} days",
            ];
        }

        // Створюємо новий запис
        $created = $now;
        $expired = $duration > 0 ? $created + ($duration * 86400) : 0;
        $days    = $duration > 0 ? $duration : 0;

        $this->db->table('amx_amxadmins')->insert([
            'username' => $username,
            'password' => '',
            'access'   => $access,
            'flags'    => $flags,
            'steamid'  => $steamId,
            'nickname' => $nick,
            'created'  => $created,
            'expired'  => $expired,
            'days'     => $days,
        ]);

        return [
            'success'  => true,
            'action'   => 'created',
            'username' => $username,
            'admin_id' => $this->db->insertID(),
            'message'  => "Created new privilege for {$steamId}",
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
     * Відкликати привілегію за маркером замовлення. Порт handle_revoke (тип admin/vip).
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
            $this->db->table('amx_amxadmins')->where('username', $username)->delete();
            $revoked[] = "amx_amxadmins: {$this->db->affectedRows()} rows";
        }

        if ($type === 'model') {
            $revoked[] = 'model: manual (ultimate_models.ini на сервері)';
        }

        return ['success' => true, 'revoked' => $revoked, 'message' => implode('; ', $revoked)];
    }
}
