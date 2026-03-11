<?php
$lang = session()->get('lang') ?? 'ua';
$name = ($lang === 'en' && !empty($product['name_en'])) ? $product['name_en'] : $product['name_ua'];
$desc = ($lang === 'en' && !empty($product['description_en'])) ? $product['description_en'] : ($product['description_ua'] ?? '');
$duration = (int) $product['duration_days'];
$cat = $product['category'] ?? 'other';

$catLabels = [
    'vip'   => ['label' => 'VIP статус', 'icon' => '⭐'],
    'admin' => ['label' => 'Адмін-права', 'icon' => '🛡️'],
    'unban' => ['label' => 'Розбан', 'icon' => '🔓'],
    'other' => ['label' => 'Інше', 'icon' => '📦'],
];
$catInfo = $catLabels[$cat] ?? $catLabels['other'];
?>

<section class="product-page">
    <!-- Навігація -->
    <div class="product-breadcrumb">
        <a href="/shop">← Назад до магазину</a>
    </div>

    <div class="product-layout">
        <!-- Ліва частина — інфо -->
        <div class="product-info">
            <div class="product-badge product-badge--<?= esc($cat) ?>">
                <?= $catInfo['icon'] ?> <?= esc($catInfo['label']) ?>
            </div>

            <h1 class="product-name"><?= esc($name) ?></h1>

            <?php if ($desc): ?>
                <div class="product-description">
                    <?= nl2br(esc($desc)) ?>
                </div>
            <?php endif ?>

            <!-- Характеристики -->
            <div class="product-specs">
                <div class="product-spec">
                    <span class="product-spec-label">Сервер</span>
                    <span class="product-spec-value"><?= esc($server['name'] ?? 'Реальні Кабани') ?></span>
                </div>
                <div class="product-spec">
                    <span class="product-spec-label">Тривалість</span>
                    <span class="product-spec-value">
                        <?php if ($duration > 0): ?>
                            <?= $duration ?> <?= $duration === 1 ? 'день' : ($duration < 5 ? 'дні' : 'днів') ?>
                        <?php else: ?>
                            Одноразово
                        <?php endif ?>
                    </span>
                </div>
                <?php if (!empty($product['amx_flags'])): ?>
                    <div class="product-spec">
                        <span class="product-spec-label">AMX флаги</span>
                        <span class="product-spec-value"><code><?= esc($product['amx_flags']) ?></code></span>
                    </div>
                <?php endif ?>
                <?php if (!empty($product['amx_access'])): ?>
                    <div class="product-spec">
                        <span class="product-spec-label">Доступ</span>
                        <span class="product-spec-value"><code><?= esc($product['amx_access']) ?></code></span>
                    </div>
                <?php endif ?>
            </div>
        </div>

        <!-- Права частина — покупка -->
        <div class="product-buy-card">
            <div class="product-buy-glow"></div>

            <div class="product-buy-price">
                <span class="product-buy-amount"><?= (int) $product['price'] ?></span>
                <span class="product-buy-currency">₴</span>
            </div>

            <?php if ($duration > 0): ?>
                <p class="product-buy-period">
                    за <?= $duration ?> <?= $duration === 1 ? 'день' : ($duration < 5 ? 'дні' : 'днів') ?>
                </p>
            <?php else: ?>
                <p class="product-buy-period">одноразова послуга</p>
            <?php endif ?>

            <?php if (session()->get('user_id')): ?>
                <a href="/buy/<?= $product['id'] ?>" class="btn-buy">
                    <span>Купити зараз</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            <?php else: ?>
                <a href="/login" class="btn-buy">
                    <span>Увійти для покупки</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                </a>
            <?php endif ?>

            <div class="product-buy-info">
                <div class="product-buy-info-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    Миттєва активація
                </div>
                <div class="product-buy-info-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Безпечна оплата
                </div>
            </div>
        </div>
    </div>
</section>
