<?php

namespace App\Controllers;

use App\Models\SettingModel;

class Stats extends BaseController
{
    public function index()
    {
        $settingModel = new SettingModel();
        $vpsUrl   = $settingModel->get('vps_api_url');
        $vpsToken = $settingModel->get('vps_api_token');

        $page    = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 30);
        $search  = trim($this->request->getGet('search') ?? '');
        if ($page < 1) $page = 1;
        if ($perPage < 10) $perPage = 10;
        if ($perPage > 100) $perPage = 100;

        $stats = null;
        $error = null;

        if ($vpsUrl && $vpsToken) {
            $queryParams = http_build_query([
                'action'   => 'stats',
                'token'    => $vpsToken,
                'page'     => $page,
                'per_page' => $perPage,
                'search'   => $search,
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
                $stats = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $error = 'Помилка декодування відповіді API';
                    $stats = null;
                }
            } else {
                $error = "Помилка підключення до VPS API (HTTP {$httpCode})";
            }
        } else {
            $error = 'VPS API не налаштований';
        }

        return view('layouts/main', [
            'page'     => 'stats/index',
            'title'    => 'Статистика гравців — CS Headshot',
            'stats'    => $stats,
            'error'    => $error,
            'curPage'  => $page,
            'perPage'  => $perPage,
            'search'   => $search,
        ]);
    }

    public function player(int $id)
    {
        $settingModel = new SettingModel();
        $vpsUrl   = $settingModel->get('vps_api_url');
        $vpsToken = $settingModel->get('vps_api_token');

        $player = null;
        $error  = null;

        if ($vpsUrl && $vpsToken) {
            $url = rtrim($vpsUrl, '/') . "?action=player&token=" . urlencode($vpsToken)
                 . "&id={$id}";

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if (!empty($data['player'])) {
                    $player = $data;
                } else {
                    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                }
            } else {
                $error = "Помилка підключення до VPS API";
            }
        }

        return view('layouts/main', [
            'page'       => 'stats/player',
            'title'      => 'Гравець — CS Headshot',
            'playerData' => $player,
            'error'      => $error,
        ]);
    }
}
