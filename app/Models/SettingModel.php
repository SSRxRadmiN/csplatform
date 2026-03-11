<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table      = 'settings';
    protected $primaryKey = 'key';
    protected $useTimestamps = false;

    protected $allowedFields = ['key', 'value', 'updated_at'];
    protected $returnType = 'array';

    /**
     * Отримати значення по ключу
     */
    public function get(string $key, $default = null): ?string
    {
        $row = $this->find($key);
        return $row ? $row['value'] : $default;
    }

    /**
     * Зберегти значення
     */
    public function setSetting(string $key, ?string $value): bool
    {
        $exists = $this->find($key);

        if ($exists) {
            return (bool) $this->update($key, [
                'value'      => $value,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return (bool) $this->insert([
            'key'        => $key,
            'value'      => $value,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Отримати всі налаштування як масив
     */
    public function getAll(): array
    {
        $rows = $this->findAll();
        $settings = [];

        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }

        return $settings;
    }
}