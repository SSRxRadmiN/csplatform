<?php
/**
 * Форматування ігрового часу
 */
function formatGametime(int $seconds): string {
    if ($seconds <= 0) return '0хв';
    $d = floor($seconds / 86400);
    $h = floor(($seconds % 86400) / 3600);
    $m = floor(($seconds % 3600) / 60);
    $parts = [];
    if ($d > 0) $parts[] = $d . 'д';
    if ($h > 0) $parts[] = $h . 'год';
    if ($m > 0 && $d == 0) $parts[] = $m . 'хв';
    return implode(' ', $parts) ?: '0хв';
}

/**
 * Скіл рівень як текст
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

<!-- Stats Page -->
<section class="stats-page">
    <div class="stats-header">
        <div class="section-label">// Статистика сервера</div>
        <h1 class="stats-title">Рейтинг гравців</h1>
        <p class="stats-subtitle">
            Статистика гравців сервера Реальні Кабани. Дані оновлюються в реальному часі.
        </p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="stats-error">
            <span>⚠️</span> <?= esc($error) ?>
        </div>
    <?php elseif (!empty($stats) && !empty($stats['players'])): ?>

        <!-- Stats Summary -->
        <div class="stats-summary">
            <div class="stats-summary-item">
                <span class="stats-summary-value"><?= number_format($stats['total']) ?></span>
                <span class="stats-summary-label">Гравців</span>
            </div>
            <?php if (!empty($stats['army_enable'])): ?>
            <div class="stats-summary-item">
                <span class="stats-summary-value"><?= count($stats['level_names'] ?? []) ?></span>
                <span class="stats-summary-label">Звань</span>
            </div>
            <?php endif; ?>
            <div class="stats-summary-item">
                <span class="stats-summary-value"><?= $perPage ?></span>
                <span class="stats-summary-label">На сторінці</span>
            </div>
        </div>

        <!-- Per Page Selector -->
        <div class="stats-controls">
            <div class="stats-per-page">
                Показати:
                <?php foreach ([15, 30, 50, 100] as $pp): ?>
                    <a href="/stats?per_page=<?= $pp ?>"
                       class="stats-pp-btn <?= $perPage == $pp ? 'active' : '' ?>"><?= $pp ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Stats Table -->
        <div class="stats-table-wrap">
            <table class="stats-table">
                <thead>
                    <tr>
                        <th class="stats-th-place">#</th>
                        <?php if (!empty($stats['army_enable'])): ?>
                            <th class="stats-th-rank">Звання</th>
                        <?php endif; ?>
                        <th class="stats-th-nick">Нікнейм</th>
                        <?php if (!empty($stats['statsx_enable'])): ?>
                            <th class="stats-th-skill" title="Скіл рейтинг">Скіл</th>
                        <?php endif; ?>
                        <th class="stats-th-num" title="Вбивства">🔫</th>
                        <th class="stats-th-num" title="Смерті">💀</th>
                        <th class="stats-th-num" title="K/D">K/D</th>
                        <th class="stats-th-num" title="В голову">🎯</th>
                        <th class="stats-th-num" title="HS%">HS%</th>
                        <?php if (!empty($stats['army_enable'])): ?>
                            <th class="stats-th-num" title="Досвід">XP</th>
                        <?php endif; ?>
                        <th class="stats-th-num stats-hide-mobile" title="Влучність">Влуч%</th>
                        <th class="stats-th-num stats-hide-mobile" title="Урон">Урон</th>
                        <th class="stats-th-time stats-hide-mobile" title="Час у грі">Час</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['players'] as $p): ?>
                        <?php
                            $isTop3 = $p['place'] <= 3;
                            $placeClass = match($p['place']) {
                                1 => 'stats-place-gold',
                                2 => 'stats-place-silver',
                                3 => 'stats-place-bronze',
                                default => ''
                            };
                            $placeIcon = match($p['place']) {
                                1 => '🥇',
                                2 => '🥈',
                                3 => '🥉',
                                default => $p['place']
                            };
                            [$skillText, $skillClass] = getSkillBadge($p['skill']);
                            $kdClass = $p['kd_ratio'] >= 1.5 ? 'stats-kd-great' :
                                      ($p['kd_ratio'] >= 1.0 ? 'stats-kd-good' : 'stats-kd-bad');
                        ?>
                        <tr class="<?= $isTop3 ? 'stats-row-top' : '' ?>">
                            <td class="stats-td-place">
                                <span class="stats-place <?= $placeClass ?>"><?= $placeIcon ?></span>
                            </td>
                            <?php if (!empty($stats['army_enable'])): ?>
                                <td class="stats-td-rank">
                                    <span class="stats-rank-badge" title="<?= esc($p['level_name']) ?>">
                                        <?= esc($p['level_name']) ?>
                                    </span>
                                </td>
                            <?php endif; ?>
                            <td class="stats-td-nick">
                                <a href="/stats/player/<?= $p['id'] ?>" class="stats-nick-link">
                                    <?= esc($p['nick']) ?>
                                </a>
                            </td>
                            <?php if (!empty($stats['statsx_enable'])): ?>
                                <td class="stats-td-skill">
                                    <span class="stats-skill <?= $skillClass ?>"
                                          title="<?= $skillText ?> (<?= $p['skill'] ?>)">
                                        <?= $p['skill'] ?>
                                    </span>
                                </td>
                            <?php endif; ?>
                            <td class="stats-td-num">
                                <span class="stats-frags"><?= number_format($p['frags']) ?></span>
                            </td>
                            <td class="stats-td-num">
                                <span class="stats-deaths"><?= number_format($p['deaths']) ?></span>
                            </td>
                            <td class="stats-td-num">
                                <span class="stats-kd <?= $kdClass ?>"><?= $p['kd_ratio'] ?></span>
                            </td>
                            <td class="stats-td-num">
                                <span class="stats-hs"><?= number_format($p['headshots']) ?></span>
                            </td>
                            <td class="stats-td-num">
                                <span class="stats-hs-pct"><?= $p['hs_percent'] ?>%</span>
                            </td>
                            <?php if (!empty($stats['army_enable'])): ?>
                                <td class="stats-td-num">
                                    <span class="stats-xp"><?= number_format($p['xp']) ?></span>
                                </td>
                            <?php endif; ?>
                            <td class="stats-td-num stats-hide-mobile"><?= $p['accuracy'] ?>%</td>
                            <td class="stats-td-num stats-hide-mobile"><?= number_format($p['damage']) ?></td>
                            <td class="stats-td-time stats-hide-mobile"><?= formatGametime($p['gametime']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($stats['pages'] > 1): ?>
            <div class="stats-pagination">
                <?php if ($curPage > 1): ?>
                    <a href="/stats?page=<?= $curPage - 1 ?>&per_page=<?= $perPage ?>" class="stats-page-btn">← Назад</a>
                <?php endif; ?>

                <?php
                    $startP = max(1, $curPage - 3);
                    $endP = min($stats['pages'], $curPage + 3);
                ?>
                <?php for ($i = $startP; $i <= $endP; $i++): ?>
                    <a href="/stats?page=<?= $i ?>&per_page=<?= $perPage ?>"
                       class="stats-page-btn <?= $i === $curPage ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($curPage < $stats['pages']): ?>
                    <a href="/stats?page=<?= $curPage + 1 ?>&per_page=<?= $perPage ?>" class="stats-page-btn">Далі →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="stats-empty">
            <p>Статистика поки що порожня. Заходьте на сервер і грайте!</p>
        </div>
    <?php endif; ?>
</section>
