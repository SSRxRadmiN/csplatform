<?php
$name = product_name($product);
$desc = product_desc($product);
$duration = (int) $product['duration_days'];

$catSlug  = $product['cat_slug'] ?? 'other';
$catName  = cat_name($product) ?: ($product['cat_name_ua'] ?? lang('Shop.other'));
$catIcon  = $product['cat_icon'] ?? '📦';
$catColor = $product['cat_color'] ?? '#9ca3af';
?>

<section class="product-page">
    <!-- Навігація -->
    <div class="product-breadcrumb">
        <a href="/shop"><?= lang('Shop.back_to_shop') ?></a>
    </div>

    <div class="product-layout">
        <!-- Ліва частина — інфо -->
        <div class="product-info">
            <?php if (!empty($product['image_url'])): ?>
                <div class="product-image">
                    <img src="<?= esc($product['image_url']) ?>" alt="<?= esc($name) ?>">
                </div>
            <?php endif ?>

            <div class="product-badge product-badge--<?= esc($catSlug) ?>">
                <?= $catIcon ?> <?= esc($catName) ?>
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
                    <span class="product-spec-label"><?= lang('Shop.spec_server') ?></span>
                    <span class="product-spec-value"><?= esc($server['name'] ?? 'Реальні Кабани') ?></span>
                </div>
                <div class="product-spec">
                    <span class="product-spec-label"><?= lang('Shop.spec_duration') ?></span>
                    <span class="product-spec-value"><?= duration_text($duration) ?></span>
                </div>
                <?php if ($catSlug !== 'models'): ?>
                    <?php if (!empty($product['amx_flags'])): ?>
                        <div class="product-spec">
                            <span class="product-spec-label"><?= lang('Shop.spec_flags') ?></span>
                            <span class="product-spec-value"><code><?= esc($product['amx_flags']) ?></code></span>
                        </div>
                    <?php endif ?>
                    <?php if (!empty($product['amx_access'])): ?>
                        <div class="product-spec">
                            <span class="product-spec-label"><?= lang('Shop.spec_access') ?></span>
                            <span class="product-spec-value"><code><?= esc($product['amx_access']) ?></code></span>
                        </div>
                    <?php endif ?>
                <?php else: ?>
                    <div class="product-spec">
                        <span class="product-spec-label"><?= current_lang() === 'en' ? 'Type' : 'Тип' ?></span>
                        <span class="product-spec-value">🎭 <?= current_lang() === 'en' ? 'Player model' : 'Модель гравця' ?></span>
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
                <p class="product-buy-period"><?= lang('Shop.per') ?> <?= days_text($duration) ?></p>
            <?php else: ?>
                <p class="product-buy-period"><?= lang('Shop.one_time_service') ?></p>
            <?php endif ?>

            <?php if (session()->get('user_id')): ?>
                <a href="/buy/<?= $product['id'] ?>" class="btn-buy">
                    <span><?= lang('Shop.buy_now') ?></span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            <?php else: ?>
                <a href="/login" class="btn-buy">
                    <span><?= lang('Shop.login_to_buy') ?></span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                </a>
            <?php endif ?>

            <div class="product-buy-info">
                <div class="product-buy-info-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    <?= lang('Shop.instant') ?>
                </div>
                <div class="product-buy-info-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <?= lang('Shop.secure') ?>
                </div>
            </div>
        </div>
    </div>
</section>
