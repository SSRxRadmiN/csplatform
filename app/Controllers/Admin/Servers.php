<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
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
     * Збір полів з форми. Покривається лише базовий набір;
     * поля db_xxx та rcon свідомо НЕ редагуються через адмінку (закладено на v2).
     */
    private function collectFormData(): array
    {
        $data = $this->request->getPost([
            'name', 'ip', 'port', 'country',
            'description_ua', 'description_en',
            'api_url', 'api_key', 'banner_url',
            'is_active',
        ]);

        $data['is_active'] = $data['is_active'] ?? 0;
        $data['port']      = (int) ($data['port'] ?? 0);

        return $data;
    }
}
