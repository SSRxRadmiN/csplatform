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
        <a href="steam://connect/<?= esc($server['ip'] ?? '31.42.190.78') ?>:<?= esc($server['port'] ?? '27015') ?>" class="btn-hero btn-hero-secondary">
            <?= lang('Home.hero_btn_connect') ?>
        </a>
    </div>

    <?php if (!empty($server)): ?>
    <div class="server-status">
        <div class="status-item">
            <span class="value <?= $server['is_online'] ? 'status-online' : 'status-offline' ?>">
                ● <?= $server['is_online'] ? 'ONLINE' : 'OFFLINE' ?>
            </span>
        </div>
        <div class="status-divider"></div>
        <div class="status-item">
            IP: <span class="value"><?= esc($server['ip']) ?>:<?= esc($server['port']) ?></span>
        </div>
        <div class="status-divider"></div>
        <div class="status-item">
            <?= lang('App.server_map') ?>: <span class="value"><?= esc($server['current_map'] ?? '—') ?></span>
        </div>
        <div class="status-divider"></div>
        <div class="status-item">
            <?= lang('App.server_players') ?>: <span class="value"><?= $server['current_players'] ?? 0 ?> / <?= $server['max_players'] ?? 32 ?></span>
        </div>
    </div>
    <?php endif; ?>
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

<!-- ═══ СЕКЦІЯ 2: МОДЕЛІ ГРАВЦІВ — автоскрол слайдери ═══ -->
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
