<?php

namespace App\Libraries;

use App\Models\SettingModel;

/**
 * NotificationService — Email + Telegram сповіщення
 *
 * Settings:
 *   - telegram_bot_token:  токен бота
 *   - telegram_admin_chat: chat_id адміна для нотифікацій
 *   - email_enabled:       1/0
 *   - email_from:          email відправника
 *   - email_from_name:     ім'я відправника
 */
class NotificationService
{
    private string $botToken;
    private string $adminChatId;
    private bool   $emailEnabled;
    private string $emailFrom;
    private string $emailFromName;

    public function __construct()
    {
        $settings = new SettingModel();

        $this->botToken     = $settings->get('telegram_bot_token') ?? '';
        $this->adminChatId  = $settings->get('telegram_admin_chat') ?? '';
        $this->emailEnabled = (bool) ($settings->get('email_enabled') ?? 0);
        $this->emailFrom    = $settings->get('email_from') ?? 'noreply@cs-headshot.com';
        $this->emailFromName = $settings->get('email_from_name') ?? 'CS Headshot';
    }

    // ==========================================
    // Після успішної оплати — покупцю
    // ==========================================

    /**
     * Повідомити покупця про успішну оплату
     */
    public function notifyBuyerPaid(array $order, array $user): void
    {
        if (! $this->emailEnabled || empty($user['email'])) {
            return;
        }

        $productName = $order['product_name'] ?? 'Привілегія';
        $amount      = (int) ($order['amount'] ?? 0);
        $steamId     = $order['steam_id'] ?? '';

        $subject = "Оплата підтверджена — {$productName}";
        $body = "Вітаємо, {$user['username']}!\n\n"
              . "Ваше замовлення #{$order['id']} успішно оплачене.\n\n"
              . "Товар: {$productName}\n"
              . "Сума: {$amount} ₴\n"
              . "Steam ID: {$steamId}\n\n"
              . "Привілегія буде активована автоматично.\n"
              . "Після зміни карти або реконнекту — перевірте статус.\n\n"
              . "Якщо є питання — пишіть в Telegram.\n\n"
              . "— CS Headshot";

        $this->sendEmail($user['email'], $subject, $body);
    }

    // ==========================================
    // Нове замовлення — адміну в Telegram
    // ==========================================

    /**
     * Повідомити адміна про нове замовлення
     */
    public function notifyAdminNewOrder(array $order, bool $delivered = false): void
    {
        if (empty($this->botToken) || empty($this->adminChatId)) {
            return;
        }

        $status = $delivered ? '✅ Доставлено' : '💰 Оплачено';
        $productName = $order['product_name'] ?? '—';
        $amount = (int) ($order['amount'] ?? 0);

        $text = "{$status}\n\n"
              . "📦 Замовлення #{$order['id']}\n"
              . "🎮 {$productName}\n"
              . "💵 {$amount} ₴\n"
              . "🆔 {$order['steam_id']}\n"
              . "👤 {$order['username'] ?? '—'}";

        if (! empty($order['delivery_log'])) {
            // Тільки останній рядок логу
            $logLines = explode("\n", $order['delivery_log']);
            $lastLog = end($logLines);
            $text .= "\n\n📋 " . mb_substr($lastLog, 0, 200);
        }

        $this->sendTelegram($this->adminChatId, $text);
    }

    /**
     * Повідомити адміна про помилку доставки
     */
    public function notifyAdminDeliveryFailed(array $order, string $error): void
    {
        if (empty($this->botToken) || empty($this->adminChatId)) {
            return;
        }

        $text = "⚠️ ПОМИЛКА ДОСТАВКИ\n\n"
              . "Замовлення #{$order['id']}\n"
              . "Steam: {$order['steam_id']}\n"
              . "Товар: {$order['product_name'] ?? '—'}\n\n"
              . "Помилка: {$error}\n\n"
              . "Потрібна ручна видача!";

        $this->sendTelegram($this->adminChatId, $text);
    }

    /**
     * Повідомити адміна про expired привілеї (крон)
     */
    public function notifyAdminExpired(int $count, array $details = []): void
    {
        if (empty($this->botToken) || empty($this->adminChatId) || $count === 0) {
            return;
        }

        $text = "🕐 Крон: {$count} привілегій прострочено\n";
        foreach (array_slice($details, 0, 10) as $d) {
            $text .= "\n• #{$d['order_id']} {$d['steam_id']} — {$d['result']}";
        }

        $this->sendTelegram($this->adminChatId, $text);
    }

    // ==========================================
    // Transport: Email
    // ==========================================

    private function sendEmail(string $to, string $subject, string $body): bool
    {
        try {
            $email = \Config\Services::email();
            $email->setFrom($this->emailFrom, $this->emailFromName);
            $email->setTo($to);
            $email->setSubject($subject);
            $email->setMessage(nl2br($body));
            $email->setMailType('html');

            if ($email->send()) {
                log_message('info', "[Notification] Email sent to {$to}: {$subject}");
                return true;
            } else {
                log_message('error', "[Notification] Email failed to {$to}: " . $email->printDebugger());
                return false;
            }
        } catch (\Throwable $e) {
            log_message('error', "[Notification] Email exception: {$e->getMessage()}");
            return false;
        }
    }

    // ==========================================
    // Transport: Telegram
    // ==========================================

    private function sendTelegram(string $chatId, string $text): bool
    {
        if (empty($this->botToken)) {
            return false;
        }

        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([
                'chat_id'    => $chatId,
                'text'       => $text,
                'parse_mode' => 'HTML',
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            log_message('info', "[Notification] Telegram sent to {$chatId}");
            return true;
        } else {
            log_message('error', "[Notification] Telegram failed (HTTP {$httpCode}): {$response}");
            return false;
        }
    }
}
