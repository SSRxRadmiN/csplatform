<?php

namespace App\Libraries;

/**
 * ServerQuery — UDP запити до ігрового сервера (A2S_INFO протокол)
 *
 * Отримує реальний онлайн, карту, назву, макс гравців.
 * Використовує Source Query Protocol (працює для GoldSrc/CS 1.6).
 */
class ServerQuery
{
    private string $ip;
    private int    $port;
    private float  $timeout;

    public function __construct(string $ip = '31.42.190.78', int $port = 27015, float $timeout = 2.0)
    {
        $this->ip      = $ip;
        $this->port    = $port;
        $this->timeout = $timeout;
    }

    /**
     * Отримати інформацію про сервер (A2S_INFO)
     *
     * @return array|null Масив з даними або null при помилці
     */
    public function getInfo(): ?array
    {
        // A2S_INFO запит: 0xFF 0xFF 0xFF 0xFF 0x54 "Source Engine Query\0"
        $packet = "\xFF\xFF\xFF\xFF\x54Source Engine Query\x00";

        $socket = @fsockopen('udp://' . $this->ip, $this->port, $errno, $errstr, $this->timeout);

        if (! $socket) {
            log_message('warning', "[ServerQuery] Cannot connect to {$this->ip}:{$this->port} — {$errstr}");
            return null;
        }

        stream_set_timeout($socket, (int) $this->timeout, (int) (($this->timeout - (int) $this->timeout) * 1000000));

        fwrite($socket, $packet);
        $response = fread($socket, 4096);
        fclose($socket);

        if (empty($response)) {
            log_message('warning', "[ServerQuery] No response from {$this->ip}:{$this->port}");
            return null;
        }

        return $this->parseInfoResponse($response);
    }

    /**
     * Парсинг A2S_INFO відповіді
     * Підтримує як GoldSrc (0x6D 'm'), так і Source (0x49 'I') формати
     */
    private function parseInfoResponse(string $data): ?array
    {
        $pos = 4; // Пропускаємо 0xFF 0xFF 0xFF 0xFF

        if (strlen($data) < 6) {
            return null;
        }

        $header = ord($data[$pos]);
        $pos++;

        // GoldSrc response (0x6D = 'm') — CS 1.6
        if ($header === 0x6D) {
            return $this->parseGoldSrc($data, $pos);
        }

        // Source response (0x49 = 'I')
        if ($header === 0x49) {
            return $this->parseSource($data, $pos);
        }

        // Challenge response (0x41 = 'A') — потрібен повторний запит з challenge
        if ($header === 0x41 && strlen($data) >= 9) {
            $challenge = substr($data, $pos, 4);
            return $this->getInfoWithChallenge($challenge);
        }

        log_message('warning', "[ServerQuery] Unknown response header: 0x" . dechex($header));
        return null;
    }

    /**
     * A2S_INFO з challenge token
     */
    private function getInfoWithChallenge(string $challenge): ?array
    {
        $packet = "\xFF\xFF\xFF\xFF\x54Source Engine Query\x00" . $challenge;

        $socket = @fsockopen('udp://' . $this->ip, $this->port, $errno, $errstr, $this->timeout);
        if (! $socket) {
            return null;
        }

        stream_set_timeout($socket, (int) $this->timeout, (int) (($this->timeout - (int) $this->timeout) * 1000000));

        fwrite($socket, $packet);
        $response = fread($socket, 4096);
        fclose($socket);

        if (empty($response)) {
            return null;
        }

        return $this->parseInfoResponse($response);
    }

    /**
     * Парсинг GoldSrc формату (CS 1.6)
     */
    private function parseGoldSrc(string $data, int $pos): ?array
    {
        try {
            $address    = $this->readString($data, $pos);
            $hostname   = $this->readString($data, $pos);
            $map        = $this->readString($data, $pos);
            $gamedir    = $this->readString($data, $pos);
            $gamedesc   = $this->readString($data, $pos);
            $players    = ord($data[$pos++]);
            $maxPlayers = ord($data[$pos++]);

            return [
                'hostname'    => $hostname,
                'map'         => $map,
                'players'     => $players,
                'max_players' => $maxPlayers,
                'gamedir'     => $gamedir,
                'gamedesc'    => $gamedesc,
                'is_online'   => true,
            ];
        } catch (\Throwable $e) {
            log_message('error', "[ServerQuery] GoldSrc parse error: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Парсинг Source формату
     */
    private function parseSource(string $data, int $pos): ?array
    {
        try {
            $protocol   = ord($data[$pos++]);
            $hostname   = $this->readString($data, $pos);
            $map        = $this->readString($data, $pos);
            $gamedir    = $this->readString($data, $pos);
            $gamedesc   = $this->readString($data, $pos);
            $appId      = unpack('v', substr($data, $pos, 2))[1]; $pos += 2;
            $players    = ord($data[$pos++]);
            $maxPlayers = ord($data[$pos++]);

            return [
                'hostname'    => $hostname,
                'map'         => $map,
                'players'     => $players,
                'max_players' => $maxPlayers,
                'gamedir'     => $gamedir,
                'gamedesc'    => $gamedesc,
                'is_online'   => true,
            ];
        } catch (\Throwable $e) {
            log_message('error', "[ServerQuery] Source parse error: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Читаємо null-terminated string
     */
    private function readString(string $data, int &$pos): string
    {
        $end = strpos($data, "\x00", $pos);
        if ($end === false) {
            $end = strlen($data);
        }
        $str = substr($data, $pos, $end - $pos);
        $pos = $end + 1;
        return $str;
    }

    /**
     * Оновити server_stats в БД
     *
     * Зручний метод для крону або контролера
     */
    public static function updateServerStats(int $serverId = 1): array
    {
        $serverModel = new \App\Models\ServerModel();
        $server = $serverModel->find($serverId);

        if (! $server) {
            return ['success' => false, 'message' => 'Server not found'];
        }

        $query = new self($server['ip'], (int) $server['port']);
        $info = $query->getInfo();

        $db = \Config\Database::connect();

        if ($info) {
            $db->table('server_stats')->where('server_id', $serverId)->update([
                'current_players' => $info['players'],
                'max_players'     => $info['max_players'],
                'current_map'     => $info['map'],
                'is_online'       => 1,
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);

            return [
                'success'  => true,
                'players'  => $info['players'],
                'max'      => $info['max_players'],
                'map'      => $info['map'],
                'hostname' => $info['hostname'],
            ];
        } else {
            $db->table('server_stats')->where('server_id', $serverId)->update([
                'is_online'   => 0,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);

            return ['success' => false, 'message' => 'Server offline'];
        }
    }
}
