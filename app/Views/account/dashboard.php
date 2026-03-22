<section class="admin-page">
    <?= view("admin/_nav", ["adminTitle" => "Адмін-панель"]) ?>

    <!-- Stats Cards -->
    <div class="admin-stats">
        <div class="admin-stat-card">
            <div class="admin-stat-value"><?= (int) $totalOrders ?></div>
            <div class="admin-stat-label">Замовлень</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-value"><?= number_format((float) $totalRevenue, 0, '.', ' ') ?> ₴</div>
            <div class="admin-stat-label">Дохід</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-value"><?= (int) $totalUsers ?></div>
            <div class="admin-stat-label">Користувачів</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-value"><?= (int) $totalProducts ?></div>
            <div class="admin-stat-label">Товарів</div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Останні замовлення</h2>
            <a href="/admin/orders" class="admin-link">Всі замовлення →</a>
        </div>

        <?php if (empty($recentOrders)): ?>
            <p class="admin-empty">Замовлень поки немає</p>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Користувач</th>
                            <th>Товар</th>
                            <th>Сума</th>
                            <th>Статус</th>
                            <th>Дата</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><a href="/admin/orders/<?= $order['id'] ?>">#<?= $order['id'] ?></a></td>
                                <td><?= esc($order['username'] ?? $order['email'] ?? '—') ?></td>
                                <td><?= esc($order['product_name'] ?? '—') ?></td>
                                <td><?= (int) $order['amount'] ?> ₴</td>
                                <td><span class="status-badge status-<?= esc($order['status']) ?>"><?= esc($order['status']) ?></span></td>
                                <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
    </div>
</section>
