<!-- Download CS 1.6 Landing Page -->
<link rel="stylesheet" href="/assets/css/download.css">

<!-- Schema.org -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "Counter-Strike 1.6 — Українська збірка",
    "operatingSystem": "Windows 10, Windows 11",
    "applicationCategory": "GameApplication",
    "offers": { "@type": "Offer", "price": "0", "priceCurrency": "UAH" },
    "description": "<?= esc($metaDescription ?? '') ?>",
    "url": "https://cs-headshot.com/download-cs-1-6",
    "publisher": { "@type": "Organization", "name": "CS Headshot", "url": "https://cs-headshot.com" }
}
</script>

<!-- ═══ HERO ═══ -->
<section class="dl-hero">
    <div class="dl-wrap">
        <div class="dl-hero__content">
            <h1 class="dl-hero__title">
                <span><?= lang('App.dl_title_1') ?></span>
                <span>CS 1.6</span>
            </h1>
            <p class="dl-hero__subtitle"><?= lang('App.dl_subtitle') ?></p>

            <a href="<?= $downloadUrl ?? '#' ?>" class="dl-btn" id="downloadBtn" rel="nofollow">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <?= lang('App.dl_btn_download') ?>
            </a>

            <div class="dl-hero__checks">
                <span>
                    <svg class="dl-check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <?= lang('App.dl_check_virus') ?>
                </span>
                <span>
                    <svg class="dl-check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <?= lang('App.dl_check_windows') ?>
                </span>
                <span>
                    <svg class="dl-check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <?= lang('App.dl_check_servers') ?>
                </span>
            </div>
        </div>
    </div>
</section>

<!-- ═══ FEATURES ═══ -->
<section class="dl-features">
    <div class="dl-wrap">
        <div class="dl-features__grid">
            <div class="dl-card dl-fade-up">
                <div class="dl-card__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </div>
                <div class="dl-card__title"><?= lang('App.dl_feat_fps') ?></div>
                <div class="dl-card__text">
                    <svg class="dl-check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?= lang('App.dl_feat_fps_desc') ?>
                </div>
            </div>
            <div class="dl-card dl-fade-up">
                <div class="dl-card__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                </div>
                <div class="dl-card__title"><?= lang('App.dl_feat_clean') ?></div>
                <div class="dl-card__text">
                    <svg class="dl-check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?= lang('App.dl_feat_clean_desc') ?>
                </div>
            </div>
            <div class="dl-card dl-fade-up">
                <div class="dl-card__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                </div>
                <div class="dl-card__title"><?= lang('App.dl_feat_servers') ?></div>
                <div class="dl-card__text">
                    <svg class="dl-check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?= lang('App.dl_feat_servers_desc') ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ CTA ═══ -->
<section class="dl-cta">
    <div class="dl-wrap">
        <div class="dl-cta__inner">
            <div class="dl-cta__content">
                <h2 class="dl-cta__title">
                    <em><?= lang('App.dl_cta_title_1') ?></em> <?= lang('App.dl_cta_title_em') ?><br>
                    <?= lang('App.dl_cta_title_2') ?>
                </h2>
                <a href="<?= $downloadUrl ?? '#' ?>" class="dl-btn" rel="nofollow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    <?= lang('App.dl_btn_download') ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ═══ SEO TEXT ═══ -->
<section class="dl-seo">
    <div class="dl-wrap">
        <div class="dl-seo__inner">
            <h2>Скачати Counter-Strike 1.6 — Українська збірка</h2>
            <p>Шукаєте де скачати CS 1.6 безкоштовно? Наша українська збірка Counter-Strike 1.6 — це чистий клієнт без вірусів та шкідливих програм, оптимізований для стабільної роботи на Windows 10 та Windows 11. Висока продуктивність навіть на слабких комп'ютерах завдяки оптимізованим налаштуванням.</p>
            <p>Після встановлення ви отримаєте доступ до онлайн серверів з низьким пінгом. Сервер «Реальні Кабани» — український паблік з VIP-системою, унікальними моделями зброї та дружньою спільнотою. Приєднуйтесь до гри прямо зараз!</p>
        </div>
    </div>
</section>

<script>
(function() {
    var obs = new IntersectionObserver(function(entries) {
        entries.forEach(function(e) { if (e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.15 });
    document.querySelectorAll('.dl-fade-up').forEach(function(el) { obs.observe(el); });
})();
</script>
