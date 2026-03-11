<section class="account-page">
    <div class="account-page-header">
        <a href="/account" class="product-breadcrumb-link">← Назад до кабінету</a>
        <h1 class="account-page-title">Історія покупок</h1>
    </div>

    <?php if (empty($orders)): ?>
        <div class="account-empty">
            <p>У вас ще немає покупок</p>
            <a href="/shop" class="btn-buy-sm">Перейти до магазину →</a>
        </div>
    <?php else: ?>
        <div class="orders-table-wrap">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Товар</th>
                        <th>Сума</th>
                        <th>Статус</th>
                        <th>Steam ID</th>
                        <th>Створено</th>
                        <th>Діє до</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <?php
                            $statusMap = [
                                'pending'   => ['Очікує', 'pending'],
                                'paid'      => ['Оплачено', 'paid'],
                                'delivered' => ['Доставлено', 'delivered'],
                                'failed'    => ['Помилка', 'failed'],
                                'expired'   => ['Завершено', 'expired'],
                                'refunded'  => ['Повернено', 'refunded'],
                            ];
                            $st = $statusMap[$o['status']] ?? ['—', 'unknown'];
                        ?>
                        <tr>
                            <td class="orders-table-id"><?= $o['id'] ?></td>
                            <td class="orders-table-name"><?= esc($o['product_name'] ?? '—') ?></td>
                            <td class="orders-table-amount"><?= (int) $o['amount'] ?> ₴</td>
                            <td><span class="order-status order-status--<?= $st[1] ?>"><?= $st[0] ?></span></td>
                            <td class="orders-table-steam"><code><?= esc($o['steam_id'] ?? '—') ?></code></td>
                            <td class="orders-table-date"><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
                            <td class="orders-table-date">
                                <?php if (!empty($o['expires_at'])): ?>
                                    <?= date('d.m.Y', strtotime($o['expires_at'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <div class="orders-summary">
            <span>Всього замовлень: <strong><?= count($orders) ?></strong></span>
        </div>
    <?php endif ?>
</section>
