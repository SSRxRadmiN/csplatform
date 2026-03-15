<?php

namespace App\Libraries;

use App\Models\SettingModel;

/**
 * ServerQuery — отримання статусу сервера через VPS Privilege API
 *
 * Shared hosting не підтримує UDP сокети, тому запит йде
 * через HTTP GET до VPS API (?action=serverstats).
 */
class ServerQuery
{
    /**
     * Оновити server_stats в БД
     */
    public static function updateServerStats(int $serverId = 1): array
    {
        $settings = new SettingModel();
        $apiUrl   = $settings->get('vps_api_url') ?? '';
        $apiToken = $settings->get('vps_api_token') ?? '';

        if (empty($apiUrl) || empty($apiToken)) {
            return ['success' => false, 'message' => 'VPS API not configured'];
        }

        // GET запит до VPS API
        $url = rtrim($apiUrl, '/') . '?action=serverstats&token=' . urlencode($apiToken);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            $db = \Config\Database::connect();
            $db->table('server_stats')->where('server_id', $serverId)->update([
                'is_online' => 0,
            ]);
            return ['success' => false, 'message' => 'VPS API request failed'];
        }

        $data = json_decode($response, true);

        if (! is_array($data)) {
            return ['success' => false, 'message' => 'Invalid API response'];
        }

        $db = \Config\Database::connect();

        if (! empty($data['is_online'])) {
            $db->table('server_stats')->where('server_id', $serverId)->update([
                'current_players' => $data['players'] ?? 0,
                'max_players'     => $data['max_players'] ?? 32,
                'current_map'     => $data['map'] ?? '',
                'is_online'       => 1,
            ]);

            return [
                'success'  => true,
                'players'  => $data['players'] ?? 0,
                'max'      => $data['max_players'] ?? 32,
                'map'      => $data['map'] ?? '',
                'hostname' => $data['hostname'] ?? '',
            ];
        } else {
            $db->table('server_stats')->where('server_id', $serverId)->update([
                'is_online' => 0,
            ]);

            return ['success' => true, 'is_online' => false, 'message' => $data['message'] ?? 'Server offline'];
        }
    }
}
