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

            <h3 class="admin-form-group-title">Підключення</h3>
            <div class="srv-connect-row">
                <div class="srv-connect-fields">
                    <div class="srv-connect-field srv-connect-ip">
                        <label class="admin-label">IP / Хост *</label>
                        <input type="text" name="ip" id="srv-ip" class="admin-input"
                            value="<?= esc(old('ip', $server['ip'] ?? '')) ?>"
                            placeholder="185.252.24.118" required>
                    </div>

                    <div class="srv-connect-field srv-connect-port">
                        <label class="admin-label">Порт *</label>
                        <input type="number" name="port" id="srv-port" class="admin-input"
                            value="<?= esc(old('port', $server['port'] ?? '27015')) ?>"
                            min="1" max="65535" required>
                    </div>
                </div>

                <div class="srv-connect-action">
                    <button type="button" id="srv-probe-btn" class="srv-probe-btn">
                        <svg class="srv-probe-btn-icon" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                        <span class="srv-probe-btn-label">Перевірити статус</span>
                    </button>
                    <span id="srv-probe-status" class="srv-probe-status" aria-live="polite"></span>
                </div>
            </div>

            <h3 class="admin-form-group-title" style="margin-top:1.5rem;">Основне</h3>
            <div class="admin-form-grid">
                <div class="admin-form-group">
                    <label class="admin-label">Назва *</label>
                    <input type="text" name="name" id="srv-name" class="admin-input"
                        value="<?= esc(old('name', $server['name'] ?? '')) ?>"
                        placeholder="РЕАЛЬНІ КАБАНИ | PUBLIC [UA/EU]" required>
                    <small class="admin-hint">Заповниться автоматично після перевірки статусу</small>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Країна</label>
                    <input type="text" name="country" class="admin-input"
                        value="<?= esc(old('country', $server['country'] ?? '')) ?>"
                        placeholder="UA" maxlength="8">
                    <small class="admin-hint">Код країни (UA, EU, US...). Вводиться вручну</small>
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
                    <small class="admin-hint">Вимкнений сервер не опитується і не показується ніде</small>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">
                        <input type="hidden" name="is_verified" value="0">
                        <input type="checkbox" name="is_verified" value="1"
                            <?= old('is_verified', $server['is_verified'] ?? 0) ? 'checked' : '' ?>>
                        Показувати на головній
                    </label>
                    <small class="admin-hint">Сервер зʼявиться в таблиці «Наші сервери» на головній сторінці</small>
                </div>
            </div>

            <button type="submit" class="btn-admin-primary" style="margin-top:1.5rem;">
                <?= $isEdit ? 'Зберегти зміни' : 'Створити сервер' ?>
            </button>
        </form>
    </div>
</section>

<style>
/* ═══ Підключення: ліво (50%) — поля, право (50%) — кнопка ═══ */
.srv-connect-row {
    display: flex;
    gap: 24px;
    align-items: end;        /* кнопка по вертикалі — по центру всього блоку */
    flex-wrap: wrap;
}

.srv-connect-fields {
    display: flex;
    gap: 12px;
    flex: 0 0 calc(50% - 12px);  /* рівно 50% мінус половина gap */
    min-width: 0;
}

.srv-connect-field { display: flex; flex-direction: column; flex: 1 1 0; min-width: 0; }
.srv-connect-ip   { flex: 2 1 0; }
.srv-connect-port { flex: 1 1 0; max-width: 140px; }

.srv-connect-action {
    flex: 1 1 0;                /* друга половина */
    display: flex;
    flex-direction: row;
    align-items: stretch;
    gap: 8px;
    min-width: 0;
}

/* Кнопка — у стилі сайту */
.srv-probe-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 45%;
    height: 38.8px;
    padding: 0 18px;
    border-radius: 8px;
    border: 1px solid rgba(74, 222, 128, 0.35);
    background:
        radial-gradient(ellipse at top, rgba(74, 222, 128, 0.12) 0%, transparent 70%),
        rgba(74, 222, 128, 0.08);
    color: #4ade80;
    font-family: inherit;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.18s ease;
    user-select: none;
}
.srv-probe-btn:hover:not(:disabled) {
    background:
        radial-gradient(ellipse at top, rgba(74, 222, 128, 0.2) 0%, transparent 70%),
        rgba(74, 222, 128, 0.14);
    border-color: #4ade80;
    box-shadow: 0 4px 16px rgba(74, 222, 128, 0.25);
    transform: translateY(-1px);
}
.srv-probe-btn:disabled { opacity: 0.6; cursor: wait; }
.srv-probe-btn-icon { flex-shrink: 0; transition: transform 0.18s ease; }
.srv-probe-btn:hover:not(:disabled) .srv-probe-btn-icon { transform: scale(1.12); }

/* Статус під кнопкою — менший шрифт */
.srv-probe-status {
    font-size: 0.78rem;
    line-height: 1.4;
    min-height: 18px;
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 0 4px;
    word-break: break-word;
}
.srv-probe-status.is-ok    { color: #4ade80; }
.srv-probe-status.is-err   { color: #f87171; }
.srv-probe-status.is-load  { color: #9ca3af; }

.srv-probe-check {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #4ade80;
    color: #050a05;
    font-weight: 700;
    font-size: 10px;
    flex-shrink: 0;
}

.srv-probe-spinner {
    width: 12px;
    height: 12px;
    border: 2px solid rgba(156, 163, 175, 0.2);
    border-top-color: #9ca3af;
    border-radius: 50%;
    animation: srvSpin 0.7s linear infinite;
    flex-shrink: 0;
}
@keyframes srvSpin { to { transform: rotate(360deg); } }

/* Адаптив: на вузьких екранах кнопка йде під поля */
@media (max-width: 720px) {
    .srv-connect-row { flex-direction: column; }
    .srv-connect-fields,
    .srv-connect-action { flex: 1 1 100%; width: 100%; }
}
</style>

<script>
(function () {
    const btn       = document.getElementById('srv-probe-btn');
    const ipInput   = document.getElementById('srv-ip');
    const portInput = document.getElementById('srv-port');
    const nameInput = document.getElementById('srv-name');
    const statusEl  = document.getElementById('srv-probe-status');

    if (!btn) return;

    function setStatus(state, html) {
        statusEl.className = 'srv-probe-status is-' + state;
        statusEl.innerHTML = html;
    }

    btn.addEventListener('click', async function () {
        const ip   = (ipInput.value || '').trim();
        const port = parseInt(portInput.value, 10) || 0;

        if (!ip || port < 1) {
            setStatus('err', 'Заповни IP і порт');
            return;
        }

        btn.disabled = true;
        setStatus('load', '<span class="srv-probe-spinner"></span> Перевіряю...');

        try {
            const url = '/admin/servers/probe?ip=' + encodeURIComponent(ip) + '&port=' + port;
            const r = await fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
            const data = await r.json();

            if (!data.ok) {
                setStatus('err', '✕ ' + (data.error || 'Сервер недоступний'));
                return;
            }

            // Заповнюємо назву якщо вона ще пуста (щоб не затирати редаговану вручну)
            if (nameInput && !nameInput.value.trim()) {
                nameInput.value = data.name || '';
            }

            const summary = (data.name || '?') +
                ' · ' + (data.map || '?') +
                ' · ' + data.players + '/' + data.max_players +
                (data.bots ? ' (+' + data.bots + ' bot)' : '');

            setStatus('ok', '<span class="srv-probe-check">✓</span> ' + summary);
        } catch (err) {
            setStatus('err', '✕ Помилка запиту: ' + err.message);
        } finally {
            btn.disabled = false;
        }
    });
})();
</script>
