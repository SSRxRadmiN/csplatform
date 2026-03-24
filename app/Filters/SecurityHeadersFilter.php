<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * SecurityHeadersFilter — додає захисні HTTP-заголовки до кожної відповіді.
 *
 * Включає:
 * - X-Content-Type-Options (захист від MIME sniffing)
 * - X-Frame-Options (захист від clickjacking)
 * - X-XSS-Protection (legacy XSS фільтр)
 * - Referrer-Policy (контроль Referer заголовка)
 * - Permissions-Policy (блокує зайві API браузера)
 * - Content-Security-Policy (контроль завантаження ресурсів)
 * - Strict-Transport-Security (примусовий HTTPS)
 */
class SecurityHeadersFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Нічого не робимо до запиту
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // --- Базові заголовки ---
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->setHeader('X-XSS-Protection', '1; mode=block');
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        // --- HSTS (тільки для production, 1 рік) ---
        if (ENVIRONMENT === 'production') {
            $response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // --- Permissions-Policy (блокуємо зайві API) ---
        $response->setHeader('Permissions-Policy', implode(', ', [
            'camera=()',
            'microphone=()',
            'geolocation=()',
            'payment=()',
            'usb=()',
            'magnetometer=()',
            'gyroscope=()',
            'accelerometer=()',
        ]));

        // --- Content-Security-Policy ---
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline'",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: https:",
            "connect-src 'self'",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self' https://www.cassa.exchange",
        ]);

        $response->setHeader('Content-Security-Policy', $csp);

        return $response;
    }
}
