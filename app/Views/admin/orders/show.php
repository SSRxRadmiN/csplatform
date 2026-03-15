<?php
$statusLabels = [
    'pending'   => 'Очікує оплати',
    'paid'      => 'Оплачено',
    'delivered' => 'Доставлено',
    'failed'    => 'Помилка',
    'expired'   => 'Прострочено',
];
?>

<section class="admin-page">
    <div class="admin-header">
        <h1 class="admin-title">Замовлення #<?= $order['id'] ?></h1>
        <div class="admin-nav">
            <a href="/admin" class="admin-nav-link">Dashboard</a>
            <a href="/admin/products" class="admin-nav-link">Товари</a>
            <a href="/admin/categories" class="admin-nav-link">Категорії</a>
            <a href="/admin/orders" class="admin-nav-link active">Замовлення</a>
            <a href="/admin/users" class="admin-nav-link">Користувачі</a>
            <a href="/admin/settings" class="admin-nav-link">Налаштування</a>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <a href="/admin/orders" class="admin-link">← Назад до замовлень</a>
            <div class="admin-order-status-row">
                <span class="status-badge status-<?= esc($order['status']) ?>">
                    <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                </span>
                <!-- Форма зміни статусу -->
                <form method="post" action="/admin/orders/<?= $order['id'] ?>/status" class="admin-inline-form">
                    <?= csrf_field() ?>
                    <select name="status" class="admin-input admin-input-sm">
                        <?php foreach ($statusLabels as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $order['status'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach ?>
                    </select>
                    <button type="submit" class="admin-btn-sm">Змінити</button>
                </form>
            </div>
        </div>

        <div class="admin-detail-grid">
            <div class="admin-detail-card">
                <h3 class="admin-detail-title">Замовлення</h3>
                <div class="admin-detail-row">
                    <span class="admin-detail-label">ID</span>
                    <span>#<?= $order['id'] ?></span>
                </div>
                <div class="admin-detail-row">
                    <span class="admin-detail-label">Товар</span>
                    <span><?= esc($order['product_name'] ?? '—') ?></span>
                </div>
                <div class="admin-detail-row">
                    <span class="admin-detail-label">Сума</span>
                    <span class="admin-detail-highlight"><?= (int) $order['amount'] ?> ₴</span>
                </div>
                <div class="admin-detail-row">
                    <span class="admin-detail-label">Payment ID</span>
                    <code><?= esc($order['payment_id'] ?? '—') ?></code>
                </div>
                <div class="admin-detail-row">
                    <span class="admin-detail-label">Створено</span>
                    <span><?= date('d.m.Y H:i:s', strtotime($order['created_at'])) ?></span>
                </div>
                <?php if (!empty($order['paid_at'])): ?>
                    <div class="admin-detail-row">
                        <span class="admin-detail-label">Оплачено</span>
                        <span><?= date('d.m.Y H:i:s', strtotime($order['paid_at'])) ?></span>
                    </div>
                <?php endif ?>
                <?php if (!empty($order['delivered_at'])): ?>
                    <div class="admin-detail-row">
                        <span class="admin-detail-label">Доставлено</span>
                        <span><?= date('d.m.Y H:i:s', strtotime($order['delivered_at'])) ?></span>
                    </div>
                <?php endif ?>
                <?php if (!empty($order['expires_at'])): ?>
                    <div class="admin-detail-row">
                        <span class="admin-detail-label">Діє до</span>
                        <span><?= date('d.m.Y H:i:s', strtotime($order['expires_at'])) ?></span>
                    </div>
                <?php endif ?>
            </div>

            <div class="admin-detail-card">
                <h3 class="admin-detail-title">Користувач</h3>
                <div class="admin-detail-row">
                    <span class="admin-detail-label">Нікнейм</span>
                    <span><?= esc($order['username'] ?? '—') ?></span>
                </div>
                <div class="admin-detail-row">
                    <span class="admin-detail-label">Email</span>
                    <span><?= esc($order['email'] ?? '—') ?></span>
                </div>
                <div class="admin-detail-row">
                    <span class="admin-detail-label">Steam ID</span>
                    <code><?= esc($order['user_steam'] ?? $order['steam_id'] ?? '—') ?></code>
                </div>
            </div>

            <div class="admin-detail-card">
                <h3 class="admin-detail-title">Привілеї</h3>
                <div class="admin-detail-row">
                    <span class="admin-detail-label">AMX Access</span>
                    <code><?= esc($order['amx_access'] ?? '—') ?></code>
                </div>
                <div class="admin-detail-row">
                    <span class="admin-detail-label">AMX Flags</span>
                    <code><?= esc($order['amx_flags'] ?? '—') ?></code>
                </div>
                <div class="admin-detail-row">
                    <span class="admin-detail-label">Тривалість</span>
                    <span><?= (int) ($order['duration_days'] ?? 0) ?> днів</span>
                </div>
                <?php if (!empty($order['delivery_log'])): ?>
                    <div class="admin-detail-row">
                        <span class="admin-detail-label">Лог доставки</span>
                        <pre class="admin-log"><?= esc($order['delivery_log']) ?></pre>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <!-- Форма ручного редагування -->
    <div class="admin-section">
        <h3 class="admin-section-title">Редагування замовлення</h3>
        <form method="post" action="/admin/orders/<?= $order['id'] ?>/update" class="admin-form">
            <?= csrf_field() ?>

            <div class="admin-form-grid">
                <div class="admin-form-group">
                    <label class="admin-label">Steam ID</label>
                    <input type="text" name="steam_id" class="admin-input"
                        value="<?= esc($order['steam_id'] ?? '') ?>"
                        placeholder="STEAM_0:1:...">
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Діє до (expires_at)</label>
                    <input type="datetime-local" name="expires_at" class="admin-input"
                        value="<?= !empty($order['expires_at']) ? date('Y-m-d\TH:i', strtotime($order['expires_at'])) : '' ?>">
                    <small class="admin-hint">Залиште порожнім для безстрокових</small>
                </div>

                <div class="admin-form-group admin-form-full">
                    <label class="admin-label">Лог доставки</label>
                    <textarea name="delivery_log" class="admin-textarea" rows="4"><?= esc($order['delivery_log'] ?? '') ?></textarea>
                </div>
            </div>

            <button type="submit" class="btn-admin-primary" style="margin-top:1rem;">
                Зберегти зміни
            </button>
        </form>
    </div>
</section>
