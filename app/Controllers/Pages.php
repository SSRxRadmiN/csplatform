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
}
