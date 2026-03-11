<?php

namespace App\Libraries;

/**
 * PrivilegeDelivery — видача привілегій на сервер
 *
 * ЗАГЛУШКА: буде переписана після фіналізації системи привілегій.
 * Зараз тільки логує спробу видачі.
 */
class PrivilegeDelivery
{
    /**
     * Видати привілегію гравцю
     *
     * @param array $order   Дані замовлення
     * @param array $product Дані товару
     * @return array ['success' => bool, 'message' => string]
     */
    public function deliver(array $order, array $product): array
    {
        // TODO: реалізувати після фіналізації системи привілегій
        // Тут буде HTTP запит до VPS API:
        // POST http://31.42.190.78/api/privilege.php
        // з параметрами: steam_id, amx_flags, amx_access, duration_days

        log_message('info', '[PrivilegeDelivery] STUB — Order #{id}, Steam: {steam}, Flags: {flags}', [
            'id'    => $order['id'],
            'steam' => $order['steam_id'],
            'flags' => $product['amx_flags'] ?? '—',
        ]);

        return [
            'success' => true,
            'message' => 'STUB: привілегія зареєстрована, очікує ручної видачі.',
        ];
    }
}
