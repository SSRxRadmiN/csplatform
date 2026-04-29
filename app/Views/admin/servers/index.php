<section class="admin-page">
    <?= view("admin/_nav", ["adminTitle" => "Сервери"]) ?>

    <div class="admin-section">
        <div class="admin-section-header">
            <span class="admin-section-title">Всі сервери (<?= count($servers) ?>)</span>
            <a href="/admin/servers/create" class="btn-admin-primary">+ Додати сервер</a>
        </div>

        <?php if (empty($servers)): ?>
            <div class="admin-empty">Серверів поки немає</div>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Назва</th>
                            <th>IP : Порт</th>
                            <th>Країна</th>
                            <th>API URL</th>
                            <th>Статус</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servers as $srv): ?>
                            <tr class="<?= !$srv['is_active'] ? 'row-inactive' : '' ?>">
                                <td><?= $srv['id'] ?></td>
                                <td><?= esc($srv['name']) ?></td>
                                <td><code><?= esc($srv['ip']) ?>:<?= esc($srv['port']) ?></code></td>
                                <td><?= esc($srv['country'] ?? '—') ?></td>
                                <td>
                                    <?php if (!empty($srv['api_url'])): ?>
                                        <code style="font-size:0.8rem;"><?= esc($srv['api_url']) ?></code>
                                    <?php else: ?>
                                        <span style="color:#9ca3af;">не задано</span>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <?php if ($srv['is_active']): ?>
                                        <span class="status-badge status-delivered">Активний</span>
                                    <?php else: ?>
                                        <span class="status-badge status-expired">Неактивний</span>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <div class="admin-actions">
                                        <a href="/admin/servers/edit/<?= $srv['id'] ?>" class="admin-btn-sm">Ред.</a>
                                        <?php if ($srv['is_active']): ?>
                                            <form method="post" action="/admin/servers/delete/<?= $srv['id'] ?>" style="display:inline;" onsubmit="return confirm('Деактивувати сервер?')">
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
