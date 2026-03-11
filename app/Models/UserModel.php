<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'email', 'password', 'username', 'steam_id',
        'role', 'language', 'is_active', 'last_login',
    ];

    protected $returnType = 'array';

    // Валідація
    protected $validationRules = [
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Цей email вже зареєстрований',
        ],
    ];

    /**
     * Знайти юзера по email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Знайти юзера по Steam ID
     */
    public function findBySteamId(string $steamId): ?array
    {
        return $this->where('steam_id', $steamId)->first();
    }

    /**
     * Оновити last_login
     */
    public function updateLastLogin(int $id): bool
    {
        return $this->update($id, ['last_login' => date('Y-m-d H:i:s')]);
    }
}
