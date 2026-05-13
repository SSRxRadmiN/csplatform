<?php

namespace App\Libraries;

use App\Models\ServerModel;

/**
 * ServerQuery — отримання статусу ігрового сервера через ПРЯМЕ UDP
 *               за протоколом Valve A2S (без VPS API посередника).
 *
 * Архітектура (раніше була через Python API на VPS):
 *   Сайт (s-host)  ──UDP──▶  HLDS (185.252.24.118:27015)
 *
 * Що це дає:
 *   - швидше: 30-100мс UDP замість 300-500мс HTTP через VPS
 *   - стабільніше: коли VPS API падає, сайт продовжує працювати
 *   - менше залежностей: не потрібен ні Python, ні API token
 *
 * Контракт публічного API ЗБЕРЕЖЕНО:
 *   ServerQuery::updateServerStats(int $serverId): array
 *   повертає той самий формат що й раніше, отже Cron і інші виклики
 *   не потребують змін.
 *
 * Додаткові методи (потрібні для модалки списку гравців):
 *   ServerQuery::getInfo(string $ip, int $port): ?array
 *   ServerQuery::getPlayers(string $ip, int $port): ?array
 */
class ServerQuery
{
    /** Таймаути для UDP (секунди) */
    private const TIMEOUT_CONNECT = 2;
    private const TIMEOUT_READ    = 2;

    /** Розмір буфера для відповіді */
    private const BUFFER_SIZE = 4096;

    // ─────────────────────────────────────────────────────────────────
    // Публічний API (зберігає старий контракт)
    // ─────────────────────────────────────────────────────────────────

    /**
     * Оновлює `server_stats` для конкретного сервера через пряме UDP.
     * Викликається з Cron::serverstats() для кожного активного сервера.
     */
    public static function updateServerStats(int $serverId = 1): array
    {
        $serverModel = new ServerModel();
        $server      = $serverModel->find($serverId);

        if (! $server) {
            return ['success' => false, 'message' => "Server #{$serverId} not found"];
        }

        $ip   = $server['ip'];
        $port = (int) $server['port'];

        $info = self::getInfo($ip, $port);

        $db = \Config\Database::connect();

        if ($info === null) {
            // Сервер не відповів → офлайн
            $db->table('server_stats')
               ->where('server_id', $serverId)
               ->update(['is_online' => 0]);

            return [
                'success'   => true,
                'is_online' => false,
                'message'   => 'Server did not respond (UDP timeout)',
            ];
        }

        // Сервер відповів → онлайн, оновлюємо кеш
        $db->table('server_stats')
           ->where('server_id', $serverId)
           ->update([
               'current_players' => $info['players'],
               'max_players'     => $info['max_players'],
               'current_map'     => $info['map'],
               'is_online'       => 1,
           ]);

        return [
            'success'  => true,
            'players'  => $info['players'],
            'max'      => $info['max_players'],
            'map'      => $info['map'],
            'hostname' => $info['hostname'],
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    // A2S_INFO — основна інформація про сервер
    // ─────────────────────────────────────────────────────────────────

    /**
     * A2S_INFO (0x54): назва, мапа, кількість гравців.
     *
     * Повертає масив або null якщо сервер не відповів.
     *
     * Формат повернення:
     *   [
     *     'hostname'    => string,
     *     'map'         => string,
     *     'folder'      => string,
     *     'game'        => string,
     *     'players'     => int,
     *     'max_players' => int,
     *     'bots'        => int,
     *     'protocol'    => int,
     *   ]
     */
    public static function getInfo(string $ip, int $port): ?array
    {
        // Старий протокол HLDS (CS 1.6) — challenge не потрібен,
        // але новий Source Engine може попросити. Підтримуємо обидва.
        $request = "\xFF\xFF\xFF\xFF" . "TSource Engine Query\x00";

        $response = self::udpRequest($ip, $port, $request);
        if ($response === null) {
            return null;
        }

        // Якщо сервер просить challenge (header 0x41) — повторюємо запит
        if (strlen($response) >= 9 && ord($response[4]) === 0x41) {
            $challenge = substr($response, 5, 4);
            $request   = "\xFF\xFF\xFF\xFF" . "TSource Engine Query\x00" . $challenge;
            $response  = self::udpRequest($ip, $port, $request);
            if ($response === null) {
                return null;
            }
        }

        return self::parseInfoResponse($response);
    }

    /**
     * Парсер відповіді A2S_INFO.
     * Підтримує HLDS (header 0x6D, протокол 47-48) і Source (header 0x49).
     */
    private static function parseInfoResponse(string $data): ?array
    {
        if (strlen($data) < 6) {
            return null;
        }

        // Перевірка префіксу \xFF\xFF\xFF\xFF
        if (substr($data, 0, 4) !== "\xFF\xFF\xFF\xFF") {
            return null;
        }

        $header = ord($data[4]);
        $offset = 5;

        // Source Engine (0x49): protocol(byte), name, map, folder, game, appid, players, max, bots, ...
        if ($header === 0x49) {
            $protocol = ord($data[$offset++]);
            $hostname = self::readString($data, $offset);
            $map      = self::readString($data, $offset);
            $folder   = self::readString($data, $offset);
            $game     = self::readString($data, $offset);

            // appid (2 bytes) — пропускаємо
            $offset += 2;

            $players    = ord($data[$offset++]);
            $maxPlayers = ord($data[$offset++]);
            $bots       = ord($data[$offset++]);

            return [
                'hostname'    => self::decodeUtf8($hostname),
                'map'         => $map,
                'folder'      => $folder,
                'game'        => $game,
                'players'     => $players,
                'max_players' => $maxPlayers,
                'bots'        => $bots,
                'protocol'    => $protocol,
            ];
        }

        // Старий HLDS (0x6D): address, name, map, folder, game, players, max, protocol, ...
        if ($header === 0x6D) {
            self::readString($data, $offset); // address — пропускаємо
            $hostname = self::readString($data, $offset);
            $map      = self::readString($data, $offset);
            $folder   = self::readString($data, $offset);
            $game     = self::readString($data, $offset);

            $players    = ord($data[$offset++]);
            $maxPlayers = ord($data[$offset++]);
            $protocol   = ord($data[$offset++]);

            return [
                'hostname'    => self::decodeUtf8($hostname),
                'map'         => $map,
                'folder'      => $folder,
                'game'        => $game,
                'players'     => $players,
                'max_players' => $maxPlayers,
                'bots'        => 0,
                'protocol'    => $protocol,
            ];
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────
    // A2S_PLAYER — список гравців
    // ─────────────────────────────────────────────────────────────────

    /**
     * A2S_PLAYER (0x55): список гравців на сервері.
     *
     * Повертає [['name'=>..., 'frags'=>..., 'time_seconds'=>...], ...]
     * відсортований за фрагами спадно. Порожній масив = немає гравців.
     * null = сервер не відповів.
     *
     * Протокол:
     *   1. Запит challenge: \xFF\xFF\xFF\xFF\x55\xFF\xFF\xFF\xFF
     *   2. Сервер шле header 0x41 + 4 байти challenge
     *      (АБО одразу відповідає 0x44 на старих HLDS)
     *   3. Запит з challenge: \xFF\xFF\xFF\xFF\x55<4 bytes challenge>
     *   4. Відповідь 0x44: num_players + цикл [index, name\0, score(int32), duration(float32)]
     */
    public static function getPlayers(string $ip, int $port): ?array
    {
        // Крок 1: запитати challenge
        $request  = "\xFF\xFF\xFF\xFF\x55\xFF\xFF\xFF\xFF";
        $response = self::udpRequest($ip, $port, $request);

        if ($response === null || strlen($response) < 5) {
            return null;
        }

        $header = ord($response[4]);

        // Крок 3 потрібен тільки якщо отримали challenge (0x41)
        if ($header === 0x41) {
            $challenge = substr($response, 5, 4);
            $request   = "\xFF\xFF\xFF\xFF\x55" . $challenge;
            $response  = self::udpRequest($ip, $port, $request);

            if ($response === null || strlen($response) < 6) {
                return null;
            }
            $header = ord($response[4]);
        }

        // Очікуємо 0x44 (player list response)
        if ($header !== 0x44) {
            return null;
        }

        return self::parsePlayersResponse($response);
    }

    /**
     * Парсер 0x44 відповіді: num_players + список.
     */
    private static function parsePlayersResponse(string $data): array
    {
        $offset     = 5; // \xFF\xFF\xFF\xFF\x44
        $numPlayers = ord($data[$offset++]);
        $players    = [];
        $len        = strlen($data);

        for ($i = 0; $i < $numPlayers; $i++) {
            if ($offset >= $len) {
                break;
            }

            // index byte — пропускаємо (HLDS часто всім ставить 0)
            $offset++;

            // name (null-terminated)
            $name = self::readString($data, $offset);

            // score (int32 LE)
            if ($offset + 4 > $len) break;
            $score   = unpack('V', substr($data, $offset, 4))[1];
            // signed conversion
            if ($score > 0x7FFFFFFF) {
                $score -= 0x100000000;
            }
            $offset += 4;

            // duration (float32 LE) — секунди на сервері
            if ($offset + 4 > $len) break;
            $duration = unpack('g', substr($data, $offset, 4))[1];
            $offset += 4;

            // HLDS іноді віддає порожнього "примарного" гравця у слоті — пропускаємо
            if ($name === '') {
                continue;
            }

            $players[] = [
                'name'         => self::decodeUtf8($name),
                'frags'        => (int) $score,
                'time_seconds' => (int) $duration,
            ];
        }

        // Сортуємо за фрагами спадно (як у gam1ngcs)
        usort($players, fn($a, $b) => $b['frags'] <=> $a['frags']);

        return $players;
    }

    // ─────────────────────────────────────────────────────────────────
    // UDP — низькорівневе спілкування
    // ─────────────────────────────────────────────────────────────────

    /**
     * Виконує UDP-запит і повертає відповідь або null.
     *
     * Використовується fsockopen("udp://...") бо він є на всіх PHP-конфігах
     * (не потребує php-sockets розширення). Так само як це робить
     * xPaw/SourceQuery, gameQ, lgsl.
     */
    private static function udpRequest(string $ip, int $port, string $payload): ?string
    {
        $fp = @fsockopen("udp://{$ip}", $port, $errno, $errstr, self::TIMEOUT_CONNECT);
        if (! $fp) {
            log_message('warning', "[ServerQuery] UDP connect failed: {$ip}:{$port} — {$errstr} ({$errno})");
            return null;
        }

        stream_set_timeout($fp, self::TIMEOUT_READ);

        fwrite($fp, $payload);
        $response = fread($fp, self::BUFFER_SIZE);

        $meta = stream_get_meta_data($fp);
        fclose($fp);

        if (! empty($meta['timed_out'])) {
            log_message('warning', "[ServerQuery] UDP timed out: {$ip}:{$port}");
            return null;
        }

        if ($response === false || $response === '') {
            return null;
        }

        return $response;
    }

    // ─────────────────────────────────────────────────────────────────
    // Хелпери парсингу
    // ─────────────────────────────────────────────────────────────────

    /**
     * Читає null-terminated string з $data починаючи з $offset.
     * $offset переноситься за термінуючий \0.
     */
    private static function readString(string $data, int &$offset): string
    {
        $end = strpos($data, "\x00", $offset);
        if ($end === false) {
            $str    = substr($data, $offset);
            $offset = strlen($data);
            return $str;
        }
        $str    = substr($data, $offset, $end - $offset);
        $offset = $end + 1;
        return $str;
    }

    /**
     * Гарантує що рядок валідний UTF-8 (HLDS дозволяє будь-які байти в нікнеймах,
     * браузер може зламатись на невалідних послідовностях).
     */
    private static function decodeUtf8(string $str): string
    {
        if ($str === '' || \mb_check_encoding($str, 'UTF-8')) {
            return $str;
        }
        // Замінюємо некоректні байти; для CS 1.6 — найімовірніше Windows-1251 або CP866
        return \mb_convert_encoding($str, 'UTF-8', 'UTF-8, Windows-1251, CP866, ISO-8859-1');
    }
}
