<?php

namespace App\Libraries;

use App\Models\SettingModel;
use App\Models\ServerModel;

/**
 * PrivilegeDelivery — видача привілегій на ігровий сервер
 *
 * Надсилає HTTP POST запити до Privilege API на VPS.
 * Типи доставки:
 *   - deliver: VIP/Admin привілегії (через FreshBans MySQL)
 *   - unban:   Розбан гравця
 *   - model:   Персональна модель гравця (через ultimate_models.ini)
 *
 * Конфігурація в Settings:
 *   - vps_api_url:   http://31.42.190.78/api/privilege
 *   - vps_api_token: секретний API токен
 */
class PrivilegeDelivery
{
    private string $apiUrl;
    private string $apiToken;
    private int    $timeout;

    public function __construct()
    {
        $settings = new SettingModel();

        $this->apiUrl   = $settings->get('vps_api_url') ?? 'http://31.42.190.78/api/privilege';
        $this->apiToken = $settings->get('vps_api_token') ?? '';
        $this->timeout  = 15;
    }

    /**
     * Видати привілегію гравцю (головний метод)
     *
     * Визначає тип доставки по категорії товару і викликає відповідний метод.
     *
     * @param array $order   Дані замовлення
     * @param array $product Дані товару
     * @return array ['success' => bool, 'message' => string]
     */
    public function deliver(array $order, array $product): array
    {
        $categorySlug = $product['cat_slug'] ?? $this->getCategorySlug($product);

        log_message('info', '[PrivilegeDelivery] Starting delivery for order #{id}, category={cat}', [
            'id'  => $order['id'],
            'cat' => $categorySlug,
        ]);

        switch ($categorySlug) {
            case 'vip':
            case 'admin':
                return $this->deliverPrivilege($order, $product);

            case 'unban':
                return $this->deliverUnban($order);

            case 'models':
                return $this->deliverModel($order, $product);

            default:
                // Невідома категорія — логуємо, але не помилка
                $msg = "Unknown category '{$categorySlug}', manual delivery required.";
                log_message('warning', "[PrivilegeDelivery] {$msg}");
                return ['success' => false, 'message' => $msg];
        }
    }

    /**
     * Видача VIP/Admin привілегій
     */
    private function deliverPrivilege(array $order, array $product): array
    {
        return $this->callApi('deliver', [
            'steam_id'      => $order['steam_id'],
            'access'        => $product['amx_access'] ?? 't',
            'flags'         => $product['amx_flags'] ?? 'ce',
            'nickname'      => $order['username'] ?? '',
            'duration_days' => $order['duration_days'] ?? $product['duration_days'] ?? 30,
            'order_id'      => $order['id'],
        ]);
    }

    /**
     * Розбан гравця
     */
    private function deliverUnban(array $order): array
    {
        return $this->callApi('unban', [
            'steam_id' => $order['steam_id'],
            'order_id' => $order['id'],
        ]);
    }

    /**
     * Видача моделі гравця
     */
    private function deliverModel(array $order, array $product): array
    {
        // model_te і model_ct зберігаються в product (додаткові поля)
        // або вказуються через amx_access у форматі "model_te|model_ct"
        $models = $this->parseModelFields($product);

        return $this->callApi('model', [
            'steam_id'      => $order['steam_id'],
            'model_te'      => $models['te'],
            'model_ct'      => $models['ct'],
            'duration_days' => $order['duration_days'] ?? $product['duration_days'] ?? 30,
            'order_id'      => $order['id'],
        ]);
    }

    /**
     * Відкликання привілегії (для крону)
     */
    public function revoke(array $order, string $type = 'all'): array
    {
        return $this->callApi('revoke', [
            'order_id' => $order['id'],
            'type'     => $type,
            'steam_id' => $order['steam_id'] ?? '',
        ]);
    }

    /**
     * HTTP POST до VPS Privilege API
     */
    private function callApi(string $action, array $params): array
    {
        if (empty($this->apiToken)) {
            $msg = 'VPS API token not configured. Set vps_api_token in Settings.';
            log_message('error', "[PrivilegeDelivery] {$msg}");
            return ['success' => false, 'message' => $msg];
        }

        $url = rtrim($this->apiUrl, '/') . '?action=' . urlencode($action);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-API-Token: ' . $this->apiToken,
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        // cURL помилка
        if ($response === false) {
            $msg = "VPS API connection failed: {$error}";
            log_message('error', "[PrivilegeDelivery] {$msg}");
            return ['success' => false, 'message' => $msg];
        }

        // Парсимо відповідь
        $data = json_decode($response, true);

        if (! is_array($data)) {
            $msg = "VPS API invalid response (HTTP {$httpCode}): " . substr($response, 0, 200);
            log_message('error', "[PrivilegeDelivery] {$msg}");
            return ['success' => false, 'message' => $msg];
        }

        // Логуємо результат
        $status = ($data['success'] ?? false) ? 'SUCCESS' : 'FAILED';
        log_message('info', "[PrivilegeDelivery] {$status}: action={action} | {message}", [
            'action'  => $action,
            'message' => $data['message'] ?? '—',
        ]);

        return [
            'success' => $data['success'] ?? false,
            'message' => date('Y-m-d H:i:s') . " | API {$action}: " . ($data['message'] ?? 'No message'),
        ];
    }

    /**
     * Визначити slug категорії з product (якщо cat_slug не прийшов)
     */
    private function getCategorySlug(array $product): string
    {
        if (! empty($product['category_id'])) {
            $db = \Config\Database::connect();
            $row = $db->table('categories')
                      ->select('slug')
                      ->where('id', $product['category_id'])
                      ->get()
                      ->getRowArray();
            return $row['slug'] ?? 'other';
        }
        return $product['category'] ?? 'other';
    }

    /**
     * Парсити поля моделей з товару
     * amx_access у форматі "model_te|model_ct" для товарів категорії models
     */
    private function parseModelFields(array $product): array
    {
        $access = $product['amx_access'] ?? '';

        if (str_contains($access, '|')) {
            [$te, $ct] = explode('|', $access, 2);
            return ['te' => trim($te), 'ct' => trim($ct)];
        }

        // Fallback: одна модель для обох сторін
        $model = $access ?: 'default';
        return ['te' => $model, 'ct' => $model];
    }
}
