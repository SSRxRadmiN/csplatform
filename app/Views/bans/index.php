<?php
/**
 * Форматування дати бану (relative, localized)
 */
function formatBanDate(int $timestamp): string {
    if ($timestamp <= 0) return '—';
    $diff = time() - $timestamp;
    if ($diff < 60) return lang('Bans.just_now');
    if ($diff < 3600) return floor($diff / 60) . ' ' . lang('Bans.min_ago');
    if ($diff < 86400) return floor($diff / 3600) . ' ' . lang('Bans.hours_ago');
    if ($diff < 172800) return lang('Bans.yesterday');
    if ($diff < 604800) return floor($diff / 86400) . ' ' . lang('Bans.days_ago');
    return date('d.m.Y', $timestamp);
}

/**
 * Статус бейдж (localized)
 */
function banStatusBadge(string $status): array {
    return match($status) {
        'active'    => ['🔴 ' . lang('Bans.status_active'), 'bans-status-active'],
        'permanent' => ['⛔ ' . lang('Bans.status_permanent'), 'bans-status-permanent'],
        'expired'   => ['⏰ ' . lang('Bans.status_expired'), 'bans-status-expired'],
        'unbanned'  => ['✅ ' . lang('Bans.status_unbanned'), 'bans-status-unbanned'],
        default     => [$status, ''],
    };
}
?>

<!-- Bans Page -->
<section class="bans-page">
    <div class="bans-header">
        <div class="section-label">// <?= lang('Bans.label') ?></div>
        <h1 class="bans-title"><?= lang('Bans.title') ?></h1>
        <p class="bans-subtitle"><?= lang('Bans.subtitle') ?></p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bans-error">
            <span>⚠️</span> <?= esc($error) ?>
        </div>
    <?php elseif (!empty($bans) && !empty($bans['bans'])): ?>

        <?php if (!empty($bans['ban_stats'])): ?>
        <div class="bans-summary">
            <div class="bans-summary-item">
                <span class="bans-summary-value"><?= number_format($bans['ban_stats']['all']) ?></span>
                <span class="bans-summary-label"><?= lang('Bans.stat_total') ?></span>
            </div>
            <div class="bans-summary-item bans-summary-active">
                <span class="bans-summary-value"><?= number_format($bans['ban_stats']['active']) ?></span>
                <span class="bans-summary-label"><?= lang('Bans.stat_active') ?></span>
            </div>
            <div class="bans-summary-item bans-summary-perm">
                <span class="bans-summary-value"><?= number_format($bans['ban_stats']['permanent']) ?></span>
                <span class="bans-summary-label"><?= lang('Bans.stat_permanent') ?></span>
            </div>
            <div class="bans-summary-item">
                <span class="bans-summary-value"><?= number_format($bans['ban_stats']['temporary']) ?></span>
                <span class="bans-summary-label"><?= lang('Bans.stat_temporary') ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="bans-controls">
            <form class="bans-search-form" method="get" action="/bans">
                <div class="bans-search-wrap">
                    <svg class="bans-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" name="search" class="bans-search-input" placeholder="<?= lang('Bans.search_hint') ?>" value="<?= esc($search) ?>" autocomplete="off">
                    <?php if (!empty($search)): ?>
                        <a href="/bans" class="bans-search-clear" title="✕">✕</a>
                    <?php endif; ?>
                </div>

                <label class="bans-filter-toggle">
                    <input type="checkbox" name="show_expired" value="1" <?= $showExpired === '1' ? 'checked' : '' ?> onchange="this.form.submit()">
                    <span class="bans-filter-label"><?= lang('Bans.show_expired') ?></span>
                </label>

                <input type="hidden" name="per_page" value="<?= $perPage ?>">
                <button type="submit" class="bans-search-btn"><?= lang('Bans.search_btn') ?></button>
            </form>
        </div>

        <div class="bans-list">
            <?php foreach ($bans['bans'] as $ban): ?>
                <?php
                    [$statusText, $statusClass] = banStatusBadge($ban['status']);
                    $isPermanent = $ban['status'] === 'permanent';
                    $isActive = in_array($ban['status'], ['active', 'permanent']);
                ?>
                <div class="bans-card <?= $isActive ? 'bans-card-active' : '' ?> <?= $isPermanent ? 'bans-card-permanent' : '' ?>">
                    <div class="bans-card-main" onclick="this.parentElement.classList.toggle('expanded')">
                        <div class="bans-card-player">
                            <span class="bans-player-nick"><?= esc($ban['player_nick'] ?: '—') ?></span>
                            <?php if ($isActive): ?>
                                <span class="bans-card-indicator"></span>
                            <?php endif; ?>
                        </div>
                        <div class="bans-card-reason"><?= esc($ban['ban_reason'] ?: '—') ?></div>
                        <div class="bans-card-duration">
                            <span class="bans-duration-text <?= $isPermanent ? 'bans-duration-perm' : '' ?>"><?= esc($ban['duration_text']) ?></span>
                        </div>
                        <div class="bans-card-status">
                            <span class="bans-status <?= $statusClass ?>"><?= $statusText ?></span>
                        </div>
                        <div class="bans-card-date"><?= formatBanDate($ban['ban_created']) ?></div>
                        <div class="bans-card-toggle">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                    </div>

                    <div class="bans-card-details">
                        <div class="bans-details-grid">
                            <div class="bans-detail-item"><span class="bans-detail-label"><?= lang('Bans.col_ban_id') ?></span><span class="bans-detail-value">#<?= $ban['bid'] ?></span></div>
                            <div class="bans-detail-item"><span class="bans-detail-label"><?= lang('Bans.col_nickname') ?></span><span class="bans-detail-value"><?= esc($ban['player_nick'] ?: '—') ?></span></div>
                            <div class="bans-detail-item"><span class="bans-detail-label"><?= lang('Bans.col_steamid') ?></span><span class="bans-detail-value bans-detail-mono"><?= esc($ban['player_id'] ?: '—') ?></span></div>
                            <div class="bans-detail-item"><span class="bans-detail-label"><?= lang('Bans.col_ip') ?></span><span class="bans-detail-value bans-detail-mono"><?= esc($ban['player_ip'] ?: '—') ?></span></div>
                            <div class="bans-detail-item"><span class="bans-detail-label"><?= lang('Bans.col_reason') ?></span><span class="bans-detail-value"><?= esc($ban['ban_reason'] ?: '—') ?></span></div>
                            <div class="bans-detail-item"><span class="bans-detail-label"><?= lang('Bans.col_admin') ?></span><span class="bans-detail-value"><?= esc($ban['admin_nick'] ?: '—') ?></span></div>
                            <div class="bans-detail-item"><span class="bans-detail-label"><?= lang('Bans.col_date') ?></span><span class="bans-detail-value"><?= esc($ban['ban_date'] ?? '—') ?></span></div>
                            <div class="bans-detail-item"><span class="bans-detail-label"><?= lang('Bans.col_duration') ?></span><span class="bans-detail-value"><?= esc($ban['duration_text']) ?></span></div>
                            <div class="bans-detail-item"><span class="bans-detail-label"><?= lang('Bans.col_expires') ?></span><span class="bans-detail-value"><?= esc($ban['expires_text']) ?></span></div>
                            <div class="bans-detail-item"><span class="bans-detail-label"><?= lang('Bans.col_server') ?></span><span class="bans-detail-value"><?= esc($ban['server_name'] ?: '—') ?></span></div>
                            <?php if ($ban['ban_kicks'] > 0): ?>
                            <div class="bans-detail-item"><span class="bans-detail-label"><?= lang('Bans.col_kicks') ?></span><span class="bans-detail-value"><?= $ban['ban_kicks'] ?></span></div>
                            <?php endif; ?>
                        </div>

                        <?php if ($isActive): ?>
                        <div class="bans-detail-cta">
                            <a href="/shop" class="bans-unban-link">🛒 <?= lang('Bans.cta_unban') ?></a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
            $baseParams = [];
            if (!empty($search)) $baseParams['search'] = $search;
            if ($showExpired === '1') $baseParams['show_expired'] = '1';
            $baseParams['per_page'] = $perPage;

            function bansPageUrl(int $p, array $base): string {
                $base['page'] = $p;
                return '/bans?' . http_build_query($base);
            }
        ?>

        <?php if ($bans['pages'] > 1): ?>
            <div class="bans-pagination">
                <?php if ($curPage > 1): ?>
                    <a href="<?= bansPageUrl($curPage - 1, $baseParams) ?>" class="bans-page-btn">←</a>
                <?php endif; ?>
                <?php
                    $startP = max(1, $curPage - 3);
                    $endP = min($bans['pages'], $curPage + 3);
                ?>
                <?php if ($startP > 1): ?>
                    <a href="<?= bansPageUrl(1, $baseParams) ?>" class="bans-page-btn">1</a>
                    <?php if ($startP > 2): ?><span class="bans-page-dots">…</span><?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $startP; $i <= $endP; $i++): ?>
                    <a href="<?= bansPageUrl($i, $baseParams) ?>" class="bans-page-btn <?= $i === $curPage ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <?php if ($endP < $bans['pages']): ?>
                    <?php if ($endP < $bans['pages'] - 1): ?><span class="bans-page-dots">…</span><?php endif; ?>
                    <a href="<?= bansPageUrl($bans['pages'], $baseParams) ?>" class="bans-page-btn"><?= $bans['pages'] ?></a>
                <?php endif; ?>
                <?php if ($curPage < $bans['pages']): ?>
                    <a href="<?= bansPageUrl($curPage + 1, $baseParams) ?>" class="bans-page-btn">→</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="bans-per-page">
            <?= lang('Bans.show_label') ?>:
            <?php foreach ([20, 50, 100] as $pp): ?>
                <?php
                    $ppParams = $baseParams;
                    $ppParams['per_page'] = $pp;
                    unset($ppParams['page']);
                ?>
                <a href="/bans?<?= http_build_query($ppParams) ?>" class="bans-pp-btn <?= $perPage == $pp ? 'active' : '' ?>"><?= $pp ?></a>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="bans-empty">
            <?php if (!empty($search)): ?>
                <p><?= lang('Bans.search_btn') ?>: «<?= esc($search) ?>» — <?= lang('App.no_results') ?></p>
                <a href="/bans" class="bans-reset-link"><?= lang('App.back') ?></a>
            <?php else: ?>
                <p>🎮 <?= lang('App.no_results') ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
