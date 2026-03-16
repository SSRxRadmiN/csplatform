<!DOCTYPE html>
<html lang="<?= session()->get('lang') ?? 'ua' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'CS Headshot | РЕАЛЬНІ КАБАНИ Public Server CS 1.6') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
    <?php if (str_contains($page ?? '', 'admin/')): ?>
        <link rel="stylesheet" href="/assets/css/admin.css">
    <?php endif ?>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/favicon/site.webmanifest">
</head>
<body>

    <!-- Ambient Glow -->
    <div class="ambient-glow g1"></div>
    <div class="ambient-glow g2"></div>
    <div class="ambient-glow g3"></div>
    <div class="grid-pattern"></div>

    <!-- Navigation -->
    <nav>
        <a href="/" class="nav-logo">
            <div class="nav-logo-icon">HS</div>
            <div class="nav-logo-text">CS <span>Headshot</span></div>
        </a>

        <ul class="nav-links">
            <li><a href="/" class="<?= current_url() === base_url('/') ? 'active' : '' ?>">Головна</a></li>
            <li><a href="/shop" class="<?= str_contains(current_url(), '/shop') ? 'active' : '' ?>">Магазин</a></li>
            <li><a href="/stats" class="<?= str_contains(current_url(), '/stats') ? 'active' : '' ?>">Статистика</a></li>
            <li><a href="/bans" class="<?= str_contains(current_url(), '/bans') ? 'active' : '' ?>">Банлист</a></li>
        </ul>

        <div class="nav-right">
            <a href="/lang/<?= (session()->get('lang') ?? 'ua') === 'ua' ? 'en' : 'ua' ?>" class="lang-switch">
                <?= (session()->get('lang') ?? 'ua') === 'ua' ? 'EN' : 'UA' ?>
            </a>

            <?php if (session()->get('user_id')): ?>
                <a href="/account" class="btn-ghost nav-hide-mobile"><?= esc(session()->get('user_name') ?? session()->get('user_email')) ?></a>
                <?php if (session()->get('user_role') === 'admin'): ?>
                    <a href="/admin" class="btn-ghost nav-hide-mobile">Admin</a>
                <?php endif; ?>
                <a href="/logout" class="btn-primary nav-hide-mobile">Вийти</a>
            <?php else: ?>
                <a href="/login" class="btn-ghost nav-hide-mobile">Увійти</a>
                <a href="/register" class="btn-primary nav-hide-mobile">Реєстрація</a>
            <?php endif; ?>

            <!-- Burger button (mobile only) -->
            <button class="burger-btn" id="burgerBtn" aria-label="Меню">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    <!-- Mobile Drawer -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    <div class="mobile-drawer" id="mobileDrawer">
        <div class="drawer-links">
            <a href="/" class="drawer-link <?= current_url() === base_url('/') ? 'active' : '' ?>">
                <span class="drawer-icon">🏠</span> Головна
            </a>
            <a href="/shop" class="drawer-link <?= str_contains(current_url(), '/shop') ? 'active' : '' ?>">
                <span class="drawer-icon">🛒</span> Магазин
            </a>
            <a href="/stats" class="drawer-link <?= str_contains(current_url(), '/stats') ? 'active' : '' ?>">
                <span class="drawer-icon">📊</span> Статистика
            </a>
            <a href="/bans" class="drawer-link <?= str_contains(current_url(), '/bans') ? 'active' : '' ?>">
                <span class="drawer-icon">🚫</span> Банлист
            </a>
        </div>
        <div class="drawer-divider"></div>
        <div class="drawer-auth">
            <?php if (session()->get('user_id')): ?>
                <div class="drawer-user">
                    <div class="drawer-user-avatar"><?= strtoupper(substr(session()->get('user_name') ?? session()->get('user_email'), 0, 1)) ?></div>
                    <div class="drawer-user-info">
                        <div class="drawer-user-name"><?= esc(session()->get('user_name') ?? session()->get('user_email')) ?></div>
                        <?php if (session()->get('user_role') === 'admin'): ?>
                            <div class="drawer-user-role">Адміністратор</div>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="/account" class="drawer-link">
                    <span class="drawer-icon">👤</span> Мій кабінет
                </a>
                <?php if (session()->get('user_role') === 'admin'): ?>
                    <a href="/admin" class="drawer-link">
                        <span class="drawer-icon">⚙️</span> Адмін-панель
                    </a>
                <?php endif; ?>
                <a href="/logout" class="drawer-link drawer-link-logout">
                    <span class="drawer-icon">🚪</span> Вийти
                </a>
            <?php else: ?>
                <a href="/login" class="drawer-btn-login">Увійти</a>
                <a href="/register" class="drawer-btn-register">Реєстрація</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="flash-message flash-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="flash-message flash-error"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="flash-message flash-error">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <div><?= esc($err) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Page Content -->
    <main>
        <?= view($page, $data ?? []) ?>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-left">© <?= date('Y') ?> CS Headshot — Реальні Кабани CS 1.6</div>
        <ul class="footer-links">
            <li><a href="#">Правила</a></li>
            <li><a href="#">Telegram</a></li>
            <li><a href="#">Discord</a></li>
        </ul>
    </footer>

    <script src="/assets/js/app.js"></script>
</body>
</html>
