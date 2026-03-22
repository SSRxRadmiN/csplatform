<?php
$statuses = ['pending', 'paid', 'delivered', 'failed', 'expired'];
?>

<section class="admin-page">
    <?= view("admin/_nav", ["adminTitle" => "Замовлення"]) ?>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">
                <?= $currentStatus ? 'Фільтр: ' . $currentStatus : 'Всі замовлення' ?>
            </h2>
            <div class="admin-filters">
                <a href="/admin/orders" class="admin-filter-btn <?= !$currentStatus ? 'active' : '' ?>">Всі</a>
                <?php foreach ($statuses as $s): ?>
                    <a href="/admin/orders?status=<?= $s ?>" class="admin-filter-btn <?= $currentStatus === $s ? 'active' : '' ?>"><?= $s ?></a>
                <?php endforeach ?>
            </div>
        </div>

        <?php if (empty($orders)): ?>
            <p class="admin-empty">Замовлень не знайдено</p>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Користувач</th>
                            <th>Товар</th>
                            <th>Сума</th>
                            <th>Steam ID</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th>Діє до</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><a href="/admin/orders/<?= $order['id'] ?>">#<?= $order['id'] ?></a></td>
                                <td><?= esc($order['username'] ?? $order['email'] ?? '—') ?></td>
                                <td><?= esc($order['product_name'] ?? '—') ?></td>
                                <td><?= (int) $order['amount'] ?> ₴</td>
                                <td><code><?= esc($order['steam_id'] ?? '—') ?></code></td>
                                <td><span class="status-badge status-<?= esc($order['status']) ?>"><?= esc($order['status']) ?></span></td>
                                <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                                <td><?= $order['expires_at'] ? date('d.m.Y', strtotime($order['expires_at'])) : '—' ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
    </div>
</section>
