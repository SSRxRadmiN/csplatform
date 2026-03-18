<?php

/**
 * Get current language
 */
function current_lang(): string
{
    return session()->get('lang') ?? 'ua';
}

/**
 * Get localized product name
 */
function product_name(array $product): string
{
    $lang = current_lang();
    return ($lang === 'en' && !empty($product['name_en'])) ? $product['name_en'] : $product['name_ua'];
}

/**
 * Get localized product description
 */
function product_desc(array $product): string
{
    $lang = current_lang();
    return ($lang === 'en' && !empty($product['description_en'])) ? $product['description_en'] : ($product['description_ua'] ?? '');
}

/**
 * Get localized category name
 */
function cat_name(array $cat): string
{
    $lang = current_lang();
    return ($lang === 'en' && !empty($cat['name_en'])) ? $cat['name_en'] : ($cat['name_ua'] ?? $cat['name'] ?? '');
}

/**
 * Pluralize days in UA/EN
 */
function days_text(int $days): string
{
    $lang = current_lang();
    if ($lang === 'en') {
        return $days . ' ' . ($days === 1 ? 'day' : 'days');
    }
    // Ukrainian
    if ($days === 1) return $days . ' день';
    if ($days >= 2 && $days <= 4) return $days . ' дні';
    return $days . ' днів';
}

/**
 * Duration label (days or one-time)
 */
function duration_text(int $days): string
{
    if ($days > 0) {
        return days_text($days);
    }
    return current_lang() === 'en' ? 'One-time' : 'Одноразово';
}

/**
 * Order status text
 */
function order_status_text(string $status): array
{
    $lang = current_lang();
    $map = [
        'ua' => [
            'pending'   => ['Очікує', 'pending'],
            'paid'      => ['Оплачено', 'paid'],
            'delivered' => ['Доставлено', 'delivered'],
            'failed'    => ['Помилка', 'failed'],
            'expired'   => ['Завершено', 'expired'],
            'refunded'  => ['Повернено', 'refunded'],
        ],
        'en' => [
            'pending'   => ['Pending', 'pending'],
            'paid'      => ['Paid', 'paid'],
            'delivered' => ['Delivered', 'delivered'],
            'failed'    => ['Failed', 'failed'],
            'expired'   => ['Expired', 'expired'],
            'refunded'  => ['Refunded', 'refunded'],
        ],
    ];
    return ($map[$lang] ?? $map['ua'])[$status] ?? ['—', 'unknown'];
}
