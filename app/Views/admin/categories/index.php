<section class="admin-page">
    <div class="admin-header">
        <h1 class="admin-title">Категорії</h1>
        <div class="admin-nav">
            <a href="/admin" class="admin-nav-link">Dashboard</a>
            <a href="/admin/products" class="admin-nav-link">Товари</a>
            <a href="/admin/categories" class="admin-nav-link active">Категорії</a>
            <a href="/admin/orders" class="admin-nav-link">Замовлення</a>
            <a href="/admin/users" class="admin-nav-link">Користувачі</a>
            <a href="/admin/settings" class="admin-nav-link">Налаштування</a>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <span class="admin-section-title">Всі категорії (<?= count($categories) ?>)</span>
            <a href="/admin/categories/create" class="btn-admin-primary">+ Додати категорію</a>
        </div>

        <?php if (empty($categories)): ?>
            <div class="admin-empty">Категорій поки немає</div>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Іконка</th>
                            <th>Slug</th>
                            <th>Назва (UA)</th>
                            <th>Назва (EN)</th>
                            <th>Колір</th>
                            <th>Порядок</th>
                            <th>Статус</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr class="<?= !$cat['is_active'] ? 'row-inactive' : '' ?>">
                                <td><?= $cat['id'] ?></td>
                                <td style="font-size:1.25rem;"><?= esc($cat['icon'] ?? '') ?></td>
                                <td><code><?= esc($cat['slug']) ?></code></td>
                                <td><?= esc($cat['name_ua']) ?></td>
                                <td><?= esc($cat['name_en'] ?? '—') ?></td>
                                <td>
                                    <span style="display:inline-flex;align-items:center;gap:0.4rem;">
                                        <span style="width:14px;height:14px;border-radius:50%;background:<?= esc($cat['color'] ?? '#9ca3af') ?>;display:inline-block;"></span>
                                        <code><?= esc($cat['color'] ?? '') ?></code>
                                    </span>
                                </td>
                                <td><?= (int) $cat['sort_order'] ?></td>
                                <td>
                                    <?php if ($cat['is_active']): ?>
                                        <span class="status-badge status-delivered">Активна</span>
                                    <?php else: ?>
                                        <span class="status-badge status-expired">Неактивна</span>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <div class="admin-actions">
                                        <a href="/admin/categories/edit/<?= $cat['id'] ?>" class="admin-btn-sm">Ред.</a>
                                        <?php if ($cat['is_active']): ?>
                                            <form method="post" action="/admin/categories/delete/<?= $cat['id'] ?>" style="display:inline;" onsubmit="return confirm('Деактивувати категорію?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="admin-btn-sm admin-btn-danger">Вимкнути</button>
                                            </form>
                                        <?php endif ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
    </div>
</section>
