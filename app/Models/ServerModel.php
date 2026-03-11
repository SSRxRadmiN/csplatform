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
}
