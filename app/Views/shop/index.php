<?php
$lang = session()->get('lang') ?? 'ua';

// Групуємо товари по категоріях
$categories = [
    'vip'   => ['label' => 'VIP статус', 'icon' => '⭐', 'items' => []],
    'admin' => ['label' => 'Адмін-права', 'icon' => '🛡️', 'items' => []],
    'unban' => ['label' => 'Розбан', 'icon' => '🔓', 'items' => []],
    'other' => ['label' => 'Інше', 'icon' => '📦', 'items' => []],
];

foreach ($products as $p) {
    $cat = $p['category'] ?? 'other';
    if (isset($categories[$cat])) {
        $categories[$cat]['items'][] = $p;
    } else {
        $categories['other']['items'][] = $p;
    }
}
?>

<section class="shop-hero">
    <h1 class="shop-title">Магазин привілегій</h1>
    <p class="shop-subtitle">
        Сервер: <strong><?= esc($server['name'] ?? 'Реальні Кабани') ?></strong>
        <span class="shop-ip"><?= esc(($server['ip'] ?? '') . ':' . ($server['port'] ?? '')) ?></span>
    </p>
</section>

<?php if (empty($products)): ?>
    <div class="shop-empty">
        <p>Товари поки відсутні. Заходьте пізніше!</p>
    </div>
<?php else: ?>
    <section class="shop-catalog">
        <?php foreach ($categories as $catKey => $cat): ?>
            <?php if (! empty($cat['items'])): ?>
                <div class="shop-category">
                    <h2 class="shop-category-title">
                        <span class="shop-category-icon"><?= $cat['icon'] ?></span>
                        <?= $cat['label'] ?>
                    </h2>
                    <div class="shop-grid">
                        <?php foreach ($cat['items'] as $product): ?>
                            <?php
                                $name = ($lang === 'en' && !empty($product['name_en'])) ? $product['name_en'] : $product['name_ua'];
                                $desc = ($lang === 'en' && !empty($product['description_en'])) ? $product['description_en'] : ($product['description_ua'] ?? '');
                                $duration = (int) $product['duration_days'];
                            ?>
                            <a href="/shop/<?= $product['id'] ?>" class="product-card">
                                <?php if (!empty($product['image_url'])): ?>
                                    <div class="product-card-image">
                                        <img src="<?= esc($product['image_url']) ?>" alt="<?= esc($name) ?>">
                                    </div>
                                <?php endif ?>

                                <div class="product-card-badge product-card-badge--<?= esc($catKey) ?>">
                                    <?= $cat['icon'] ?> <?= esc(mb_strtoupper($catKey)) ?>
                                </div>

                                <h3 class="product-card-name"><?= esc($name) ?></h3>

                                <?php if ($desc): ?>
                                    <p class="product-card-desc"><?= esc(mb_substr($desc, 0, 100)) ?><?= mb_strlen($desc) > 100 ? '…' : '' ?></p>
                                <?php endif ?>

                                <div class="product-card-meta">
                                    <?php if ($duration > 0): ?>
                                        <span class="product-card-duration">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            <?= $duration ?> <?= $duration === 1 ? 'день' : ($duration < 5 ? 'дні' : 'днів') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="product-card-duration">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            Разово
                                        </span>
                                    <?php endif ?>
                                </div>

                                <div class="product-card-footer">
                                    <span class="product-card-price"><?= (int) $product['price'] ?> ₴</span>
                                    <span class="product-card-btn">Детальніше →</span>
                                </div>
                            </a>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endif ?>
        <?php endforeach ?>
    </section>
<?php endif ?>
