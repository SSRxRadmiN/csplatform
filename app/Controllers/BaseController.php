<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    /**
     * Хелпери доступні у ВСІХ контролерах і в'юшках
     */
    protected $helpers = ['form', 'url', 'antispam', 'lang'];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Set locale from session
        $locale = session()->get('lang') ?? 'ua';
        service('language')->setLocale($locale);
    }
}
