<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table         = 'products';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'server_id', 'name_ua', 'name_en',
        'description_ua', 'description_en',
        'price', 'duration_days',
        'amx_access', 'amx_flags',
        'category_id', 'image_url',
        'sort_order', 'is_active',
    ];

    protected $returnType = 'array';

    /**
     * Отримати активні товари сервера з категорією
     */
    public function getByServer(int $serverId): array
    {
        return $this->where('server_id', $serverId)
                    ->where('is_active', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Отримати активні товари з JOIN categories
     */
    public function getByServerWithCategory(int $serverId): array
    {
        return $this->select('products.*, categories.slug as cat_slug, categories.name_ua as cat_name_ua, categories.name_en as cat_name_en, categories.icon as cat_icon, categories.color as cat_color, categories.sort_order as cat_sort')
                    ->join('categories', 'categories.id = products.category_id', 'left')
                    ->where('products.server_id', $serverId)
                    ->where('products.is_active', 1)
                    ->orderBy('categories.sort_order', 'ASC')
                    ->orderBy('products.sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Отримати назву товару в залежності від мови
     */
    public function getName(array $product, string $lang = 'ua'): string
    {
        $field = 'name_' . $lang;
        return $product[$field] ?? $product['name_ua'];
    }

    /**
     * Отримати опис товару в залежності від мови
     */
    public function getDescription(array $product, string $lang = 'ua'): string
    {
        $field = 'description_' . $lang;
        return $product[$field] ?? $product['description_ua'] ?? '';
    }
}
