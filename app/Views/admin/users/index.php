<section class="admin-page">
    <?= view("admin/_nav", ["adminTitle" => "Користувачі"]) ?>

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
