<?php
$groups = [
    'Загальні' => ['site_name', 'site_url', 'admin_email', 'default_language', 'maintenance_mode'],
    'CASSA оплата' => ['cassa_id', 'cassa_secret', 'cassa_url', 'cassa_merchant_id'],
];

$labels = [
    'site_name'          => 'Назва сайту',
    'site_url'           => 'URL сайту',
    'admin_email'        => 'Email адміна',
    'default_language'   => 'Мова за замовчуванням',
    'maintenance_mode'   => 'Режим обслуговування',
    'cassa_id'           => 'CASSA ID',
    'cassa_secret'       => 'Секретний ключ CASSA',
    'cassa_url'          => 'URL CASSA',
    'cassa_merchant_id'  => 'Merchant ID',
];

$passwordFields = ['cassa_secret', 'cassa_secret_key'];
?>

<section class="admin-page">
    <div class="admin-header">
        <h1 class="admin-title">Налаштування</h1>
        <div class="admin-nav">
            <a href="/admin" class="admin-nav-link">Dashboard</a>
            <a href="/admin/products" class="admin-nav-link">Товари</a>
            <a href="/admin/categories" class="admin-nav-link">Категорії</a>
            <a href="/admin/orders" class="admin-nav-link">Замовлення</a>
            <a href="/admin/users" class="admin-nav-link">Користувачі</a>
            <a href="/admin/settings" class="admin-nav-link active">Налаштування</a>
        </div>
    </div>

    <div class="admin-section">
        <form method="post" action="/admin/settings" class="admin-form">
            <?= csrf_field() ?>

            <?php foreach ($groups as $groupName => $keys): ?>
                <h3 class="admin-form-group-title"><?= $groupName ?></h3>
                <div class="admin-form-grid">
                    <?php foreach ($keys as $key): ?>
                        <div class="admin-form-group">
                            <label class="admin-label"><?= $labels[$key] ?? $key ?></label>
                            <?php if ($key === 'maintenance_mode'): ?>
                                <select name="<?= esc($key) ?>" class="admin-input">
                                    <option value="0" <?= ($settings[$key] ?? '0') === '0' ? 'selected' : '' ?>>Вимкнено</option>
                                    <option value="1" <?= ($settings[$key] ?? '0') === '1' ? 'selected' : '' ?>>Увімкнено</option>
                                </select>
                            <?php elseif ($key === 'default_language'): ?>
                                <select name="<?= esc($key) ?>" class="admin-input">
                                    <option value="ua" <?= ($settings[$key] ?? 'ua') === 'ua' ? 'selected' : '' ?>>Українська</option>
                                    <option value="en" <?= ($settings[$key] ?? 'ua') === 'en' ? 'selected' : '' ?>>English</option>
                                </select>
                            <?php elseif (in_array($key, $passwordFields)): ?>
                                <input type="password" name="<?= esc($key) ?>" class="admin-input"
                                    value="<?= esc($settings[$key] ?? '') ?>"
                                    autocomplete="off">
                            <?php else: ?>
                                <input type="text" name="<?= esc($key) ?>" class="admin-input"
                                    value="<?= esc($settings[$key] ?? '') ?>">
                            <?php endif ?>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endforeach ?>

            <button type="submit" class="btn-admin-primary" style="margin-top:1.5rem;">
                Зберегти налаштування
            </button>
        </form>
    </div>
</section>
