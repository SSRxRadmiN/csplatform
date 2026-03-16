<section class="admin-page">
    <div class="admin-header">
        <h1 class="admin-title">Користувачі</h1>
        <div class="admin-nav">
            <a href="/admin" class="admin-nav-link">Dashboard</a>
            <a href="/admin/products" class="admin-nav-link">Товари</a>
            <a href="/admin/categories" class="admin-nav-link">Категорії</a>
            <a href="/admin/orders" class="admin-nav-link">Замовлення</a>
            <a href="/admin/users" class="admin-nav-link active">Користувачі</a>
            <a href="/admin/settings" class="admin-nav-link">Налаштування</a>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Всі користувачі (<?= count($users) ?>)</h2>
        </div>

        <?php if (empty($users)): ?>
            <p class="admin-empty">Користувачів немає</p>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Нікнейм</th>
                            <th>Email</th>
                            <th>Steam ID</th>
                            <th>Роль</th>
                            <th>Статус</th>
                            <th>Останній вхід</th>
                            <th>Реєстрація</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><?= esc($u['username'] ?? '—') ?></td>
                                <td><?= esc($u['email']) ?></td>
                                <td><code><?= esc($u['steam_id'] ?? '—') ?></code></td>
                                <td>
                                    <span class="status-badge status-<?= $u['role'] === 'admin' ? 'delivered' : 'pending' ?>">
                                        <?= esc($u['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= ($u['is_active'] ?? 1) ? 'delivered' : 'failed' ?>">
                                        <?= ($u['is_active'] ?? 1) ? 'Активний' : 'Заблокований' ?>
                                    </span>
                                </td>
                                <td><?= !empty($u['last_login']) ? date('d.m.Y H:i', strtotime($u['last_login'])) : '—' ?></td>
                                <td><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
                                <td>
                                    <a href="/admin/users/edit/<?= $u['id'] ?>" class="admin-link">Ред.</a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
    </div>
</section>
