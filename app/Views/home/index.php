<!-- Hero Section -->
<section class="hero">
    <div class="hero-badge">
        <div class="hero-badge-dot"></div>
        <?php if (!empty($server) && $server['is_online']): ?>
            Сервер онлайн — <?= $server['current_players'] ?>/<?= $server['max_players'] ?> гравців
        <?php else: ?>
            Сервер оффлайн
        <?php endif; ?>
    </div>

    <h1>
        Привілеї для<br>
        <span class="gradient-text">Counter-Strike 1.6</span>
    </h1>

    <p class="hero-subtitle">
        VIP статус, адмін-права та інші привілеї для сервера Реальні Кабани. Миттєва активація після оплати.
    </p>

    <div class="hero-actions">
        <a href="/shop" class="btn-hero btn-hero-primary">
            Перейти до магазину
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="steam://connect/<?= esc($server['ip'] ?? '51.38.141.33') ?>:<?= esc($server['port'] ?? '27015') ?>" class="btn-hero btn-hero-secondary">
            Підключитись до сервера
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
            Карта: <span class="value"><?= esc($server['current_map'] ?? '—') ?></span>
        </div>
        <div class="status-divider"></div>
        <div class="status-item">
            Гравці: <span class="value"><?= $server['current_players'] ?? 0 ?> / <?= $server['max_players'] ?? 32 ?></span>
        </div>
    </div>
    <?php endif; ?>
</section>

<!-- Shop Preview -->
<?php if (!empty($products)): ?>
<section id="shop-preview">
    <div class="section-header fade-in">
        <div class="section-label">// Магазин привілегій</div>
        <h2 class="section-title">Обери свій рівень доступу</h2>
        <p class="section-subtitle">Всі привілеї активуються автоматично. Підтримуємо Visa, Mono, Privat24, крипту та GPay.</p>
    </div>

    <div class="products-grid">
        <?php foreach ($products as $i => $product): ?>
        <div class="product-card <?= $i === 0 ? 'featured' : '' ?> fade-in">
            <div class="product-badge badge-<?= esc($product['category']) ?>">
                <?= esc(strtoupper($product['category'])) ?>
            </div>
            <div class="product-icon">
                <?php
                $icons = ['vip' => '⭐', 'admin' => '🛡️', 'unban' => '🔓', 'other' => '🎯'];
                echo $icons[$product['category']] ?? '🎯';
                ?>
            </div>
            <div class="product-name"><?= esc($product['name_ua']) ?></div>
            <p class="product-desc"><?= esc($product['description_ua']) ?></p>

            <div class="product-pricing">
                <span class="product-price"><?= number_format($product['price'], 0) ?></span>
                <span class="product-price-currency">грн</span>
                <span class="product-price-period">
                    / <?= $product['duration_days'] > 0 ? $product['duration_days'] . ' днів' : 'одноразово' ?>
                </span>
            </div>

            <a href="/buy/<?= $product['id'] ?>" class="btn-buy">
                Придбати <?= esc($product['name_ua']) ?>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- How It Works -->
<section>
    <div class="section-header fade-in">
        <div class="section-label">// Як це працює</div>
        <h2 class="section-title">Чотири простих кроки</h2>
    </div>

    <div class="steps-grid fade-in">
        <div class="step-card">
            <div class="step-number">01</div>
            <div class="step-icon">👤</div>
            <div class="step-title">Реєстрація</div>
            <div class="step-desc">Створи акаунт та прив'яжи свій Steam ID</div>
        </div>
        <div class="step-card">
            <div class="step-number">02</div>
            <div class="step-icon">🛒</div>
            <div class="step-title">Вибір</div>
            <div class="step-desc">Обери привілегію та термін дії</div>
        </div>
        <div class="step-card">
            <div class="step-number">03</div>
            <div class="step-icon">💳</div>
            <div class="step-title">Оплата</div>
            <div class="step-desc">Оплати зручним способом через CASSA</div>
        </div>
        <div class="step-card">
            <div class="step-number">04</div>
            <div class="step-icon">🎮</div>
            <div class="step-title">Грай!</div>
            <div class="step-desc">Привілеї активні при зміні карти</div>
        </div>
    </div>
</section>
