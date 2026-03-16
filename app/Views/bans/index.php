<?php
/**
 * Форматування дати бану (relative)
 */
function formatBanDate(int $timestamp): string {
    if ($timestamp <= 0) return '—';
    $diff = time() - $timestamp;
    if ($diff < 60) return 'Щойно';
    if ($diff < 3600) return floor($diff / 60) . ' хв тому';
    if ($diff < 86400) return floor($diff / 3600) . ' год тому';
    if ($diff < 172800) return 'Вчора';
    if ($diff < 604800) return floor($diff / 86400) . ' дн тому';
    return date('d.m.Y', $timestamp);
}

/**
 * Статус бейдж
 */
function banStatusBadge(string $status): array {
    return match($status) {
        'active'    => ['🔴 Активний', 'bans-status-active'],
        'permanent' => ['⛔ Перманент', 'bans-status-permanent'],
        'expired'   => ['⏰ Закінчився', 'bans-status-expired'],
        'unbanned'  => ['✅ Розбанений', 'bans-status-unbanned'],
        default     => [$status, ''],
    };
}
?>

<!-- Bans Page -->
<section class="bans-page">
    <div class="bans-header">
        <div class="section-label">// Банлист сервера</div>
        <h1 class="bans-title">Заблоковані гравці</h1>
        <p class="bans-subtitle">
            Список гравців, заблокованих за порушення правил сервера Реальні Кабани.
        </p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bans-error">
            <span>⚠️</span> <?= esc($error) ?>
        </div>
    <?php elseif (!empty($bans) && !empty($bans['bans'])): ?>

        <!-- Ban Stats Summary -->
        <?php if (!empty($bans['ban_stats'])): ?>
        <div class="bans-summary">
            <div class="bans-summary-item">
                <span class="bans-summary-value"><?= number_format($bans['ban_stats']['all']) ?></span>
                <span class="bans-summary-label">Всього банів</span>
            </div>
            <div class="bans-summary-item bans-summary-active">
                <span class="bans-summary-value"><?= number_format($bans['ban_stats']['active']) ?></span>
                <span class="bans-summary-label">Активних</span>
            </div>
            <div class="bans-summary-item bans-summary-perm">
                <span class="bans-summary-value"><?= number_format($bans['ban_stats']['permanent']) ?></span>
                <span class="bans-summary-label">Перманентних</span>
            </div>
            <div class="bans-summary-item">
                <span class="bans-summary-value"><?= number_format($bans['ban_stats']['temporary']) ?></span>
                <span class="bans-summary-label">Тимчасових</span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Controls: Search + Filters -->
        <div class="bans-controls">
            <form class="bans-search-form" method="get" action="/bans">
                <div class="bans-search-wrap">
                    <svg class="bans-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text"
                           name="search"
                           class="bans-search-input"
                           placeholder="Пошук по ніку, SteamID, причині..."
                           value="<?= esc($search) ?>"
                           autocomplete="off">
                    <?php if (!empty($search)): ?>
                        <a href="/bans" class="bans-search-clear" title="Очистити">✕</a>
                    <?php endif; ?>
                </div>

                <label class="bans-filter-toggle">
                    <input type="checkbox"
                           name="show_expired"
                           value="1"
                           <?= $showExpired === '1' ? 'checked' : '' ?>
                           onchange="this.form.submit()">
                    <span class="bans-filter-label">Показати розбанених</span>
                </label>

                <input type="hidden" name="per_page" value="<?= $perPage ?>">
                <button type="submit" class="bans-search-btn">Знайти</button>
            </form>
        </div>

        <!-- Bans List -->
        <div class="bans-list">
            <?php foreach ($bans['bans'] as $ban): ?>
                <?php
                    [$statusText, $statusClass] = banStatusBadge($ban['status']);
                    $isPermanent = $ban['status'] === 'permanent';
                    $isActive = in_array($ban['status'], ['active', 'permanent']);
                ?>
                <div class="bans-card <?= $isActive ? 'bans-card-active' : '' ?> <?= $isPermanent ? 'bans-card-permanent' : '' ?>">
                    <!-- Main row (always visible) -->
                    <div class="bans-card-main" onclick="this.parentElement.classList.toggle('expanded')">
                        <div class="bans-card-player">
                            <span class="bans-player-nick"><?= esc($ban['player_nick'] ?: '—') ?></span>
                            <?php if ($isActive): ?>
                                <span class="bans-card-indicator"></span>
                            <?php endif; ?>
                        </div>
                        <div class="bans-card-reason"><?= esc($ban['ban_reason'] ?: 'Не вказано') ?></div>
                        <div class="bans-card-duration">
                            <span class="bans-duration-text <?= $isPermanent ? 'bans-duration-perm' : '' ?>">
                                <?= esc($ban['duration_text']) ?>
                            </span>
                            <?php if ($ban['remaining_text']): ?>
                                <span class="bans-remaining">залишилось <?= esc($ban['remaining_text']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="bans-card-status">
                            <span class="bans-status <?= $statusClass ?>"><?= $statusText ?></span>
                        </div>
                        <div class="bans-card-date"><?= formatBanDate($ban['ban_created']) ?></div>
                        <div class="bans-card-toggle">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Expandable details -->
                    <div class="bans-card-details">
                        <div class="bans-details-grid">
                            <div class="bans-detail-item">
                                <span class="bans-detail-label">Ban ID</span>
                                <span class="bans-detail-value">#<?= $ban['bid'] ?></span>
                            </div>
                            <div class="bans-detail-item">
                                <span class="bans-detail-label">Нікнейм</span>
                                <span class="bans-detail-value"><?= esc($ban['player_nick'] ?: '—') ?></span>
                            </div>
                            <div class="bans-detail-item">
                                <span class="bans-detail-label">SteamID</span>
                                <span class="bans-detail-value bans-detail-mono"><?= esc($ban['player_id'] ?: '—') ?></span>
                            </div>
                            <div class="bans-detail-item">
                                <span class="bans-detail-label">IP адреса</span>
                                <span class="bans-detail-value bans-detail-mono"><?= esc($ban['player_ip'] ?: '—') ?></span>
                            </div>
                            <div class="bans-detail-item">
                                <span class="bans-detail-label">Причина</span>
                                <span class="bans-detail-value"><?= esc($ban['ban_reason'] ?: 'Не вказано') ?></span>
                            </div>
                            <div class="bans-detail-item">
                                <span class="bans-detail-label">Адміністратор</span>
                                <span class="bans-detail-value"><?= esc($ban['admin_nick'] ?: '—') ?></span>
                            </div>
                            <div class="bans-detail-item">
                                <span class="bans-detail-label">Дата бану</span>
                                <span class="bans-detail-value"><?= esc($ban['ban_date'] ?? '—') ?></span>
                            </div>
                            <div class="bans-detail-item">
                                <span class="bans-detail-label">Термін</span>
                                <span class="bans-detail-value"><?= esc($ban['duration_text']) ?></span>
                            </div>
                            <div class="bans-detail-item">
                                <span class="bans-detail-label">Закінчується</span>
                                <span class="bans-detail-value"><?= esc($ban['expires_text']) ?></span>
                            </div>
                            <?php if ($ban['remaining_text']): ?>
                            <div class="bans-detail-item">
                                <span class="bans-detail-label">Залишилось</span>
                                <span class="bans-detail-value bans-detail-remaining"><?= esc($ban['remaining_text']) ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="bans-detail-item">
                                <span class="bans-detail-label">Сервер</span>
                                <span class="bans-detail-value"><?= esc($ban['server_name'] ?: '—') ?></span>
                            </div>
                            <?php if ($ban['ban_kicks'] > 0): ?>
                            <div class="bans-detail-item">
                                <span class="bans-detail-label">Кіків</span>
                                <span class="bans-detail-value"><?= $ban['ban_kicks'] ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($isActive): ?>
                        <div class="bans-detail-cta">
                            <a href="/shop" class="bans-unban-link">
                                🛒 Придбати розбан у магазині
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination & Per Page -->
        <?php
            // Базовий URL для пагінації та per-page (визначаємо до блоків)
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
                    <a href="<?= bansPageUrl($curPage - 1, $baseParams) ?>" class="bans-page-btn">← Назад</a>
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
                    <a href="<?= bansPageUrl($i, $baseParams) ?>"
                       class="bans-page-btn <?= $i === $curPage ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($endP < $bans['pages']): ?>
                    <?php if ($endP < $bans['pages'] - 1): ?><span class="bans-page-dots">…</span><?php endif; ?>
                    <a href="<?= bansPageUrl($bans['pages'], $baseParams) ?>" class="bans-page-btn"><?= $bans['pages'] ?></a>
                <?php endif; ?>

                <?php if ($curPage < $bans['pages']): ?>
                    <a href="<?= bansPageUrl($curPage + 1, $baseParams) ?>" class="bans-page-btn">Далі →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Per Page -->
        <div class="bans-per-page">
            Показати:
            <?php foreach ([20, 50, 100] as $pp): ?>
                <?php
                    $ppParams = $baseParams;
                    $ppParams['per_page'] = $pp;
                    unset($ppParams['page']);
                ?>
                <a href="/bans?<?= http_build_query($ppParams) ?>"
                   class="bans-pp-btn <?= $perPage == $pp ? 'active' : '' ?>"><?= $pp ?></a>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="bans-empty">
            <?php if (!empty($search)): ?>
                <p>За запитом «<?= esc($search) ?>» банів не знайдено.</p>
                <a href="/bans" class="bans-reset-link">Скинути пошук</a>
            <?php else: ?>
                <p>Банлист порожній. Всі грають чесно! 🎮</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
