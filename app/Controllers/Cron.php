<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Libraries\PrivilegeDelivery;
use App\Libraries\NotificationService;
use CodeIgniter\Controller;

/**
 * Cron — автоматичні задачі
 *
 * Викликається через cron URL з секретним ключем:
 *   GET /cron/expire?key=SECRET_KEY
 *
 * Або через CLI:
 *   php spark cron:expire (TODO: якщо буде потрібно)
 *
 * Рекомендований cron: кожні 15 хвилин
 *   * /15 * * * * curl -s "https://cs-headshot.com/cron/expire?key=SECRET" > /dev/null
 */
class Cron extends Controller
{
    /**
     * Перевірити прострочені привілеї та відкликати їх
     */
    public function expire()
    {
        // Верифікація ключа
        $key = $this->request->getGet('key');
        $settings = new \App\Models\SettingModel();
        $cronKey = $settings->get('cron_secret') ?? '';

        if (empty($cronKey) || $key !== $cronKey) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }

        $orderModel = new OrderModel();
        $delivery   = new PrivilegeDelivery();

        // Знаходимо прострочені привілеї
        $expired = $orderModel->getExpired();

        if (empty($expired)) {
            log_message('info', '[Cron] No expired privileges found');
            return $this->response
                ->setContentType('application/json')
                ->setBody(json_encode(['processed' => 0, 'message' => 'No expired orders']));
        }

        $results = [];

        foreach ($expired as $order) {
            // Визначаємо тип для revoke
            $catSlug = $this->getCategorySlug($order);
            $revokeType = ($catSlug === 'models') ? 'model' : 'admin';

            // Відкликаємо через VPS API
            $result = $delivery->revoke($order, $revokeType);

            // Оновлюємо статус замовлення
            $logEntry = date('Y-m-d H:i:s') . ' | Cron: expired → revoked';
            if (! empty($result['message'])) {
                $logEntry .= ' | ' . $result['message'];
            }

            $existingLog = $order['delivery_log'] ?? '';
            $newLog = $existingLog ? $existingLog . "\n" . $logEntry : $logEntry;

            $orderModel->update($order['id'], [
                'status'       => 'expired',
                'delivery_log' => $newLog,
            ]);

            $results[] = [
                'order_id' => $order['id'],
                'steam_id' => $order['steam_id'],
                'type'     => $revokeType,
                'result'   => $result['success'] ? 'ok' : 'failed',
            ];

            log_message('info', '[Cron] Expired order #{id}: {result}', [
                'id'     => $order['id'],
                'result' => $result['success'] ? 'revoked' : 'failed: ' . ($result['message'] ?? ''),
            ]);
        }

        $response = [
            'processed' => count($results),
            'results'   => $results,
        ];

        // Telegram нотифікація адміну
        try {
            $notify = new NotificationService();
            $notify->notifyAdminExpired(count($results), $results);
        } catch (\Throwable $e) {
            log_message('error', '[Cron] Notification error: ' . $e->getMessage());
        }

        return $this->response
            ->setContentType('application/json')
            ->setBody(json_encode($response, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Health check для моніторингу
     */
    public function health()
    {
        return $this->response
            ->setContentType('application/json')
            ->setBody(json_encode(['status' => 'ok', 'time' => date('Y-m-d H:i:s')]));
    }

    /**
     * Оновити статус сервера через UDP A2S_INFO
     * GET /cron/serverstats?key=SECRET
     *
     * Рекомендований cron: кожні 2 хвилини
     *   * /2 * * * * curl -s "https://cs-headshot.com/cron/serverstats?key=SECRET" > /dev/null
     */
    public function serverstats()
    {
        $key = $this->request->getGet('key');
        $settings = new \App\Models\SettingModel();
        $cronKey = $settings->get('cron_secret') ?? '';

        if (empty($cronKey) || $key !== $cronKey) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }

        $result = \App\Libraries\ServerQuery::updateServerStats(1);

        log_message('info', '[Cron] ServerStats: ' . json_encode($result));

        return $this->response
            ->setContentType('application/json')
            ->setBody(json_encode($result, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Визначити slug категорії з order
     */
    private function getCategorySlug(array $order): string
    {
        if (! empty($order['product_id'])) {
            $db = \Config\Database::connect();
            $row = $db->table('products')
                      ->select('categories.slug')
                      ->join('categories', 'categories.id = products.category_id', 'left')
                      ->where('products.id', $order['product_id'])
                      ->get()
                      ->getRowArray();
            return $row['slug'] ?? 'other';
        }
        return 'other';
    }
}
