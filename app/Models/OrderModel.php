<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table         = 'orders';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'user_id', 'product_id', 'server_id',
        'steam_id', 'amount', 'status',
        'payment_id', 'payment_url',
        'product_name', 'amx_access', 'amx_flags', 'duration_days',
        'paid_at', 'delivered_at', 'expires_at',
        'delivery_log',
    ];

    protected $returnType = 'array';

    /**
     * Замовлення юзера
     */
    public function getByUser(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Активні привілеї юзера
     */
    public function getActivePrivileges(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->where('status', 'delivered')
                    ->groupStart()
                        ->where('expires_at >', date('Y-m-d H:i:s'))
                        ->orWhere('expires_at IS NULL')
                    ->groupEnd()
                    ->findAll();
    }

    /**
     * Замовлення по payment_id
     */
    public function findByPaymentId(string $paymentId): ?array
    {
        return $this->where('payment_id', $paymentId)->first();
    }

    /**
     * Прострочені привілеї
     */
    public function getExpired(): array
    {
        return $this->where('status', 'delivered')
                    ->where('expires_at <', date('Y-m-d H:i:s'))
                    ->where('expires_at IS NOT NULL')
                    ->findAll();
    }
}
