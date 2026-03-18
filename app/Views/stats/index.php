<?php
/**
 * Форматування ігрового часу (localized)
 */
function formatGametime(int $seconds): string {
    if ($seconds <= 0) return '0' . lang('Stats.minutes');
    $d = floor($seconds / 86400);
    $h = floor(($seconds % 86400) / 3600);
    $m = floor(($seconds % 3600) / 60);
    $parts = [];
    $dLabel = current_lang() === 'en' ? 'd' : 'д';
    if ($d > 0) $parts[] = $d . $dLabel;
    if ($h > 0) $parts[] = $h . lang('Stats.hours');
    if ($m > 0 && $d == 0) $parts[] = $m . lang('Stats.minutes');
    return implode(' ', $parts) ?: '0' . lang('Stats.minutes');
}

/**
 * Скіл рівень
 */
function getSkillBadge(int $skill): array {
    if ($skill >= 140) return ['Godlike', 'stats-skill-god'];
    if ($skill >= 125) return ['Pro+', 'stats-skill-pro'];
    if ($skill >= 115) return ['Pro', 'stats-skill-pro'];
    if ($skill >= 108) return ['High+', 'stats-skill-high'];
    if ($skill >= 103) return ['High', 'stats-skill-high'];
    if ($skill >= 98)  return ['Medium+', 'stats-skill-med'];
    if ($skill >= 93)  return ['Medium', 'stats-skill-med'];
    if ($skill >= 88)  return ['Low+', 'stats-skill-low'];
    if ($skill >= 80)  return ['Low', 'stats-skill-low'];
    return ['Newbie', 'stats-skill-newbie'];
}
?>

<section class="stats-page">
    <div class="stats-header">
        <h1 class="stats-title"><?= lang('Stats.title') ?></h1>
        <p class="stats-subtitle"><?= lang('Stats.subtitle') ?></p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="stats-error"><span>⚠️</span> <?= esc($error) ?></div>
    <?php elseif (!empty($stats) && !empty($stats['players'])): ?>

        <div class="stats-summary">
            <div class="stats-summary-item">
                <span class="stats-summary-value"><?= number_format($stats['total']) ?></span>
                <span class="stats-summary-label"><?= lang('Stats.stat_players') ?></span>
            </div>
            <?php if (!empty($stats['army_enable'])): ?>
            <div class="stats-summary-item">
                <span class="stats-summary-value"><?= count($stats['level_names'] ?? []) ?></span>
                <span class="stats-summary-label"><?= lang('Stats.stat_ranks') ?></span>
            </div>
            <?php endif; ?>
            <div class="stats-summary-item">
                <span class="stats-summary-value"><?= $perPage ?></span>
                <span class="stats-summary-label"><?= lang('Stats.stat_per_page') ?></span>
            </div>
        </div>

        <div class="stats-controls">
            <form class="stats-search-form" method="get" action="/stats">
                <div class="stats-search-wrap">
                    <svg class="stats-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" name="search" class="stats-search-input" placeholder="<?= lang('Stats.search_hint') ?>" value="<?= esc($search ?? '') ?>" autocomplete="off">
                    <?php if (!empty($search)): ?>
                        <a href="/stats?per_page=<?= $perPage ?>" class="stats-search-clear" title="✕">✕</a>
                    <?php endif; ?>
                </div>
                <input type="hidden" name="per_page" value="<?= $perPage ?>">
                <button type="submit" class="stats-search-btn"><?= lang('Stats.search_btn') ?></button>
            </form>
            <div class="stats-per-page">
                <?= lang('Stats.show') ?>:
                <?php foreach ([15, 30, 50, 100] as $pp): ?>
                    <a href="/stats?per_page=<?= $pp ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"
                       class="stats-pp-btn <?= $perPage == $pp ? 'active' : '' ?>"><?= $pp ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (!empty($search)): ?>
            <div class="stats-search-result">
                <?= lang('Stats.search_results', [esc($search), number_format($stats['total'])]) ?>
            </div>
        <?php endif; ?>

        <div class="stats-table-wrap">
            <table class="stats-table">
                <thead>
                    <tr>
                        <th class="stats-th-place"><?= lang('Stats.col_rank') ?></th>
                        <?php if (!empty($stats['army_enable'])): ?>
                            <th class="stats-th-rank"><?= lang('Stats.col_title') ?></th>
                        <?php endif; ?>
                        <th class="stats-th-nick"><?= lang('Stats.col_nick') ?></th>
                        <?php if (!empty($stats['statsx_enable'])): ?>
                            <th class="stats-th-skill"><?= lang('Stats.col_skill') ?></th>
                        <?php endif; ?>
                        <th class="stats-th-num">🔫</th>
                        <th class="stats-th-num">💀</th>
                        <th class="stats-th-num"><?= lang('Stats.col_kd') ?></th>
                        <th class="stats-th-num">🎯</th>
                        <th class="stats-th-num"><?= lang('Stats.col_hs') ?></th>
                        <?php if (!empty($stats['army_enable'])): ?>
                            <th class="stats-th-num"><?= lang('Stats.col_xp') ?></th>
                        <?php endif; ?>
                        <th class="stats-th-num stats-hide-mobile"><?= lang('Stats.col_dmg') ?></th>
                        <th class="stats-th-num stats-hide-mobile"><?= lang('Stats.col_damage') ?></th>
                        <th class="stats-th-time stats-hide-mobile"><?= lang('Stats.col_time') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['players'] as $p): ?>
                        <?php
                            $isTop3 = $p['place'] <= 3;
                            $placeClass = match($p['place']) { 1 => 'stats-place-gold', 2 => 'stats-place-silver', 3 => 'stats-place-bronze', default => '' };
                            $placeIcon = match($p['place']) { 1 => '🥇', 2 => '🥈', 3 => '🥉', default => $p['place'] };
                            [$skillText, $skillClass] = getSkillBadge($p['skill']);
                            $kdClass = $p['kd_ratio'] >= 1.5 ? 'stats-kd-great' : ($p['kd_ratio'] >= 1.0 ? 'stats-kd-good' : 'stats-kd-bad');
                        ?>
                        <tr class="<?= $isTop3 ? 'stats-row-top' : '' ?>">
                            <td class="stats-td-place"><span class="stats-place <?= $placeClass ?>"><?= $placeIcon ?></span></td>
                            <?php if (!empty($stats['army_enable'])): ?>
                                <td class="stats-td-rank"><span class="stats-rank-badge" title="<?= esc($p['level_name']) ?>"><?= esc($p['level_name']) ?></span></td>
                            <?php endif; ?>
                            <td class="stats-td-nick"><a href="/stats/player/<?= $p['id'] ?>" class="stats-nick-link"><?= esc($p['nick']) ?></a></td>
                            <?php if (!empty($stats['statsx_enable'])): ?>
                                <td class="stats-td-skill"><span class="stats-skill <?= $skillClass ?>" title="<?= $skillText ?> (<?= $p['skill'] ?>)"><?= $p['skill'] ?></span></td>
                            <?php endif; ?>
                            <td class="stats-td-num"><span class="stats-frags"><?= number_format($p['frags']) ?></span></td>
                            <td class="stats-td-num"><span class="stats-deaths"><?= number_format($p['deaths']) ?></span></td>
                            <td class="stats-td-num"><span class="stats-kd <?= $kdClass ?>"><?= $p['kd_ratio'] ?></span></td>
                            <td class="stats-td-num"><span class="stats-hs"><?= number_format($p['headshots']) ?></span></td>
                            <td class="stats-td-num"><span class="stats-hs-pct"><?= $p['hs_percent'] ?>%</span></td>
                            <?php if (!empty($stats['army_enable'])): ?>
                                <td class="stats-td-num"><span class="stats-xp"><?= number_format($p['xp']) ?></span></td>
                            <?php endif; ?>
                            <td class="stats-td-num stats-hide-mobile"><?= $p['accuracy'] ?>%</td>
                            <td class="stats-td-num stats-hide-mobile"><?= number_format($p['damage']) ?></td>
                            <td class="stats-td-time stats-hide-mobile"><?= formatGametime($p['gametime']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php $paginationExtra = !empty($search) ? '&search=' . urlencode($search) : ''; ?>
        <?php if ($stats['pages'] > 1): ?>
            <div class="stats-pagination">
                <?php if ($curPage > 1): ?>
                    <a href="/stats?page=<?= $curPage - 1 ?>&per_page=<?= $perPage ?><?= $paginationExtra ?>" class="stats-page-btn">←</a>
                <?php endif; ?>
                <?php $startP = max(1, $curPage - 3); $endP = min($stats['pages'], $curPage + 3); ?>
                <?php for ($i = $startP; $i <= $endP; $i++): ?>
                    <a href="/stats?page=<?= $i ?>&per_page=<?= $perPage ?><?= $paginationExtra ?>" class="stats-page-btn <?= $i === $curPage ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <?php if ($curPage < $stats['pages']): ?>
                    <a href="/stats?page=<?= $curPage + 1 ?>&per_page=<?= $perPage ?><?= $paginationExtra ?>" class="stats-page-btn">→</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="stats-empty">
            <?php if (!empty($search)): ?>
                <p><?= lang('Stats.search_empty', [esc($search)]) ?></p>
                <a href="/stats" class="stats-reset-link"><?= lang('Stats.search_clear') ?></a>
            <?php else: ?>
                <p><?= lang('App.no_results') ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
