<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function faq()
    {
        return view('layouts/main', [
            'page'            => 'pages/faq',
            'title'           => 'FAQ — CS Headshot',
            'metaTitle'       => 'FAQ — Часті питання про CS Headshot',
            'metaDescription' => 'Відповіді на часті питання про VIP, моделі, оплату, розбан та роботу сервера Реальні Кабани CS 1.6.',
            'pageClass'       => 'page-faq',
        ]);
    }

    public function privacy()
    {
        return view('layouts/main', [
            'page'            => 'pages/privacy',
            'title'           => 'Умови та конфіденційність — CS Headshot',
            'metaTitle'       => 'Політика конфіденційності — CS Headshot',
            'metaDescription' => 'Умови використання платформи CS Headshot та політика конфіденційності.',
            'pageClass'       => 'page-privacy',
        ]);
    }

    public function privileges()
    {
        return view('layouts/main', [
            'page'            => 'pages/privileges',
            'title'           => 'Привілегії — CS Headshot',
            'metaTitle'       => 'Привілегії на сервері — VIP та Меценат | CS Headshot',
            'metaDescription' => 'Порівняння привілегій на сервері Реальні Кабани CS 1.6 — що отримує звичайний гравець, VIP та Меценат. Повна таблиця бонусів.',
            'metaKeywords'    => 'привілегії cs 1.6, vip cs 1.6, меценат cs 1.6, бонуси сервер cs, реальні кабани привілегії',
            'pageClass'       => 'page-privileges',
        ]);
    }

    public function download()
    {
        return view('layouts/main', [
            'page'            => 'download/index',
            'title'           => 'Скачати CS 1.6 — Українська збірка без вірусів | CS Headshot',
            'metaTitle'       => 'Скачати CS 1.6 — Українська збірка без вірусів | CS Headshot',
            'metaDescription' => 'Завантажити Counter-Strike 1.6 — українська збірка без вірусів, з високим FPS та онлайн серверами. Працює на Windows 10/11. Безкоштовно!',
            'metaKeywords'    => 'скачати кс 1.6, скачати cs 1.6, counter strike 1.6 скачати, cs 1.6 українська збірка, кс 1.6 без вірусів, cs 1.6 безкоштовно',
            'pageClass'       => 'page-download',
        ]);
    }
}
