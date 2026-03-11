<?php

namespace App\Libraries;

use App\Models\SettingModel;

/**
 * CassaPayment — бібліотека для роботи з CSHOST CASSA API
 *
 * Створює платіжну форму, генерує підписи, верифікує webhook.
 */
class CassaPayment
{
    private string $cassaId;
    private string $secretKey;
    private string $cassaUrl;

    public function __construct()
    {
        $settings = new SettingModel();

        $this->cassaId   = $settings->get('cassa_id') ?? '';
        $this->secretKey = $settings->get('cassa_secret') ?? '';
        $this->cassaUrl  = $settings->get('cassa_url') ?? 'https://cshost.com.ua/cassa';
    }

    /**
     * Генерація даних для платіжної форми
     *
     * @param string $userdata  Ідентифікатор покупця (email або steam_id)
     * @param int    $sum       Сума в UAH (без копійок)
     * @param string $ps        Платіжна система: liqpay, fondy, p2p
     * @param int    $orderId   ID замовлення в нашій системі
     * @return array            Дані для POST-форми
     */
    public function createPayment(string $userdata, int $sum, string $ps, int $orderId): array
    {
        $timer = substr((string) time(), -4);
        $idpay = $this->cassaId . time() . $orderId;

        $sign = $this->generateSignature($userdata, $this->cassaId, $idpay, $this->secretKey);

        return [
            'action'   => $this->cassaUrl,
            'fields'   => [
                'userdata' => $userdata,
                'sum'      => $sum,
                'timer'    => $timer,
                'ps'       => $ps,
                'idpay'    => $idpay,
                'idcassa'  => $this->cassaId,
                'sign'     => $sign,
            ],
        ];
    }

    /**
     * Верифікація webhook від CASSA
     *
     * @param array $data POST-дані від CASSA
     * @return bool
     */
    public function verifyWebhook(array $data): bool
    {
        $required = ['sign', 'userdata', 'idcassa', 'idpay', 'status'];
        foreach ($required as $key) {
            if (empty($data[$key])) {
                return false;
            }
        }

        $expectedSign = $this->generateSignature(
            $data['userdata'],
            $data['idcassa'],
            $data['idpay'],
            $this->secretKey
        );

        return hash_equals($expectedSign, $data['sign']);
    }

    /**
     * Перевірка чи платіж успішний
     */
    public function isSuccess(array $data): bool
    {
        return ($data['status'] ?? 0) == 1;
    }

    /**
     * Генерація підпису sha256
     */
    private function generateSignature(string $userdata, string $idcassa, string $idpay, string $secretkey): string
    {
        $hashStr = $userdata . '|' . $idcassa . '|' . $idpay . '|' . $secretkey;
        return hash('sha256', $hashStr);
    }

    /**
     * Чи налаштована CASSA
     */
    public function isConfigured(): bool
    {
        return ! empty($this->cassaId)
            && ! empty($this->secretKey)
            && $this->cassaId !== 'YOUR_CASSA_ID';
    }
}
