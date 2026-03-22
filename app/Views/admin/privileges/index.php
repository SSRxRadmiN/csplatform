<section class="admin-page">
    <div class="admin-header">
        <h1 class="admin-title">Привілеї сервера</h1>
        <div class="admin-nav">
            <a href="/admin" class="admin-nav-link">Dashboard</a>
            <a href="/admin/products" class="admin-nav-link">Товари</a>
            <a href="/admin/categories" class="admin-nav-link">Категорії</a>
            <a href="/admin/orders" class="admin-nav-link">Замовлення</a>
            <a href="/admin/users" class="admin-nav-link">Користувачі</a>
            <a href="/admin/privileges" class="admin-nav-link active">Привілеї</a>
            <a href="/admin/settings" class="admin-nav-link">Налаштування</a>
        </div>
    </div>

    <?php if (!empty($apiError)): ?>
        <div class="flash-message flash-error">API помилка: <?= esc($apiError) ?></div>
    <?php endif ?>

    <div class="admin-section">
        <div class="admin-section-header" style="flex-wrap:wrap; gap:1rem;">
            <h2 class="admin-section-title">
                amx_amxadmins
                <span style="font-size:0.8rem; color:#94a3b8; font-weight:400;">(<?= $total ?> записів)</span>
            </h2>
            <div style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap;">
                <!-- Пошук -->
                <form method="get" action="/admin/privileges" style="display:flex; gap:0.5rem;">
                    <input type="text" name="search" class="admin-input" style="width:220px; padding:0.45rem 0.75rem; font-size:0.85rem;"
                        placeholder="Нік, Steam ID, доступ..." value="<?= esc($search) ?>">
                    <button type="submit" class="btn-admin-primary" style="padding:0.45rem 1rem; font-size:0.85rem;">Шукати</button>
                    <?php if ($search): ?>
                        <a href="/admin/privileges" class="admin-btn-sm" style="padding:0.45rem 0.75rem;">✕</a>
                    <?php endif ?>
                </form>
                <!-- Додати -->
                <button onclick="document.getElementById('addModal').style.display='flex'" class="btn-admin-primary" style="padding:0.45rem 1rem; font-size:0.85rem;">+ Додати</button>
            </div>
        </div>

        <?php if (empty($privileges)): ?>
            <p class="admin-empty"><?= $search ? "За запитом «{$search}» нічого не знайдено" : 'Привілегій немає' ?></p>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Нік</th>
                            <th>Steam ID</th>
                            <th>Доступ</th>
                            <th>Флаги</th>
                            <th>Термін</th>
                            <th>Статус</th>
                            <th>Джерело</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($privileges as $p): ?>
                            <tr class="<?= $p['status'] === 'expired' ? 'row-inactive' : '' ?>" id="row-<?= $p['id'] ?>">
                                <td><?= $p['id'] ?></td>
                                <td title="<?= esc($p['nickname']) ?>"><?= esc(mb_strimwidth($p['nickname'], 0, 20, '…')) ?></td>
                                <td><code style="font-size:0.75rem;"><?= esc($p['steamid']) ?></code></td>
                                <td><code><?= esc($p['access']) ?></code></td>
                                <td><code><?= esc($p['flags']) ?></code></td>
                                <td style="font-size:0.8rem;">
                                    <?php if ($p['status'] === 'permanent'): ?>
                                        <span style="color:#facc15;">∞ Безстрок.</span>
                                    <?php elseif ($p['remaining_text']): ?>
                                        <?= esc($p['remaining_text']) ?> лишилось
                                    <?php else: ?>
                                        <?= $p['days'] ?> дн
                                    <?php endif ?>
                                    <div style="color:#64748b; font-size:0.7rem;"><?= esc($p['created_text']) ?></div>
                                </td>
                                <td>
                                    <?php if ($p['status'] === 'active'): ?>
                                        <span class="status-badge status-delivered">Активний</span>
                                    <?php elseif ($p['status'] === 'permanent'): ?>
                                        <span class="status-badge" style="background:rgba(250,204,21,0.12); color:#facc15;">Безстрок.</span>
                                    <?php else: ?>
                                        <span class="status-badge status-failed">Закінчився</span>
                                    <?php endif ?>
                                </td>
                                <td style="font-size:0.75rem;">
                                    <?php if ($p['source'] === 'shop'): ?>
                                        <span style="color:#4ade80;" title="<?= esc($p['username']) ?>">🛒 Магазин</span>
                                    <?php else: ?>
                                        <span style="color:#94a3b8;" title="<?= esc($p['username']) ?>">✋ Вручну</span>
                                    <?php endif ?>
                                </td>
                                <td class="admin-actions" style="white-space:nowrap;">
                                    <button class="admin-btn-sm" onclick="openEditModal(<?= htmlspecialchars(json_encode($p), ENT_QUOTES) ?>)">Ред.</button>
                                    <form method="post" action="/admin/privileges/delete/<?= $p['id'] ?>" style="display:inline" onsubmit="return confirm('Видалити привілегію #<?= $p['id'] ?> (<?= esc($p['nickname']) ?>)?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="admin-btn-sm admin-btn-danger">Вид.</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <!-- Пагінація -->
            <?php if ($pages > 1): ?>
                <?php
                    $baseParams = [];
                    if ($search) $baseParams['search'] = $search;
                    if ($perPage != 50) $baseParams['per_page'] = $perPage;
                ?>
                <div class="pagination" style="margin-top:1.5rem; display:flex; gap:0.5rem; justify-content:center; flex-wrap:wrap;">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <?php if ($i == $currentPage): ?>
                            <span class="pagination-current"><?= $i ?></span>
                        <?php elseif ($i <= 2 || $i >= $pages - 1 || abs($i - $currentPage) <= 2): ?>
                            <a href="/admin/privileges?<?= http_build_query(array_merge($baseParams, ['page' => $i])) ?>" class="pagination-link"><?= $i ?></a>
                        <?php elseif ($i == 3 || $i == $pages - 2): ?>
                            <span class="pagination-dots">…</span>
                        <?php endif ?>
                    <?php endfor ?>
                </div>
            <?php endif ?>
        <?php endif ?>
    </div>
</section>

<!-- Модалка: Додати привілегію -->
<div id="addModal" class="priv-modal" style="display:none;">
    <div class="priv-modal-content">
        <div class="priv-modal-header">
            <h3>Додати привілегію</h3>
            <button onclick="this.closest('.priv-modal').style.display='none'" class="priv-modal-close">&times;</button>
        </div>
        <form method="post" action="/admin/privileges/add">
            <?= csrf_field() ?>
            <div class="priv-modal-body">
                <div class="admin-form-group">
                    <label class="admin-label">Steam ID *</label>
                    <input type="text" name="steam_id" class="admin-input" required placeholder="STEAM_0:1:12345678">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Нік</label>
                    <input type="text" name="nickname" class="admin-input" placeholder="Player">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="admin-form-group">
                        <label class="admin-label">Доступ *</label>
                        <input type="text" name="access" class="admin-input" required placeholder="t" value="t">
                        <small class="admin-hint">t=VIP, abcdefghijklm=Admin</small>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Флаги</label>
                        <input type="text" name="flags" class="admin-input" placeholder="ce" value="ce">
                    </div>
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Термін (днів)</label>
                    <input type="number" name="days" class="admin-input" min="0" value="30">
                    <small class="admin-hint">0 = безстроково</small>
                </div>
            </div>
            <div class="priv-modal-footer">
                <button type="button" onclick="this.closest('.priv-modal').style.display='none'" class="admin-btn-sm">Скасувати</button>
                <button type="submit" class="btn-admin-primary" style="padding:0.5rem 1.5rem;">Додати</button>
            </div>
        </form>
    </div>
</div>

<!-- Модалка: Редагувати привілегію -->
<div id="editModal" class="priv-modal" style="display:none;">
    <div class="priv-modal-content">
        <div class="priv-modal-header">
            <h3>Редагувати привілегію <span id="editTitle"></span></h3>
            <button onclick="this.closest('.priv-modal').style.display='none'" class="priv-modal-close">&times;</button>
        </div>
        <form method="post" id="editForm">
            <?= csrf_field() ?>
            <div class="priv-modal-body">
                <div class="admin-form-group">
                    <label class="admin-label">Steam ID</label>
                    <input type="text" name="steamid" id="editSteamid" class="admin-input">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Нік</label>
                    <input type="text" name="nickname" id="editNickname" class="admin-input">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="admin-form-group">
                        <label class="admin-label">Доступ</label>
                        <input type="text" name="access" id="editAccess" class="admin-input">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Флаги</label>
                        <input type="text" name="flags" id="editFlags" class="admin-input">
                    </div>
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Термін (днів)</label>
                    <input type="number" name="days" id="editDays" class="admin-input" min="0">
                    <small class="admin-hint">0 = безстроково. Перераховується від дати створення.</small>
                </div>
            </div>
            <div class="priv-modal-footer">
                <button type="button" onclick="this.closest('.priv-modal').style.display='none'" class="admin-btn-sm">Скасувати</button>
                <button type="submit" class="btn-admin-primary" style="padding:0.5rem 1.5rem;">Зберегти</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(p) {
    document.getElementById('editTitle').textContent = '#' + p.id;
    document.getElementById('editForm').action = '/admin/privileges/update/' + p.id;
    document.getElementById('editSteamid').value = p.steamid;
    document.getElementById('editNickname').value = p.nickname;
    document.getElementById('editAccess').value = p.access;
    document.getElementById('editFlags').value = p.flags;
    document.getElementById('editDays').value = p.days;
    document.getElementById('editModal').style.display = 'flex';
}

// Закриття модалки по кліку на overlay
document.querySelectorAll('.priv-modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});

// Escape закриває модалки
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.priv-modal').forEach(m => m.style.display = 'none');
    }
});
</script>

<style>
.priv-modal {
    position: fixed;
    inset: 0;
    z-index: 9000;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(4px);
}
.priv-modal-content {
    background: #0a140a;
    border: 1px solid rgba(74,222,128,0.15);
    border-radius: 14px;
    width: 100%;
    max-width: 480px;
    max-height: 90vh;
    overflow-y: auto;
}
.priv-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(74,222,128,0.1);
}
.priv-modal-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #e2e8f0;
}
.priv-modal-close {
    background: none;
    border: none;
    color: #94a3b8;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0 0.25rem;
}
.priv-modal-close:hover { color: #ef4444; }
.priv-modal-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.priv-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid rgba(74,222,128,0.1);
}
</style>
