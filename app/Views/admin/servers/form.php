<?php
$isEdit = !empty($server);
$action = $isEdit ? '/admin/servers/edit/' . $server['id'] : '/admin/servers/create';
?>

<section class="admin-page">
    <?= view("admin/_nav", ["adminTitle" => $isEdit ? "Редагувати сервер" : "Новий сервер"]) ?>

    <div class="admin-section">
        <div class="admin-section-header">
            <a href="/admin/servers" class="admin-link">← Назад до серверів</a>
        </div>

        <form method="post" action="<?= $action ?>" class="admin-form">
            <?= csrf_field() ?>

            <h3 class="admin-form-group-title">Основне</h3>
            <div class="admin-form-grid">
                <div class="admin-form-group">
                    <label class="admin-label">Назва *</label>
                    <input type="text" name="name" class="admin-input"
                        value="<?= esc(old('name', $server['name'] ?? '')) ?>"
                        placeholder="РЕАЛЬНІ КАБАНИ | PUBLIC [UA/EU]" required>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Країна</label>
                    <input type="text" name="country" class="admin-input"
                        value="<?= esc(old('country', $server['country'] ?? '')) ?>"
                        placeholder="UA" maxlength="8">
                    <small class="admin-hint">Код країни (UA, EU, US...)</small>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">IP / Хост *</label>
                    <input type="text" name="ip" class="admin-input"
                        value="<?= esc(old('ip', $server['ip'] ?? '')) ?>"
                        placeholder="185.252.24.118" required>
                    <small class="admin-hint">Видно гравцям на головній сторінці</small>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Порт *</label>
                    <input type="number" name="port" class="admin-input"
                        value="<?= esc(old('port', $server['port'] ?? '27015')) ?>"
                        min="1" max="65535" required>
                </div>

                <div class="admin-form-group" style="grid-column:1/-1;">
                    <label class="admin-label">Опис (UA)</label>
                    <textarea name="description_ua" class="admin-input admin-textarea" rows="2"
                        placeholder="Український public-сервер CS 1.6"><?= esc(old('description_ua', $server['description_ua'] ?? '')) ?></textarea>
                </div>

                <div class="admin-form-group" style="grid-column:1/-1;">
                    <label class="admin-label">Опис (EN)</label>
                    <textarea name="description_en" class="admin-input admin-textarea" rows="2"
                        placeholder="Ukrainian CS 1.6 public server"><?= esc(old('description_en', $server['description_en'] ?? '')) ?></textarea>
                </div>

                <div class="admin-form-group" style="grid-column:1/-1;">
                    <label class="admin-label">URL банера</label>
                    <input type="text" name="banner_url" class="admin-input"
                        value="<?= esc(old('banner_url', $server['banner_url'] ?? '')) ?>"
                        placeholder="/assets/img/server-banner.webp">
                </div>
            </div>

            <h3 class="admin-form-group-title" style="margin-top:1.5rem;">VPS API (доставка привілегій)</h3>
            <div class="admin-form-grid">
                <div class="admin-form-group" style="grid-column:1/-1;">
                    <label class="admin-label">URL API</label>
                    <input type="text" name="api_url" class="admin-input"
                        value="<?= esc(old('api_url', $server['api_url'] ?? '')) ?>"
                        placeholder="http://185.252.24.118/api/privilege">
                    <small class="admin-hint">Endpoint Python API на VPS (PrivilegeDelivery, ServerQuery)</small>
                </div>

                <div class="admin-form-group" style="grid-column:1/-1;">
                    <label class="admin-label">API Token</label>
                    <input type="password" name="api_key" class="admin-input"
                        value="<?= esc(old('api_key', $server['api_key'] ?? '')) ?>"
                        autocomplete="off"
                        placeholder="Секретний токен">
                </div>
            </div>

            <h3 class="admin-form-group-title" style="margin-top:1.5rem;">Статус</h3>
            <div class="admin-form-grid">
                <div class="admin-form-group">
                    <label class="admin-label">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                            <?= old('is_active', $server['is_active'] ?? 1) ? 'checked' : '' ?>>
                        Активний
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-admin-primary" style="margin-top:1.5rem;">
                <?= $isEdit ? 'Зберегти зміни' : 'Створити сервер' ?>
            </button>
        </form>
    </div>
</section>
