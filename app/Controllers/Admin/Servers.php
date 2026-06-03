<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ServerQuery;
use App\Models\ServerModel;

class Servers extends BaseController
{
    public function index()
    {
        $serverModel = new ServerModel();

        $servers = $serverModel
            ->orderBy('id', 'ASC')
            ->findAll();

        return view('layouts/main', [
            'page'    => 'admin/servers/index',
            'title'   => 'Сервери — Адмін',
            'servers' => $servers,
        ]);
    }

    public function create()
    {
        return view('layouts/main', [
            'page'   => 'admin/servers/form',
            'title'  => 'Новий сервер — Адмін',
            'server' => null,
        ]);
    }

    public function store()
    {
        $serverModel = new ServerModel();

        $data = $this->collectFormData();

        if (! $serverModel->insert($data)) {
            return redirect()->back()->withInput()
                ->with('errors', $serverModel->errors());
        }

        return redirect()->to('/admin/servers')
            ->with('success', 'Сервер створено');
    }

    public function edit(int $id)
    {
        $serverModel = new ServerModel();

        $server = $serverModel->find($id);
        if (! $server) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('layouts/main', [
            'page'   => 'admin/servers/form',
            'title'  => 'Редагувати: ' . $server['name'] . ' — Адмін',
            'server' => $server,
        ]);
    }

    public function update(int $id)
    {
        $serverModel = new ServerModel();

        $server = $serverModel->find($id);
        if (! $server) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = $this->collectFormData();

        if (! $serverModel->update($id, $data)) {
            return redirect()->back()->withInput()
                ->with('errors', $serverModel->errors());
        }

        return redirect()->to('/admin/servers')
            ->with('success', 'Сервер оновлено');
    }

    public function delete(int $id)
    {
        $serverModel = new ServerModel();

        $server = $serverModel->find($id);
        if (! $server) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // М'яке видалення — деактивація (як у Categories)
        $serverModel->update($id, ['is_active' => 0]);

        return redirect()->to('/admin/servers')
            ->with('success', 'Сервер деактивовано');
    }

    /**
     * AJAX: перевірка статусу ігрового сервера за IP:port.
     *
     * Викликається з форми /admin/servers/create і /edit при кліку
     * "Перевірити статус". Робить кілька спроб A2S_INFO (UDP) бо UDP
     * може губити пакети.
     *
     * Маршрут: GET /admin/servers/probe?ip=X&port=Y
     *
     * Повертає JSON:
     *   { ok: true, name, map, players, max_players, bots, attempt: 1..3 }
     *   { ok: false, error: "..." }
     */
    public function probe()
    {
        $ip   = trim((string) $this->request->getGet('ip'));
        $port = (int) $this->request->getGet('port');

        // Базова валідація — щоб не відкривати UDP до чого попало
        if ($ip === '' || $port < 1 || $port > 65535) {
            return $this->jsonProbe(false, 'IP або порт некоректні');
        }
        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            return $this->jsonProbe(false, 'Невалідний IP');
        }

        // До 3 спроб з невеликою паузою — UDP пакети інколи губляться
        $info = null;
        $attempt = 0;
        for ($i = 1; $i <= 3; $i++) {
            $attempt = $i;
            $info = ServerQuery::getInfo($ip, $port);
            if ($info !== null) {
                break;
            }
            if ($i < 3) {
                usleep(300_000); // 300 мс між спробами
            }
        }

        if ($info === null) {
            return $this->jsonProbe(false, 'Сервер не відповів після 3 спроб');
        }

        return $this->response
            ->setContentType('application/json')
            ->setJSON([
                'ok'          => true,
                'attempt'     => $attempt,
                'name'        => $info['hostname'],
                'map'         => $info['map'],
                'players'     => $info['players'],
                'max_players' => $info['max_players'],
                'bots'        => $info['bots'] ?? 0,
                'game'        => $info['game'] ?? '',
                'protocol'    => $info['protocol'] ?? 0,
            ]);
    }

    private function jsonProbe(bool $ok, string $msg)
    {
        return $this->response
            ->setContentType('application/json')
            ->setJSON([
                'ok'    => $ok,
                'error' => $msg,
            ]);
    }

    /**
     * Збір полів з форми. Покривається лише базовий набір;
     * поля db_xxx та rcon свідомо НЕ редагуються через адмінку (закладено на v2).
     */
    private function collectFormData(): array
    {
        $data = $this->request->getPost([
            'name', 'ip', 'port', 'country',
            'description_ua', 'description_en',
            'api_url', 'api_key', 'banner_url',
            'is_active', 'is_verified',
        ]);

        $data['is_active']   = $data['is_active'] ?? 0;
        $data['is_verified'] = $data['is_verified'] ?? 0;
        $data['port']        = (int) ($data['port'] ?? 0);

        return $data;
    }
}
