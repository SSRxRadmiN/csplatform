<!-- Hero Section -->
<section class="hero">
    <div class="hero-badge">
        <div class="hero-badge-dot"></div>
        <?php if (!empty($server) && $server['is_online']): ?>
            <?= lang('App.server_online') ?> — <?= $server['current_players'] ?>/<?= $server['max_players'] ?> <?= lang('App.server_players') ?>
        <?php else: ?>
            <?= lang('App.server_offline') ?>
        <?php endif; ?>
    </div>

    <h1>
        <?= lang('Home.hero_title') ?><br>
        <span class="gradient-text">Counter-Strike 1.6</span>
    </h1>

    <p class="hero-subtitle">
        <?= lang('Home.hero_subtitle') ?>
    </p>

    <div class="hero-actions">
        <a href="#shop-preview" class="btn-hero btn-hero-primary">
            <?= lang('Home.hero_btn_shop') ?>
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="https://t.me/csheadshot" target="_blank" rel="noopener" class="btn-hero btn-hero-secondary">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="margin-right:6px;">
                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
            </svg>
            <?= lang('Home.hero_btn_telegram') ?? 'Долучитись в Telegram' ?>
        </a>
    </div>

    <!-- ═══ НАШІ СЕРВЕРИ (всередині Hero) ═══ -->
    <?php if (!empty($servers)): ?>
    <div class="hero-servers fade-in">
        <div class="servers-table-wrap">
            <table class="servers-table">
                <thead>
                    <tr>
                        <th class="srv-col-name">Сервер</th>
                        <th class="srv-col-map">Мапа</th>
                        <th class="srv-col-players">Онлайн</th>
                        <th class="srv-col-ip">IP-адрес</th>
                        <th class="srv-col-actions">Дії</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servers as $srv): ?>
                        <?php
                            $isOnline = !empty($srv['is_online']);
                            $players  = (int) ($srv['current_players'] ?? 0);
                            $maxPlay  = (int) ($srv['max_players'] ?? 32);
                            $map      = $srv['current_map'] ?? '—';
                            $ipPort   = $srv['ip'] . ':' . $srv['port'];
                        ?>
                        <tr class="<?= $isOnline ? 'srv-row-online' : 'srv-row-offline' ?>">
                            <td class="srv-col-name">
                                <div class="srv-name-wrap">
                                    <span class="srv-status-dot <?= $isOnline ? 'srv-dot-online' : 'srv-dot-offline' ?>"></span>
                                    <span class="srv-name"><?= esc($srv['name']) ?></span>
                                    <?php if (!empty($srv['country'])): ?>
                                        <span class="srv-country"><?= esc($srv['country']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="srv-col-map">
                                <code class="srv-map"><?= esc($map) ?></code>
                            </td>
                            <td class="srv-col-players">
                                <?php if ($isOnline): ?>
                                    <button type="button"
                                            class="srv-players-btn"
                                            data-players-server="<?= (int) $srv['id'] ?>"
                                            data-server-name="<?= esc($srv['name']) ?>"
                                            title="Показати список гравців">
                                        <span class="srv-players"><?= $players ?> / <?= $maxPlay ?></span>
                                    </button>
                                <?php else: ?>
                                    <span class="srv-players-off">Офлайн</span>
                                <?php endif; ?>
                            </td>
                            <td class="srv-col-ip">
                                <button type="button" class="srv-ip-btn" data-copy="<?= esc($ipPort) ?>" title="Скопіювати">
                                    <code><?= esc($ipPort) ?></code>
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="9" y="9" width="13" height="13" rx="2"/>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                    </svg>
                                </button>
                            </td>
                            <td class="srv-col-actions">
                                <div class="srv-actions-wrap">
                                    <a href="steam://connect/<?= esc($ipPort) ?>"
                                       class="srv-icon-btn srv-icon-connect"
                                       title="Підключитись до сервера">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <polygon points="5 3 19 12 5 21 5 3"/>
                                        </svg>
                                    </a>
                                    <a href="/bans"
                                       class="srv-icon-btn srv-icon-bans"
                                       title="Список заблокованих">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                        </svg>
                                    </a>
                                    <a href="/stats"
                                       class="srv-icon-btn srv-icon-stats"
                                       title="Статистика гравців">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <line x1="18" y1="20" x2="18" y2="10"/>
                                            <line x1="12" y1="20" x2="12" y2="4"/>
                                            <line x1="6"  y1="20" x2="6"  y2="14"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</section>

<!-- ═══ ЛІЧИЛЬНИКИ ═══ -->
<section class="counters-section fade-in">
    <div class="counters-grid">
        <div class="counter-card">
            <div class="counter-info">
                <div class="counter-value" data-count="460">0</div>
                <div class="counter-suffix">+</div>
            </div>
            <div class="counter-label"><?= lang('Home.counter_players') ?></div>
            <div class="counter-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>
        <div class="counter-card">
            <div class="counter-info">
                <div class="counter-value" data-count="15">0</div>
                <div class="counter-suffix"></div>
            </div>
            <div class="counter-label"><?= lang('Home.counter_models') ?></div>
            <div class="counter-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
            </div>
        </div>
        <div class="counter-card">
            <div class="counter-info">
                <div class="counter-value">24/7</div>
            </div>
            <div class="counter-label"><?= lang('Home.counter_online') ?></div>
            <div class="counter-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
        </div>
        <div class="counter-card">
            <div class="counter-info">
                <div class="counter-value"><?= lang('Home.counter_instant_value') ?></div>
            </div>
            <div class="counter-label"><?= lang('Home.counter_instant') ?></div>
            <div class="counter-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            </div>
        </div>
    </div>
</section>

<!-- ═══ СЕКЦІЯ 1: ПРИВІЛЕЇ (VIP 30 як є + VIP 90 анімований border) ═══ -->
<?php if (!empty($vipProducts)): ?>
<section id="shop-preview">
    <div class="section-header fade-in">
        <div class="section-label">// <?= lang('Home.shop_label') ?></div>
        <h2 class="section-title"><?= lang('Home.shop_title') ?></h2>
        <p class="section-subtitle"><?= lang('Home.shop_subtitle') ?></p>
    </div>

    <div class="vip-grid fade-in">
        <?php foreach ($vipProducts as $i => $product): ?>
        <div class="product-card <?= $i === 0 ? 'featured' : '' ?> <?= $i > 0 ? 'vip-animated' : '' ?>">
            <div class="product-badge" style="background: <?= esc($product['cat_color'] ?? '#4ade80') ?>20; color: <?= esc($product['cat_color'] ?? '#4ade80') ?>;">
                <?= esc($product['cat_icon'] ?? '') ?> <?= esc(strtoupper(cat_name($product) ?: ($product['cat_name_ua'] ?? ''))) ?>
            </div>
            <div class="product-icon">
                <?= esc($product['cat_icon'] ?? '⭐') ?>
            </div>
            <div class="product-name"><?= esc(product_name($product)) ?></div>
            <p class="product-desc"><?= esc(product_desc($product)) ?></p>

            <div class="product-pricing">
                <span class="product-price"><?= number_format($product['price'], 0) ?></span>
                <span class="product-price-currency"><?= lang('App.currency') ?></span>
                <span class="product-price-period">
                    / <?= duration_text((int)$product['duration_days']) ?>
                </span>
            </div>

            <a href="/shop/<?= $product['id'] ?>" class="btn-buy">
                <?= lang('Home.buy_btn') ?> <?= esc(product_name($product)) ?>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ═══ CTA: ПОРІВНЯННЯ ПРИВІЛЕГІЙ ═══ -->
<section class="priv-cta-banner fade-in">
    <div class="priv-cta-banner__inner">
        <div class="priv-cta-banner__text">
            <div class="priv-cta-banner__icon">⚔️</div>
            <div>
                <div class="priv-cta-banner__title">Не знаєш що обрати?</div>
                <div class="priv-cta-banner__sub">Порівняй усі бонуси звичайного гравця, VIP та Мецената в одній таблиці</div>
            </div>
        </div>
        <a href="/privileges" class="priv-cta-banner__btn">
            Порівняти привілегії
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
    </div>
</section>
<?php if (!empty($modelProductsF) || !empty($modelProductsM)): ?>
<section id="models-section">
    <div class="section-header fade-in">
        <div class="section-label">// <?= lang('Home.models_label') ?></div>
        <h2 class="section-title"><?= lang('Home.models_title') ?></h2>
        <p class="section-subtitle"><?= lang('Home.models_subtitle') ?></p>
    </div>

    <?php if (!empty($modelProductsF)): ?>
    <div class="models-slider-section fade-in">
        <div class="models-slider-header">
            <span class="models-slider-icon" style="color:#ec4899;">♀</span>
            <span class="models-slider-title"><?= lang('Home.models_female') ?></span>
            <span class="models-slider-line"></span>
            <span class="models-slider-count"><?= count($modelProductsF) ?> <?= lang('Home.models_skins') ?></span>
        </div>
        <div class="models-slider-wrap">
            <div class="models-slider-track models-slider-track--left">
                <?php for ($loop = 0; $loop < 2; $loop++): ?>
                    <?php foreach ($modelProductsF as $p): ?>
                    <a href="/shop/<?= $p['id'] ?>" class="skin-card skin-card--female">
                        <div class="skin-card-overlay">
                            <span class="skin-card-btn">
                                <?= lang('Home.buy_skin') ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </span>
                        </div>
                        <div class="skin-card-img">
                            <?php if (!empty($p['image_url'])): ?>
                                <img src="<?= esc($p['image_url']) ?>" alt="<?= esc(product_name($p)) ?>">
                            <?php else: ?>
                                <span class="skin-card-emoji"><?= esc($p['cat_icon'] ?? '🎭') ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="skin-card-info">
                            <div class="skin-card-name"><?= esc(product_name($p)) ?></div>
                            <div class="skin-card-sub"><?= esc(mb_substr(product_desc($p), 0, 60)) ?></div>
                            <div class="skin-card-bottom">
                                <div class="skin-card-price"><?= number_format($p['price'], 0) ?> ₴ <span>/ <?= duration_text((int)$p['duration_days']) ?></span></div>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($modelProductsM)): ?>
    <div class="models-slider-section fade-in">
        <div class="models-slider-header">
            <span class="models-slider-icon" style="color:#60a5fa;">♂</span>
            <span class="models-slider-title"><?= lang('Home.models_male') ?></span>
            <span class="models-slider-line"></span>
            <span class="models-slider-count"><?= count($modelProductsM) ?> <?= lang('Home.models_skins') ?></span>
        </div>
        <div class="models-slider-wrap">
            <div class="models-slider-track models-slider-track--right">
                <?php for ($loop = 0; $loop < 2; $loop++): ?>
                    <?php foreach ($modelProductsM as $p): ?>
                    <a href="/shop/<?= $p['id'] ?>" class="skin-card">
                        <div class="skin-card-overlay">
                            <span class="skin-card-btn">
                                <?= lang('Home.buy_skin') ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </span>
                        </div>
                        <div class="skin-card-img">
                            <?php if (!empty($p['image_url'])): ?>
                                <img src="<?= esc($p['image_url']) ?>" alt="<?= esc(product_name($p)) ?>">
                            <?php else: ?>
                                <span class="skin-card-emoji"><?= esc($p['cat_icon'] ?? '🎭') ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="skin-card-info">
                            <div class="skin-card-name"><?= esc(product_name($p)) ?></div>
                            <div class="skin-card-sub"><?= esc(mb_substr(product_desc($p), 0, 60)) ?></div>
                            <div class="skin-card-bottom">
                                <div class="skin-card-price"><?= number_format($p['price'], 0) ?> ₴ <span>/ <?= duration_text((int)$p['duration_days']) ?></span></div>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<!-- ═══ СЕКЦІЯ 3: ДОДАТКОВІ ПОСЛУГИ ═══ -->
<?php if (!empty($serviceProducts)): ?>
<section id="services-section">
    <div class="section-header fade-in">
        <div class="section-label">// <?= lang('Home.services_label') ?></div>
        <h2 class="section-title"><?= lang('Home.services_title') ?></h2>
    </div>

    <div class="services-grid fade-in">
        <?php foreach ($serviceProducts as $p): ?>
        <?php
            $slug = $p['cat_slug'] ?? '';
            $nameKey = strtolower(str_replace(' ', '_', $p['name_en'] ?? ''));
            // Визначаємо стиль для спец-послуг
            $extraClass = '';
            if (stripos($p['name_ua'] ?? '', 'Буст') !== false || stripos($p['name_en'] ?? '', 'Boost') !== false) {
                $extraClass = 'service-card--xp';
            } elseif (stripos($p['name_ua'] ?? '', 'Комбо') !== false || stripos($p['name_ua'] ?? '', 'Моделька') !== false
                   || stripos($p['name_en'] ?? '', 'Combo') !== false || stripos($p['name_en'] ?? '', 'Model') !== false) {
                $extraClass = 'service-card--combo';
            }
        ?>
        <div class="service-card <?= $extraClass ?> fade-in">
            <div class="service-card-icon"><?= esc($p['cat_icon'] ?? '🎯') ?></div>
            <div class="service-card-name"><?= esc(product_name($p)) ?></div>
            <div class="service-card-desc"><?= esc(product_desc($p)) ?></div>
            <div class="service-card-price">
                <?= number_format($p['price'], 0) ?> ₴
                <span>/ <?= duration_text((int)$p['duration_days']) ?></span>
            </div>
            <a href="/shop/<?= $p['id'] ?>" class="service-card-btn">
                <?= lang('Home.buy_btn') ?> <?= esc(product_name($p)) ?>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- How It Works -->
<section>
    <div class="section-header fade-in">
        <div class="section-label">// <?= lang('Home.steps_label') ?></div>
        <h2 class="section-title"><?= lang('Home.steps_title') ?></h2>
    </div>

    <div class="steps-grid fade-in">
        <div class="step-card">
            <div class="step-number">01</div>
            <div class="step-icon">👤</div>
            <div class="step-title"><?= lang('Home.step1_title') ?></div>
            <div class="step-desc"><?= lang('Home.step1_desc') ?></div>
        </div>
        <div class="step-card">
            <div class="step-number">02</div>
            <div class="step-icon">🛒</div>
            <div class="step-title"><?= lang('Home.step2_title') ?></div>
            <div class="step-desc"><?= lang('Home.step2_desc') ?></div>
        </div>
        <div class="step-card">
            <div class="step-number">03</div>
            <div class="step-icon">💳</div>
            <div class="step-title"><?= lang('Home.step3_title') ?></div>
            <div class="step-desc"><?= lang('Home.step3_desc') ?></div>
        </div>
        <div class="step-card">
            <div class="step-number">04</div>
            <div class="step-icon">🎮</div>
            <div class="step-title"><?= lang('Home.step4_title') ?></div>
            <div class="step-desc"><?= lang('Home.step4_desc') ?></div>
        </div>
    </div>
</section>

<!-- ═══ МОДАЛКА: СПИСОК ГРАВЦІВ ═══ -->
<div id="players-modal" class="players-modal-overlay" hidden>
    <div class="players-modal" role="dialog" aria-labelledby="players-modal-title" aria-modal="true">
        <div class="players-modal-header">
            <div class="players-modal-title-wrap">
                <h3 id="players-modal-title" class="players-modal-title">Гравці на сервері</h3>
                <span id="players-modal-server" class="players-modal-subtitle"></span>
            </div>
            <button type="button" class="players-modal-close" aria-label="Закрити">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div id="players-modal-body" class="players-modal-body">
            <div class="players-modal-loader">Завантаження...</div>
        </div>
    </div>
</div>
