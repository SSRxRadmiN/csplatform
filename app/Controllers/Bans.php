<?php

namespace App\Controllers;

use App\Models\SettingModel;

class Bans extends BaseController
{
    public function index()
    {
        $settingModel = new SettingModel();
        $vpsUrl   = $settingModel->get('vps_api_url');
        $vpsToken = $settingModel->get('vps_api_token');

        $page        = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage     = (int) ($this->request->getGet('per_page') ?? 20);
        $search      = trim($this->request->getGet('search') ?? '');
        $showExpired = $this->request->getGet('show_expired') ?? '0';

        if ($perPage < 10) $perPage = 10;
        if ($perPage > 100) $perPage = 100;

        $bans  = null;
        $error = null;

        if ($vpsUrl && $vpsToken) {
            $queryParams = http_build_query([
                'action'       => 'bans',
                'token'        => $vpsToken,
                'page'         => $page,
                'per_page'     => $perPage,
                'search'       => $search,
                'show_expired' => $showExpired,
            ]);
            $url = rtrim($vpsUrl, '/') . '?' . $queryParams;

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $bans = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $error = 'Помилка декодування відповіді API';
                    $bans = null;
                }
            } else {
                $error = "Помилка підключення до VPS API (HTTP {$httpCode})";
            }
        } else {
            $error = 'VPS API не налаштований';
        }

        return view('layouts/main', [
            'page'        => 'bans/index',
            'title'       => 'Банлист — CS Headshot',
            'bans'        => $bans,
            'error'       => $error,
            'curPage'     => $page,
            'perPage'     => $perPage,
            'search'      => $search,
            'showExpired' => $showExpired,
        ]);
    }
}
