<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\ServerModel;
use App\Models\OrderModel;
use App\Libraries\CassaPayment;

class Buy extends BaseController
{
    /**
     * Сторінка оформлення замовлення
     */
    public function index(int $productId)
    {
        $productModel = new ProductModel();
        $product = $productModel->find($productId);

        if (! $product || ! $product['is_active']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $serverModel = new ServerModel();
        $server = $serverModel->find($product['server_id']);

        $cassa = new CassaPayment();

        if (! $cassa->isConfigured()) {
            return redirect()->to('/shop/' . $productId)
                ->with('error', 'Оплата тимчасово недоступна');
        }

        return view('layouts/main', [
            'page'    => 'buy/index',
            'title'   => 'Оформлення — CS Headshot',
            'product' => $product,
            'server'  => $server,
            'steamId' => session()->get('user_steam'),
        ]);
    }

    /**
     * Створення замовлення + редірект на CASSA
     */
    public function process(int $productId)
    {
        $productModel = new ProductModel();
        $product = $productModel->find($productId);

        if (! $product || ! $product['is_active']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Валідація
        $ps = $this->request->getPost('ps');
        if (! in_array($ps, ['liqpay', 'fondy', 'p2p'])) {
            return redirect()->back()->with('error', 'Оберіть спосіб оплати');
        }

        $userId  = session()->get('user_id');
        $steamId = session()->get('user_steam');
        $email   = session()->get('user_email');
        $lang    = session()->get('lang') ?? 'ua';

        // Створюємо замовлення
        $orderModel = new OrderModel();
        $orderId = $orderModel->insert([
            'user_id'      => $userId,
            'product_id'   => $product['id'],
            'server_id'    => $product['server_id'],
            'steam_id'     => $steamId,
            'amount'       => $product['price'],
            'status'       => 'pending',
            'product_name' => $product['name_' . $lang] ?? $product['name_ua'],
            'amx_access'   => $product['amx_access'],
            'amx_flags'    => $product['amx_flags'],
            'duration_days' => $product['duration_days'],
        ]);

        if (! $orderId) {
            return redirect()->back()->with('error', 'Помилка створення замовлення');
        }

        // Генеруємо платіж
        $cassa = new CassaPayment();
        $payment = $cassa->createPayment(
            $email,
            (int) $product['price'],
            $ps,
            $orderId
        );

        // Зберігаємо payment_id
        $orderModel->update($orderId, [
            'payment_id' => $payment['fields']['idpay'],
        ]);

        // Показуємо форму-редірект на CASSA
        return view('layouts/main', [
            'page'    => 'buy/redirect',
            'title'   => 'Переадресація на оплату...',
            'payment' => $payment,
        ]);
    }

    /**
     * Сторінка успішної оплати (юзер повертається сюди з CASSA)
     */
    public function success()
    {
        return view('layouts/main', [
            'page'  => 'buy/success',
            'title' => 'Оплата успішна — CS Headshot',
        ]);
    }

    /**
     * Сторінка невдалої оплати
     */
    public function failed()
    {
        return view('layouts/main', [
            'page'  => 'buy/failed',
            'title' => 'Помилка оплати — CS Headshot',
        ]);
    }
}
