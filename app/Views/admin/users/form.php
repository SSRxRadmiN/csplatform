<section class="admin-page">
    <div class="admin-header">
        <h1 class="admin-title">Редагувати користувача</h1>
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
            <a href="/admin/users" class="admin-link">← Назад до користувачів</a>
        </div>

        <!-- Інфо блок -->
        <div class="admin-info-block" style="margin-bottom: 1.5rem; padding: 1rem; border: 1px solid rgba(74, 222, 128, 0.15); border-radius: 8px; background: rgba(74, 222, 128, 0.03);">
            <div style="display: flex; gap: 2rem; flex-wrap: wrap; font-size: 0.85rem; color: rgba(240, 253, 244, 0.6);">
                <span>ID: <strong style="color: #f0fdf4;"><?= $editUser['id'] ?></strong></span>
                <span>Реєстрація: <strong style="color: #f0fdf4;"><?= date('d.m.Y H:i', strtotime($editUser['created_at'])) ?></strong></span>
                <span>Останній вхід: <strong style="color: #f0fdf4;"><?= !empty($editUser['last_login']) ? date('d.m.Y H:i', strtotime($editUser['last_login'])) : '—' ?></strong></span>
            </div>
        </div>

        <form method="post" action="/admin/users/edit/<?= $editUser['id'] ?>" class="admin-form">
            <?= csrf_field() ?>

            <div class="admin-form-grid">
                <div class="admin-form-group">
                    <label class="admin-label">Нікнейм</label>
                    <input type="text" name="username" class="admin-input"
                        value="<?= esc(old('username', $editUser['username'] ?? '')) ?>"
                        placeholder="Нік гравця">
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Email *</label>
                    <input type="email" name="email" class="admin-input"
                        value="<?= esc(old('email', $editUser['email'])) ?>" required>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Steam ID</label>
                    <input type="text" name="steam_id" class="admin-input"
                        value="<?= esc(old('steam_id', $editUser['steam_id'] ?? '')) ?>"
                        placeholder="STEAM_0:1:xxxxx">
                    <small class="admin-hint">Формат: STEAM_X:Y:ZZZZZ</small>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Роль</label>
                    <?php
                        $roles = [
                            'player'    => '🎮 Гравець',
                            'moderator' => '🛡️ Модератор',
                            'owner'     => '👑 Власник сервера',
                            'admin'     => '⚡ Адміністратор',
                        ];
                        $currentRole = old('role', $editUser['role']);
                        $isSelf = ((int)$editUser['id'] === (int)session()->get('user_id'));
                    ?>
                    <select name="role" class="admin-input" <?= $isSelf ? 'disabled' : '' ?>>
                        <?php foreach ($roles as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $currentRole === $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <?php if ($isSelf): ?>
                        <input type="hidden" name="role" value="<?= esc($currentRole) ?>">
                        <small class="admin-hint">Не можна змінити роль самому собі</small>
                    <?php endif ?>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Статус</label>
                    <?php $isSelf = ((int)$editUser['id'] === (int)session()->get('user_id')); ?>
                    <select name="is_active" class="admin-input" <?= $isSelf ? 'disabled' : '' ?>>
                        <option value="1" <?= ($editUser['is_active'] ?? 1) ? 'selected' : '' ?>>✅ Активний</option>
                        <option value="0" <?= !($editUser['is_active'] ?? 1) ? 'selected' : '' ?>>🚫 Заблокований</option>
                    </select>
                    <?php if ($isSelf): ?>
                        <input type="hidden" name="is_active" value="1">
                        <small class="admin-hint">Не можна заблокувати самого себе</small>
                    <?php endif ?>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Новий пароль</label>
                    <input type="password" name="new_password" class="admin-input"
                        placeholder="Залиште порожнім щоб не змінювати" autocomplete="new-password">
                    <small class="admin-hint">Мінімум 6 символів. Залиште порожнім якщо не потрібно змінювати.</small>
                </div>
            </div>

            <button type="submit" class="btn-admin-primary" style="margin-top: 1.5rem;">
                Зберегти зміни
            </button>
        </form>
    </div>
</section>
