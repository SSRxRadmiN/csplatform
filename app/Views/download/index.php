<!-- Download CS 1.6 Landing Page -->
<link rel="stylesheet" href="/assets/css/download.css">

<!-- Schema.org SoftwareApplication -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "Counter-Strike 1.6 — Українська збірка",
    "operatingSystem": "Windows 10, Windows 11",
    "applicationCategory": "GameApplication",
    "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "UAH"
    },
    "description": "<?= esc($metaDescription ?? '') ?>",
    "url": "https://cs-headshot.com/download-cs-1-6",
    "publisher": {
        "@type": "Organization",
        "name": "CS Headshot",
        "url": "https://cs-headshot.com"
    }
}
</script>

<!-- ═══════════ HERO ═══════════ -->
<section class="dl-hero">
    <div class="dl-hero__bg"></div>
    <div class="dl-hero__glow"></div>
    <div class="dl-hero__particles"></div>
    <img src="/assets/img/download/hero-soldier.png" alt="CS 1.6 Soldier" class="dl-hero__soldier" loading="eager">

    <div class="dl-hero__content">
        <h1 class="dl-hero__title">
            <span><?= lang('App.dl_title_1') ?></span>
            <span>CS 1.6</span>
        </h1>
        <p class="dl-hero__subtitle">
            <?= lang('App.dl_subtitle') ?>
        </p>
        <a href="#download-link" class="dl-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <?= lang('App.dl_btn_download') ?>
        </a>
        <div class="dl-hero__checks">
            <span><span class="dl-check">✔</span> <?= lang('App.dl_check_virus') ?></span>
            <span><span class="dl-check">✔</span> <?= lang('App.dl_check_windows') ?></span>
            <span><span class="dl-check">✔</span> <?= lang('App.dl_check_servers') ?></span>
        </div>
    </div>
</section>

<!-- ═══════════ DIVIDER ═══════════ -->
<div class="dl-divider"></div>

<!-- ═══════════ FEATURES ═══════════ -->
<section class="dl-features">
    <div class="dl-glow-blob dl-glow-blob--1"></div>
    <div class="dl-glow-blob dl-glow-blob--2"></div>
    <div class="dl-features__grid">
        <div class="dl-card dl-fade-up">
            <div class="dl-card__icon">🚀</div>
            <div class="dl-card__title"><?= lang('App.dl_feat_fps') ?></div>
            <div class="dl-card__text">
                <span class="dl-check">✔</span>
                <?= lang('App.dl_feat_fps_desc') ?>
            </div>
        </div>
        <div class="dl-card dl-fade-up">
            <div class="dl-card__icon">🛡️</div>
            <div class="dl-card__title"><?= lang('App.dl_feat_clean') ?></div>
            <div class="dl-card__text">
                <span class="dl-check">✔</span>
                <?= lang('App.dl_feat_clean_desc') ?>
            </div>
        </div>
        <div class="dl-card dl-fade-up">
            <div class="dl-card__icon">🌐</div>
            <div class="dl-card__title"><?= lang('App.dl_feat_servers') ?></div>
            <div class="dl-card__text">
                <span class="dl-check">✔</span>
                <?= lang('App.dl_feat_servers_desc') ?>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════ DIVIDER ═══════════ -->
<div class="dl-divider dl-divider--thin"></div>

<!-- ═══════════ CTA BOTTOM ═══════════ -->
<section class="dl-cta">
    <div class="dl-cta__bg">
        <img src="/assets/img/download/map-bg.jpg" alt="" class="dl-cta__bg-map" loading="lazy">
        <div class="dl-cta__bg-overlay"></div>
        <div class="dl-cta__bg-line"></div>
    </div>
    <div class="dl-cta__grid"></div>
    <img src="/assets/img/download/soldier-bottom.png" alt="CS 1.6 Player" class="dl-cta__soldier" loading="lazy">

    <div class="dl-cta__content">
        <h2 class="dl-cta__title">
            <?= lang('App.dl_cta_title_1') ?> <em><?= lang('App.dl_cta_title_em') ?></em><br>
            <?= lang('App.dl_cta_title_2') ?>
        </h2>
        <a href="https://cs-headshot.com" class="dl-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            CS Headshot
        </a>
    </div>
</section>

<!-- Scroll animations -->
<script>
(function() {
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) entry.target.classList.add('visible');
        });
    }, { threshold: 0.15 });
    document.querySelectorAll('.dl-fade-up').forEach(function(el) { observer.observe(el); });
})();
</script>
