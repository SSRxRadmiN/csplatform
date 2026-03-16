<?php
$lang = session()->get('lang') ?? 'ua';
$name = ($lang === 'en' && !empty($product['name_en'])) ? $product['name_en'] : $product['name_ua'];
$duration = (int) $product['duration_days'];
?>

<section class="account-page">
    <div class="account-page-header">
        <a href="/shop/<?= $product['id'] ?>" class="product-breadcrumb-link">← Назад до товару</a>
        <h1 class="account-page-title">Оформлення замовлення</h1>
    </div>

    <div class="buy-layout">
        <!-- Ліва частина — деталі -->
        <div class="buy-details">
            <div class="buy-product-card">
                <h3 class="buy-product-name"><?= esc($name) ?></h3>
                <div class="buy-product-meta">
                    <span>Сервер: <strong><?= esc($server['name'] ?? '—') ?></strong></span>
                    <?php if ($duration > 0): ?>
                        <span>Термін: <strong><?= $duration ?> <?= $duration === 1 ? 'день' : ($duration < 5 ? 'дні' : 'днів') ?></strong></span>
                    <?php else: ?>
                        <span>Тип: <strong>Одноразово</strong></span>
                    <?php endif ?>
                    <span>Steam ID: <code><?= esc($steamId) ?></code></span>
                </div>
                <div class="buy-product-price">
                    <span class="buy-price-value"><?= (int) $product['price'] ?></span>
                    <span class="buy-price-currency">₴</span>
                </div>
            </div>

            <!-- Вибір способу оплати -->
            <form method="post" action="/buy/<?= $product['id'] ?>" class="buy-form">
                <?= csrf_field() ?>

                <h3 class="buy-section-title">Спосіб оплати</h3>

                <div class="payment-methods">
                    <label class="payment-method">
                        <input type="radio" name="ps" value="p2p" checked>
                        <div class="payment-method-card">
                            <span class="payment-method-name">Банківська картка</span>
                            <span class="payment-method-desc">Приватбанк / Монобанк</span>
                        </div>
                    </label>
                </div>

                <button type="submit" class="btn-auth" style="margin-top: 1.5rem;">
                    <span>Оплатити <?= (int) $product['price'] ?> ₴</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</section>
