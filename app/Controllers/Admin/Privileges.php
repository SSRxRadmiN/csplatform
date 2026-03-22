<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class Privileges extends BaseController
{
    private string $apiUrl;
    private string $apiToken;

    public function __construct()
    {
        $settings = new SettingModel();
        $this->apiUrl   = $settings->get('vps_api_url') ?? 'http://31.42.190.78/api/privilege';
        $this->apiToken = $settings->get('vps_api_token') ?? '';
    }

    /**
     * GET /admin/privileges — список привілегій
     */
    public function index()
    {
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 50);
        $search  = $this->request->getGet('search') ?? '';

        $query = http_build_query([
            'action'   => 'privileges',
            'token'    => $this->apiToken,
            'page'     => $page,
            'per_page' => $perPage,
            'search'   => $search,
        ]);

        $response = $this->apiGet($query);

        return view('layouts/main', [
            'page'       => 'admin/privileges/index',
            'title'      => 'Привілеї — Адмін',
            'privileges' => $response['privileges'] ?? [],
            'total'      => $response['total'] ?? 0,
            'pages'      => $response['pages'] ?? 1,
            'currentPage' => $page,
            'perPage'    => $perPage,
            'search'     => $search,
            'apiError'   => $response['error'] ?? null,
        ]);
    }

    /**
     * POST /admin/privileges/add — додати привілегію вручну
     */
    public function add()
    {
        $data = $this->request->getPost([
            'steam_id', 'access', 'flags', 'nickname', 'days',
        ]);

        $result = $this->apiPost('privilege_add', $data);

        if (!empty($result['success'])) {
            return redirect()->to('/admin/privileges')
                ->with('success', 'Привілегію додано: ' . ($result['username'] ?? ''));
        }

        return redirect()->to('/admin/privileges')
            ->with('error', 'Помилка: ' . ($result['error'] ?? $result['message'] ?? 'Unknown'));
    }

    /**
     * POST /admin/privileges/update/{id} — оновити привілегію
     */
    public function update(int $id)
    {
        $data = $this->request->getPost([
            'access', 'flags', 'steamid', 'nickname', 'days',
        ]);

        // Прибираємо порожні значення
        $data = array_filter($data, fn($v) => $v !== null && $v !== '');
        $data['id'] = $id;

        $result = $this->apiPost('privilege_update', $data);

        if (!empty($result['success'])) {
            return redirect()->to('/admin/privileges')
                ->with('success', "Привілегію #{$id} оновлено");
        }

        return redirect()->to('/admin/privileges')
            ->with('error', 'Помилка: ' . ($result['error'] ?? 'Unknown'));
    }

    /**
     * POST /admin/privileges/delete/{id} — видалити привілегію
     */
    public function delete(int $id)
    {
        $result = $this->apiPost('privilege_delete', ['id' => $id]);

        if (!empty($result['success'])) {
            return redirect()->to('/admin/privileges')
                ->with('success', "Привілегію #{$id} видалено");
        }

        return redirect()->to('/admin/privileges')
            ->with('error', 'Помилка: ' . ($result['error'] ?? 'Unknown'));
    }

    /**
     * GET запит до VPS API
     */
    private function apiGet(string $query): array
    {
        $url = rtrim($this->apiUrl, '/') . '?' . $query;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return ['error' => "API connection failed: {$error}"];
        }

        return json_decode($response, true) ?? ['error' => 'Invalid API response'];
    }

    /**
     * POST запит до VPS API
     */
    private function apiPost(string $action, array $params): array
    {
        $params['action'] = $action;
        $params['token']  = $this->apiToken;

        $url = rtrim($this->apiUrl, '/');

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return ['error' => "API connection failed: {$error}"];
        }

        return json_decode($response, true) ?? ['error' => 'Invalid API response'];
    }
}
