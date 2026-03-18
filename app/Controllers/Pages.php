<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function faq()
    {
        return view('layouts/main', [
            'page'      => 'pages/faq',
            'title'     => 'FAQ — CS Headshot',
            'pageClass' => 'page-faq',
        ]);
    }

    public function privacy()
    {
        return view('layouts/main', [
            'page'      => 'pages/privacy',
            'title'     => 'Послуги — CS Headshot',
            'pageClass' => 'page-privacy',
        ]);
    }
}
