<?php

namespace App\Models;

use CodeIgniter\Model;

class ServerModel extends Model
{
    protected $table         = 'servers';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'owner_id', 'name', 'ip', 'port',
        'description_ua', 'description_en', 'country', 'banner_url',
        'connection_type', 'api_url', 'api_key',
        'db_host', 'db_port', 'db_user', 'db_pass_encrypted', 'db_name', 'db_table',
        'rcon_password_encrypted',
        'is_active', 'is_verified',
    ];

    protected $returnType = 'array';

    // Валідація для адмін-форми
    protected $validationRules = [
        'name' => 'required|max_length[120]',
        'ip'   => 'required|max_length[64]',
        'port' => 'required|integer|greater_than[0]|less_than[65536]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Введіть назву сервера',
            'max_length' => 'Назва занадто довга',
        ],
        'ip' => [
            'required'   => 'Введіть IP або хост',
            'max_length' => 'IP занадто довгий',
        ],
        'port' => [
            'required'      => 'Введіть порт',
            'integer'       => 'Порт має бути числом',
            'greater_than'  => 'Порт має бути > 0',
            'less_than'     => 'Порт має бути < 65536',
        ],
    ];

    /**
     * Отримати активні сервери
     */
    public function getActive(): array
    {
        return $this->where('is_active', 1)
                    ->where('is_verified', 1)
                    ->findAll();
    }

    /**
     * Отримати сервер з його статистикою
     */
    public function getWithStats(int $id): ?array
    {
        return $this->select('servers.*, server_stats.current_players, server_stats.max_players, server_stats.current_map, server_stats.is_online')
                    ->join('server_stats', 'server_stats.server_id = servers.id', 'left')
                    ->find($id);
    }

    /**
     * Отримати облікові дані VPS API для сервера.
     * Використовується бібліотеками PrivilegeDelivery, ServerQuery,
     * та контролерами Stats, Bans, Admin\Privileges.
     *
     * @return array{url: string, token: string}
     */
    public function getApiCredentials(int $serverId = 1): array
    {
        $row = $this->select('api_url, api_key')->find($serverId);
        return [
            'url'   => $row['api_url'] ?? '',
            'token' => $row['api_key'] ?? '',
        ];
    }
}
