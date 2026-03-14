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
            <a href="/admin/orders" class="admin-nav-link active">Замовлення</a>
            <a href="/admin/users" class="admin-nav-link">Користувачі</a>
            <a href="/admin/settings" class="admin-nav-link">Налаштування</a>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <a href="/admin/orders" class="admin-link">← Назад до замовлень</a>
            <span class="status-badge status-<?= esc($order['status']) ?>">
                <?= $statusLabels[$order['status']] ?? $order['status'] ?>
            </span>
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
                <?php if (!empty($order['expires_at'])): ?>
                    <div class="admin-detail-row">
                        <span class="admin-detail-label">Діє до</span>
                        <span><?= date('d.m.Y', strtotime($order['expires_at'])) ?></span>
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
                    <code><?= esc($order['steam_id'] ?? '—') ?></code>
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
</section>
