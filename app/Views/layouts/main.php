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
        </ul>

        <div class="nav-right">
            <a href="/lang/<?= (session()->get('lang') ?? 'ua') === 'ua' ? 'en' : 'ua' ?>" class="lang-switch">
                <?= (session()->get('lang') ?? 'ua') === 'ua' ? 'EN' : 'UA' ?>
            </a>

            <?php if (session()->get('user_id')): ?>
                <a href="/account" class="btn-ghost"><?= esc(session()->get('user_name') ?? session()->get('user_email')) ?></a>
                <?php if (session()->get('user_role') === 'admin'): ?>
                    <a href="/admin" class="btn-ghost">Admin</a>
                <?php endif; ?>
                <a href="/logout" class="btn-primary">Вийти</a>
            <?php else: ?>
                <a href="/login" class="btn-ghost">Увійти</a>
                <a href="/register" class="btn-primary">Реєстрація</a>
            <?php endif; ?>
        </div>
    </nav>

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
