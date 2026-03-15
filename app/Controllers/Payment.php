<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Libraries\CassaPayment;
use App\Libraries\PrivilegeDelivery;
use App\Libraries\NotificationService;
use CodeIgniter\Controller;

/**
 * Payment — обробка webhook від CASSA
 *
 * Не наслідує BaseController — не потрібні сесії/хелпери.
 * CSRF вимкнений для цього маршруту в Filters.php.
 */
class Payment extends Controller
{
    /**
     * POST /payment/callback
     * Webhook від CASSA після оплати
     */
    public function callback()
    {
        $data = $this->request->getPost();

        log_message('info', '[Payment] Webhook received: ' . json_encode($data));

        // Верифікація підпису
        $cassa = new CassaPayment();

        if (! $cassa->verifyWebhook($data)) {
            log_message('warning', '[Payment] Invalid signature!');
            return $this->response->setStatusCode(403)->setBody('Invalid signature');
        }

        // Перевірка статусу
        if (! $cassa->isSuccess($data)) {
            log_message('info', '[Payment] Status not success: ' . ($data['status'] ?? '?'));
            return $this->response->setStatusCode(200)->setBody('OK');
        }

        // Знаходимо замовлення
        $orderModel = new OrderModel();
        $order = $orderModel
            ->select('orders.*, users.username, users.email')
            ->join('users', 'users.id = orders.user_id', 'left')
            ->where('orders.payment_id', $data['idpay'])
            ->first();

        if (! $order) {
            log_message('error', '[Payment] Order not found for idpay: ' . $data['idpay']);
            return $this->response->setStatusCode(404)->setBody('Order not found');
        }

        // Захист від повторної обробки
        if ($order['status'] !== 'pending') {
            log_message('info', '[Payment] Order #{id} already processed: {status}', [
                'id'     => $order['id'],
                'status' => $order['status'],
            ]);
            return $this->response->setStatusCode(200)->setBody('Already processed');
        }

        // Перевірка суми
        if ((int) $data['sum'] !== (int) $order['amount']) {
            log_message('error', '[Payment] Amount mismatch! Expected {expected}, got {got}', [
                'expected' => $order['amount'],
                'got'      => $data['sum'],
            ]);
            $orderModel->update($order['id'], [
                'status'       => 'failed',
                'delivery_log' => 'Amount mismatch: expected ' . $order['amount'] . ', got ' . $data['sum'],
            ]);
            return $this->response->setStatusCode(200)->setBody('Amount mismatch');
        }

        // Оновлюємо статус на "paid"
        $orderModel->update($order['id'], [
            'status'  => 'paid',
            'paid_at' => date('Y-m-d H:i:s'),
        ]);

        // Видача привілегії
        $productModel = new ProductModel();
        $product = $productModel
            ->select('products.*, categories.slug as cat_slug')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->where('products.id', $order['product_id'])
            ->first();

        $delivery = new PrivilegeDelivery();
        $result = $delivery->deliver($order, $product ?? []);

        // Обчислюємо дату закінчення
        $expiresAt = null;
        if (! empty($order['duration_days']) && (int) $order['duration_days'] > 0) {
            $expiresAt = date('Y-m-d H:i:s', strtotime('+' . (int) $order['duration_days'] . ' days'));
        }

        // Фінальне оновлення
        $orderModel->update($order['id'], [
            'status'       => $result['success'] ? 'delivered' : 'paid',
            'delivered_at' => $result['success'] ? date('Y-m-d H:i:s') : null,
            'expires_at'   => $expiresAt,
            'delivery_log' => $result['message'],
        ]);

        log_message('info', '[Payment] Order #{id} completed: {status}', [
            'id'     => $order['id'],
            'status' => $result['success'] ? 'delivered' : 'paid',
        ]);

        // Сповіщення
        try {
            $notify = new NotificationService();

            // Email покупцю
            $notify->notifyBuyerPaid($order, [
                'username' => $order['username'] ?? '',
                'email'    => $order['email'] ?? '',
            ]);

            // Telegram адміну
            $order['delivery_log'] = $result['message'];
            $notify->notifyAdminNewOrder($order, $result['success']);

            // Якщо доставка не вдалась — окреме попередження
            if (! $result['success']) {
                $notify->notifyAdminDeliveryFailed($order, $result['message']);
            }
        } catch (\Throwable $e) {
            log_message('error', '[Payment] Notification error: ' . $e->getMessage());
        }

        return $this->response->setStatusCode(200)->setBody('OK');
    }
}
