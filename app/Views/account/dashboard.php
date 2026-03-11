<?php
$userName  = session()->get('user_name') ?? session()->get('user_email');
$userSteam = session()->get('user_steam') ?? '—';
$userRole  = session()->get('user_role') ?? 'player';
?>

<section class="account-page">
    <!-- Header -->
    <div class="account-header">
        <div class="account-avatar">
            <span><?= mb_strtoupper(mb_substr($userName, 0, 1)) ?></span>
        </div>
        <div class="account-header-info">
            <h1 class="account-name"><?= esc($userName) ?></h1>
            <div class="account-meta">
                <span class="account-steam"><code><?= esc($userSteam) ?></code></span>
                <span class="account-role account-role--<?= esc($userRole) ?>"><?= esc(mb_strtoupper($userRole)) ?></span>
            </div>
        </div>
        <div class="account-actions">
            <a href="/account/edit" class="btn-ghost-sm">Редагувати профіль</a>
            <a href="/logout" class="btn-ghost-sm btn-ghost-sm--muted">Вийти</a>
        </div>
    </div>

    <!-- Active Privileges -->
    <div class="account-section">
        <h2 class="account-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            Активні привілеї
        </h2>

        <?php if (empty($privileges)): ?>
            <div class="account-empty">
                <p>У вас поки немає активних привілегій</p>
                <a href="/shop" class="btn-buy-sm">Перейти до магазину →</a>
            </div>
        <?php else: ?>
            <div class="privilege-grid">
                <?php foreach ($privileges as $p): ?>
                    <div class="privilege-card">
                        <div class="privilege-card-top">
                            <span class="privilege-name"><?= esc($p['product_name'] ?? 'Привілегія #' . $p['id']) ?></span>
                            <span class="privilege-status">Активна</span>
                        </div>
                        <?php if (!empty($p['expires_at'])): ?>
                            <?php
                                $expires = strtotime($p['expires_at']);
                                $daysLeft = max(0, (int) ceil(($expires - time()) / 86400));
                            ?>
                            <div class="privilege-expires">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                Залишилось <?= $daysLeft ?> <?= $daysLeft === 1 ? 'день' : ($daysLeft < 5 ? 'дні' : 'днів') ?>
                                <span class="privilege-date">(до <?= date('d.m.Y', $expires) ?>)</span>
                            </div>
                        <?php else: ?>
                            <div class="privilege-expires">Безстроково</div>
                        <?php endif ?>
                        <?php if (!empty($p['amx_flags'])): ?>
                            <div class="privilege-flags">Флаги: <code><?= esc($p['amx_flags']) ?></code></div>
                        <?php endif ?>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </div>

    <!-- Recent Orders -->
    <div class="account-section">
        <div class="account-section-header">
            <h2 class="account-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Останні замовлення
            </h2>
            <?php if (!empty($orders)): ?>
                <a href="/account/purchases" class="account-section-link">Всі покупки →</a>
            <?php endif ?>
        </div>

        <?php if (empty($orders)): ?>
            <div class="account-empty">
                <p>Замовлень поки немає</p>
            </div>
        <?php else: ?>
            <div class="orders-table-wrap">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Товар</th>
                            <th>Сума</th>
                            <th>Статус</th>
                            <th>Дата</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td class="orders-table-name"><?= esc($o['product_name'] ?? '—') ?></td>
                                <td class="orders-table-amount"><?= (int) $o['amount'] ?> ₴</td>
                                <td>
                                    <?php
                                        $statusMap = [
                                            'pending'   => ['Очікує', 'pending'],
                                            'paid'      => ['Оплачено', 'paid'],
                                            'delivered'  => ['Доставлено', 'delivered'],
                                            'failed'    => ['Помилка', 'failed'],
                                            'expired'   => ['Завершено', 'expired'],
                                            'refunded'  => ['Повернено', 'refunded'],
                                        ];
                                        $st = $statusMap[$o['status']] ?? ['—', 'unknown'];
                                    ?>
                                    <span class="order-status order-status--<?= $st[1] ?>"><?= $st[0] ?></span>
                                </td>
                                <td class="orders-table-date"><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
    </div>
</section>
