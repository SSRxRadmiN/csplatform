<?php

namespace App\Libraries;

use App\Models\ServerModel;

/**
 * PrivilegeDelivery — видача привілегій на ігровий сервер.
 *
 * Пише напряму в базу freshbans (amx_amxadmins / amx_bans) через
 * FreshbansRepository (віддалений MySQL на 185.252.24.118).
 * Раніше йшло HTTP POST на Python privilege_api.py — більше не потрібно.
 *
 * Типи доставки:
 *   - deliver (vip/admin): запис привілегії в amx_amxadmins
 *   - unban:               amx_bans.expired = 1
 *   - model:               НЕ автоматизується (файл ultimate_models.ini на FS
 *                          сервера), видається вручну.
 */
class PrivilegeDelivery
{
    private int $serverId;
    private FreshbansRepository $repo;

    public function __construct(int $serverId = 1)
    {
        $this->serverId = $serverId;
        $this->repo     = new FreshbansRepository();
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
     * Видача VIP/Admin привілегій на вибраний гравцем сервер.
     *
     * server_id у замовленні — це CI4 servers.id (1=РК, 2=УЕ), що НЕ збігається з
     * freshbans server_id (3/4). Тому: беремо IP:port CI4-сервера → резолвимо
     * freshbans-id через amx_serverinfo (по адресу) → пишемо привілей туди.
     */
    private function deliverPrivilege(array $order, array $product): array
    {
        $duration   = (int) ($order['duration_days'] ?? $product['duration_days'] ?? 30);
        $ci4Server  = (int) ($order['server_id'] ?? 0);

        if ($ci4Server <= 0) {
            $msg = "Order #{$order['id']}: не вказано server_id (вибір сервера). Доставку зупинено.";
            log_message('error', "[PrivilegeDelivery] {$msg}");
            return ['success' => false, 'message' => $msg];
        }

        // IP:port вибраного сервера з CI4-таблиці servers
        $serverModel = new ServerModel();
        $server = $serverModel->find($ci4Server);
        if (! $server) {
            $msg = "Order #{$order['id']}: сервер CI4 #{$ci4Server} не знайдено.";
            log_message('error', "[PrivilegeDelivery] {$msg}");
            return ['success' => false, 'message' => $msg];
        }

        // Резолв freshbans server_id по адресу (amx_serverinfo)
        $fbServerId = $this->repo->resolveServerId($server['ip'], (int) $server['port']);
        if ($fbServerId === null) {
            $msg = "Order #{$order['id']}: сервер {$server['ip']}:{$server['port']} відсутній у freshbans (amx_serverinfo). Привілей нікуди писати.";
            log_message('error', "[PrivilegeDelivery] {$msg}");
            return ['success' => false, 'message' => $msg];
        }

        return $this->wrap('deliver', $this->repo->deliver(
            $order['steam_id'],
            $product['amx_access'] ?? 't',
            $fbServerId,
            $duration,
            $order['username'] ?? '',
            $order['id'],
            $product['amx_flags'] ?? 'ce'
        ));
    }

    /**
     * Розбан гравця
     */
    private function deliverUnban(array $order): array
    {
        return $this->wrap('unban', $this->repo->unban($order['steam_id']));
    }

    /**
     * Видача моделі гравця.
     * Через прямий MySQL не автоматизується (файл ultimate_models.ini на FS
     * ігрового сервера). Видається вручну — повертаємо відповідний статус.
     */
    private function deliverModel(array $order, array $product): array
    {
        $msg = "Модель: ручна видача (ultimate_models.ini на сервері), order #{$order['id']}.";
        log_message('info', "[PrivilegeDelivery] {$msg}");
        return ['success' => false, 'manual' => true, 'message' => $msg];
    }

    /**
     * Відкликання привілегії (для крону)
     */
    public function revoke(array $order, string $type = 'all'): array
    {
        // 'all' трактуємо як admin/vip (моделі тут не чіпаємо — ручні)
        $repoType = $type === 'all' ? 'admin' : $type;
        return $this->wrap('revoke', $this->repo->revoke($order['id'], $repoType));
    }

    /**
     * Обгортка над результатом репозиторію: логування + єдиний формат відповіді,
     * сумісний зі старим callApi (success + message з датою).
     * Ловить DB-винятки (немає конекту до 185.252.24.118, тощо).
     */
    private function wrap(string $action, array $result): array
    {
        $status = ($result['success'] ?? false) ? 'SUCCESS' : 'FAILED';
        log_message('info', "[PrivilegeDelivery] {$status}: action={action} | {message}", [
            'action'  => $action,
            'message' => $result['message'] ?? '—',
        ]);

        return [
            'success' => $result['success'] ?? false,
            'message' => date('Y-m-d H:i:s') . " | {$action}: " . ($result['message'] ?? 'No message'),
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
}
