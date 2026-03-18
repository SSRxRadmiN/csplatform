<?php
$name = product_name($product);
$duration = (int) $product['duration_days'];
?>

<section class="account-page">
    <div class="account-page-header">
        <a href="/shop/<?= $product['id'] ?>" class="product-breadcrumb-link"><?= lang('Account.back_to_product') ?></a>
        <h1 class="account-page-title"><?= lang('Account.checkout_title') ?></h1>
    </div>

    <div class="buy-layout">
        <div class="buy-details">
            <div class="buy-product-card">
                <h3 class="buy-product-name"><?= esc($name) ?></h3>
                <div class="buy-product-meta">
                    <span><?= lang('Account.buy_server') ?>: <strong><?= esc($server['name'] ?? '—') ?></strong></span>
                    <?php if ($duration > 0): ?>
                        <span><?= lang('Account.buy_duration') ?>: <strong><?= days_text($duration) ?></strong></span>
                    <?php else: ?>
                        <span><?= lang('Account.buy_type') ?>: <strong><?= lang('Account.buy_one_time') ?></strong></span>
                    <?php endif ?>
                    <span>Steam ID: <code><?= esc($steamId) ?></code></span>
                </div>
                <div class="buy-product-price">
                    <span class="buy-price-value"><?= (int) $product['price'] ?></span>
                    <span class="buy-price-currency">₴</span>
                </div>
            </div>

            <form method="post" action="/buy/<?= $product['id'] ?>" class="buy-form">
                <?= csrf_field() ?>

                <h3 class="buy-section-title"><?= lang('Account.payment_method') ?></h3>

                <div class="payment-methods">
                    <label class="payment-method">
                        <input type="radio" name="ps" value="p2p" checked>
                        <div class="payment-method-card">
                            <span class="payment-method-name"><?= lang('Account.bank_card') ?></span>
                            <span class="payment-method-desc"><?= lang('Account.bank_card_desc') ?></span>
                        </div>
                    </label>
                </div>

                <button type="submit" class="btn-auth" style="margin-top: 1.5rem;">
                    <span><?= lang('Account.pay_btn') ?> <?= (int) $product['price'] ?> ₴</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</section>
