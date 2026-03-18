<?php
// Групуємо товари по категоріях з БД
$grouped = [];
foreach ($products as $p) {
    $catId = $p['category_id'] ?? 0;
    if (! isset($grouped[$catId])) {
        $grouped[$catId] = [];
    }
    $grouped[$catId][] = $p;
}

// Індексуємо категорії по id
$catMap = [];
foreach ($categories as $c) {
    $catMap[$c['id']] = $c;
}
?>

<section class="shop-hero">
    <h1 class="shop-title"><?= lang('Shop.title') ?></h1>
    <p class="shop-subtitle">
        <?= lang('Shop.server') ?>: <strong><?= esc($server['name'] ?? 'Реальні Кабани') ?></strong>
        <span class="shop-ip"><?= esc(($server['ip'] ?? '') . ':' . ($server['port'] ?? '')) ?></span>
    </p>
</section>

<?php if (empty($products)): ?>
    <div class="shop-empty">
        <p><?= lang('Shop.empty') ?></p>
    </div>
<?php else: ?>
    <section class="shop-catalog">
        <?php foreach ($categories as $cat): ?>
            <?php if (! empty($grouped[$cat['id']])): ?>
                <?php
                    $catName = cat_name($cat);
                    $catSlug = $cat['slug'] ?? 'other';
                    $catIcon = $cat['icon'] ?? '📦';
                ?>
                <div class="shop-category">
                    <h2 class="shop-category-title">
                        <span class="shop-category-icon"><?= $catIcon ?></span>
                        <?= esc($catName) ?>
                    </h2>
                    <div class="shop-grid">
                        <?php foreach ($grouped[$cat['id']] as $product): ?>
                            <?php
                                $name = product_name($product);
                                $desc = product_desc($product);
                                $duration = (int) $product['duration_days'];
                            ?>
                            <a href="/shop/<?= $product['id'] ?>" class="product-card">
                                <?php if (!empty($product['image_url'])): ?>
                                    <div class="product-card-image">
                                        <img src="<?= esc($product['image_url']) ?>" alt="<?= esc($name) ?>">
                                    </div>
                                <?php endif ?>

                                <div class="product-card-badge product-card-badge--<?= esc($catSlug) ?>">
                                    <?= $catIcon ?> <?= esc(mb_strtoupper($catSlug)) ?>
                                </div>

                                <h3 class="product-card-name"><?= esc($name) ?></h3>

                                <?php if ($desc): ?>
                                    <p class="product-card-desc"><?= esc(mb_substr($desc, 0, 100)) ?><?= mb_strlen($desc) > 100 ? '…' : '' ?></p>
                                <?php endif ?>

                                <div class="product-card-meta">
                                    <?php if ($duration > 0): ?>
                                        <span class="product-card-duration">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            <?= days_text($duration) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="product-card-duration">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            <?= lang('Shop.once') ?>
                                        </span>
                                    <?php endif ?>
                                </div>

                                <div class="product-card-footer">
                                    <span class="product-card-price"><?= (int) $product['price'] ?> ₴</span>
                                    <span class="product-card-btn"><?= lang('Shop.details') ?></span>
                                </div>
                            </a>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endif ?>
        <?php endforeach ?>

        <?php // Товари без категорії ?>
        <?php if (! empty($grouped[0])): ?>
            <div class="shop-category">
                <h2 class="shop-category-title">
                    <span class="shop-category-icon">📦</span>
                    <?= lang('Shop.other') ?>
                </h2>
                <div class="shop-grid">
                    <?php foreach ($grouped[0] as $product): ?>
                        <?php $name = product_name($product); ?>
                        <a href="/shop/<?= $product['id'] ?>" class="product-card">
                            <div class="product-card-badge product-card-badge--other">📦 OTHER</div>
                            <h3 class="product-card-name"><?= esc($name) ?></h3>
                            <div class="product-card-footer">
                                <span class="product-card-price"><?= (int) $product['price'] ?> ₴</span>
                                <span class="product-card-btn"><?= lang('Shop.details') ?></span>
                            </div>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>
    </section>
<?php endif ?>
