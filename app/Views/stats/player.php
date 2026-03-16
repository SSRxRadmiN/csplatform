<?php
function formatGametime2(int $seconds): string {
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

function getSkillBadge2(int $skill): array {
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

$p = $playerData['player'] ?? null;
$armyEnable = $playerData['army_enable'] ?? false;
?>

<section class="stats-page">
    <div class="stats-header">
        <a href="/stats" class="stats-back-link">← Назад до рейтингу</a>
        <?php if ($p): ?>
            <h1 class="stats-title"><?= esc($p['nick']) ?></h1>
            <p class="stats-subtitle">
                Місце #<?= $p['place'] ?>
                <?php if (!empty($p['level_name'])): ?>
                    — <?= esc($p['level_name']) ?>
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>

    <?php if (!empty($error)): ?>
        <div class="stats-error"><span>⚠️</span> <?= esc($error) ?></div>
    <?php elseif ($p): ?>

        <?php
            [$skillText, $skillClass] = getSkillBadge2($p['skill']);
            $kdRatio = $p['kd_ratio'];
            $hsPct = $p['hs_percent'];
            $accuracy = $p['accuracy'];
            $tkPct = $p['frags'] > 0 ? round($p['teamkills'] / $p['frags'] * 100, 1) : 0;
        ?>

        <!-- Player Cards Grid -->
        <div class="stats-player-grid">
            <!-- Main Stats -->
            <div class="stats-player-card">
                <h3 class="stats-card-title">Бойова статистика</h3>
                <div class="stats-player-rows">
                    <div class="stats-player-row">
                        <span class="stats-row-label">Місце</span>
                        <span class="stats-row-value stats-place-gold">#<?= $p['place'] ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">🔫 Вбивства</span>
                        <span class="stats-row-value stats-frags"><?= number_format($p['frags']) ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">💀 Смерті</span>
                        <span class="stats-row-value stats-deaths"><?= number_format($p['deaths']) ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">K/D Ratio</span>
                        <span class="stats-row-value <?= $kdRatio >= 1.0 ? 'stats-kd-good' : 'stats-kd-bad' ?>"><?= $kdRatio ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">🎯 Хедшоти</span>
                        <span class="stats-row-value"><?= number_format($p['headshots']) ?> (<?= $hsPct ?>%)</span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">Скіл</span>
                        <span class="stats-row-value <?= $skillClass ?>"><?= $p['skill'] ?> — <?= $skillText ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">Тімкіли</span>
                        <span class="stats-row-value"><?= number_format($p['teamkills']) ?> (<?= $tkPct ?>%)</span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">Суїцид</span>
                        <span class="stats-row-value"><?= number_format($p['suicide']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Precision -->
            <div class="stats-player-card">
                <h3 class="stats-card-title">Точність</h3>
                <div class="stats-player-rows">
                    <div class="stats-player-row">
                        <span class="stats-row-label">Постріли</span>
                        <span class="stats-row-value"><?= number_format($p['shots']) ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">Влучання</span>
                        <span class="stats-row-value"><?= number_format($p['hits']) ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">Влучність</span>
                        <span class="stats-row-value"><?= $accuracy ?>%</span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">Урон</span>
                        <span class="stats-row-value"><?= number_format($p['damage']) ?></span>
                    </div>
                </div>

                <h3 class="stats-card-title" style="margin-top:1.5rem;">Бомба</h3>
                <div class="stats-player-rows">
                    <div class="stats-player-row">
                        <span class="stats-row-label">💣 Встановив</span>
                        <span class="stats-row-value"><?= number_format($p['planted']) ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">💥 Підірвав</span>
                        <span class="stats-row-value"><?= number_format($p['explode']) ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">🔧 Розмінував</span>
                        <span class="stats-row-value"><?= number_format($p['defused']) ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">Намагався</span>
                        <span class="stats-row-value"><?= number_format($p['defusing']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Game Info -->
            <div class="stats-player-card">
                <h3 class="stats-card-title">Ігрова активність</h3>
                <div class="stats-player-rows">
                    <div class="stats-player-row">
                        <span class="stats-row-label">⏱ Час у грі</span>
                        <span class="stats-row-value"><?= formatGametime2($p['gametime']) ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">Заходів</span>
                        <span class="stats-row-value"><?= number_format($p['connects']) ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">Раундів</span>
                        <span class="stats-row-value"><?= number_format($p['rounds']) ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">Виграв за T</span>
                        <span class="stats-row-value" style="color:#e74c3c;"><?= number_format($p['wint']) ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">Виграв за CT</span>
                        <span class="stats-row-value" style="color:#3498db;"><?= number_format($p['winct']) ?></span>
                    </div>
                </div>

                <?php if ($armyEnable): ?>
                <h3 class="stats-card-title" style="margin-top:1.5rem;">Army Ranks</h3>
                <div class="stats-player-rows">
                    <div class="stats-player-row">
                        <span class="stats-row-label">Звання</span>
                        <span class="stats-row-value"><?= esc($p['level_name']) ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">Рівень</span>
                        <span class="stats-row-value"><?= $p['level'] ?></span>
                    </div>
                    <div class="stats-player-row">
                        <span class="stats-row-label">Досвід (XP)</span>
                        <span class="stats-row-value stats-xp"><?= number_format($p['xp']) ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <div class="stats-empty"><p>Гравця не знайдено</p></div>
    <?php endif; ?>
</section>
