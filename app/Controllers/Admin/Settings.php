<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class Settings extends BaseController
{
    public function index()
    {
        $settingModel = new SettingModel();
        $settings = $settingModel->getAll();

        return view('layouts/main', [
            'page'     => 'admin/settings/index',
            'title'    => 'Налаштування — Адмін',
            'settings' => $settings,
        ]);
    }

    public function update()
    {
        $settingModel = new SettingModel();

        $fields = $this->request->getPost();
        unset($fields['csrf_test_name'], $fields[csrf_token()]);

        foreach ($fields as $key => $value) {
            $settingModel->setSetting($key, $value);
        }

        return redirect()->to('/admin/settings')
            ->with('success', 'Налаштування збережено');
    }
}
