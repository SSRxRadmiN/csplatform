<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;

class Sitemap extends BaseController
{
    public function index()
    {
        $productModel  = new ProductModel();
        $categoryModel = new CategoryModel();

        $baseUrl = 'https://cs-headshot.com';

        // Static pages
        $urls = [
            ['loc' => $baseUrl . '/',        'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => $baseUrl . '/shop',    'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => $baseUrl . '/stats',   'priority' => '0.7', 'changefreq' => 'hourly'],
            ['loc' => $baseUrl . '/bans',    'priority' => '0.6', 'changefreq' => 'daily'],
            ['loc' => $baseUrl . '/faq',     'priority' => '0.4', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/privacy', 'priority' => '0.3', 'changefreq' => 'monthly'],
        ];

        // Product pages
        $products = $productModel->where('is_active', 1)->findAll();
        foreach ($products as $p) {
            $urls[] = [
                'loc'        => $baseUrl . '/shop/' . $p['id'],
                'priority'   => '0.8',
                'changefreq' => 'weekly',
            ];
        }

        // Build XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . esc($u['loc']) . "</loc>\n";
            $xml .= "    <changefreq>" . $u['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $u['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $this->response
            ->setHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->setBody($xml);
    }
}
