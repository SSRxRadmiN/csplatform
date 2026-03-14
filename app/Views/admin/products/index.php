<section class="admin-page">
    <div class="admin-header">
        <h1 class="admin-title">Товари</h1>
        <div class="admin-nav">
            <a href="/admin" class="admin-nav-link">Dashboard</a>
            <a href="/admin/products" class="admin-nav-link active">Товари</a>
            <a href="/admin/orders" class="admin-nav-link">Замовлення</a>
            <a href="/admin/users" class="admin-nav-link">Користувачі</a>
            <a href="/admin/settings" class="admin-nav-link">Налаштування</a>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Всі товари</h2>
            <a href="/admin/products/create" class="btn-admin-primary">+ Додати товар</a>
        </div>

        <?php if (empty($products)): ?>
            <p class="admin-empty">Товарів немає</p>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Назва</th>
                            <th>Сервер</th>
                            <th>Ціна</th>
                            <th>Днів</th>
                            <th>Порядок</th>
                            <th>Статус</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr class="<?= $p['is_active'] ? '' : 'row-inactive' ?>">
                                <td><?= $p['id'] ?></td>
                                <td><?= esc($p['name_ua']) ?></td>
                                <td><?= esc($p['server_name'] ?? '—') ?></td>
                                <td><?= (int) $p['price'] ?> ₴</td>
                                <td><?= (int) $p['duration_days'] ?: '∞' ?></td>
                                <td><?= (int) $p['sort_order'] ?></td>
                                <td>
                                    <span class="status-badge status-<?= $p['is_active'] ? 'delivered' : 'failed' ?>">
                                        <?= $p['is_active'] ? 'Активний' : 'Вимкнено' ?>
                                    </span>
                                </td>
                                <td class="admin-actions">
                                    <a href="/admin/products/edit/<?= $p['id'] ?>" class="admin-btn-sm">Ред.</a>
                                    <form method="post" action="/admin/products/delete/<?= $p['id'] ?>" style="display:inline" onsubmit="return confirm('Деактивувати товар?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="admin-btn-sm admin-btn-danger">Вимкнути</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
    </div>
</section>
