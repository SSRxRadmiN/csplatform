<section class="account-page">
    <div class="account-page-header">
        <a href="/account" class="product-breadcrumb-link"><?= lang('Account.back_to_account') ?></a>
        <h1 class="account-page-title"><?= lang('Account.purchases_title') ?></h1>
    </div>

    <?php if (empty($orders)): ?>
        <div class="account-empty">
            <p><?= lang('Account.no_purchases') ?></p>
            <a href="/shop" class="btn-buy-sm"><?= lang('Account.go_shop') ?></a>
        </div>
    <?php else: ?>
        <div class="orders-table-wrap">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th><?= lang('Account.col_id') ?></th>
                        <th><?= lang('Account.col_product') ?></th>
                        <th><?= lang('Account.col_amount') ?></th>
                        <th><?= lang('Account.col_status') ?></th>
                        <th><?= lang('Account.col_steam') ?></th>
                        <th><?= lang('Account.col_created') ?></th>
                        <th><?= lang('Account.col_expires') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <?php $st = order_status_text($o['status']); ?>
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
            <span><?= lang('Account.total_orders') ?>: <strong><?= count($orders) ?></strong></span>
        </div>
    <?php endif ?>
</section>
