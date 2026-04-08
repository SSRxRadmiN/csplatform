<!-- Download CS 1.6 Landing Page -->
<link rel="stylesheet" href="/assets/css/download.css">

<!-- Schema.org -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "Counter-Strike 1.6 — Українська збірка",
    "operatingSystem": "Windows XP, Windows 7, Windows 8, Windows 10, Windows 11",
    "applicationCategory": "GameApplication",
    "applicationSubCategory": "Action / FPS",
    "softwareVersion": "1.6 (Build 8684, Protocol 48)",
    "fileSize": "750MB",
    "datePublished": "2026-04-07",
    "inLanguage": ["uk", "en"],
    "offers": { "@type": "Offer", "price": "0", "priceCurrency": "UAH" },
    "description": "<?= esc($metaDescription ?? '') ?>",
    "url": "https://cs-headshot.com/download-cs-1-6",
    "downloadUrl": "<?= esc($downloadUrl ?? 'https://cs-headshot.com/download-cs-1-6', 'attr') ?>",
    "publisher": { "@type": "Organization", "name": "CS Headshot", "url": "https://cs-headshot.com" }
}
</script>

<!-- Schema.org HowTo -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "HowTo",
    "name": "<?= lang('App.dl_install_title') ?>",
    "description": "<?= lang('App.dl_install_subtitle') ?>",
    "totalTime": "PT2M",
    "step": [
        { "@type": "HowToStep", "position": 1, "name": "<?= lang('App.dl_install_1_title') ?>", "text": "<?= lang('App.dl_install_1_desc') ?>" },
        { "@type": "HowToStep", "position": 2, "name": "<?= lang('App.dl_install_2_title') ?>", "text": "<?= lang('App.dl_install_2_desc') ?>" },
        { "@type": "HowToStep", "position": 3, "name": "<?= lang('App.dl_install_3_title') ?>", "text": "<?= lang('App.dl_install_3_desc') ?>" },
        { "@type": "HowToStep", "position": 4, "name": "<?= lang('App.dl_install_4_title') ?>", "text": "<?= lang('App.dl_install_4_desc') ?>" }
    ]
}
</script>

<!-- Schema.org FAQPage -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        { "@type": "Question", "name": "<?= lang('App.dl_faq_q1') ?>", "acceptedAnswer": { "@type": "Answer", "text": "<?= lang('App.dl_faq_a1') ?>" } },
        { "@type": "Question", "name": "<?= lang('App.dl_faq_q2') ?>", "acceptedAnswer": { "@type": "Answer", "text": "<?= lang('App.dl_faq_a2') ?>" } },
        { "@type": "Question", "name": "<?= lang('App.dl_faq_q3') ?>", "acceptedAnswer": { "@type": "Answer", "text": "<?= lang('App.dl_faq_a3') ?>" } },
        { "@type": "Question", "name": "<?= lang('App.dl_faq_q4') ?>", "acceptedAnswer": { "@type": "Answer", "text": "<?= lang('App.dl_faq_a4') ?>" } },
        { "@type": "Question", "name": "<?= lang('App.dl_faq_q5') ?>", "acceptedAnswer": { "@type": "Answer", "text": "<?= lang('App.dl_faq_a5') ?>" } },
        { "@type": "Question", "name": "<?= lang('App.dl_faq_q6') ?>", "acceptedAnswer": { "@type": "Answer", "text": "<?= lang('App.dl_faq_a6') ?>" } },
        { "@type": "Question", "name": "<?= lang('App.dl_faq_q7') ?>", "acceptedAnswer": { "@type": "Answer", "text": "<?= lang('App.dl_faq_a7') ?>" } }
    ]
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

<!-- ═══ INCLUDED ═══ -->
<section class="dl-included">
    <div class="dl-wrap">
        <div class="dl-section-head">
            <h2 class="dl-section-title"><?= lang('App.dl_inc_title') ?></h2>
            <p class="dl-section-sub"><?= lang('App.dl_inc_subtitle') ?></p>
        </div>
        <div class="dl-included__grid">
            <?php for ($i = 1; $i <= 6; $i++): ?>
            <div class="dl-inc-item dl-fade-up">
                <div class="dl-inc-item__num">0<?= $i ?></div>
                <div class="dl-inc-item__body">
                    <h3 class="dl-inc-item__title"><?= lang('App.dl_inc_' . $i . '_title') ?></h3>
                    <p class="dl-inc-item__desc"><?= lang('App.dl_inc_' . $i . '_desc') ?></p>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- ═══ INSTALL STEPS ═══ -->
<section class="dl-install">
    <div class="dl-wrap">
        <div class="dl-section-head">
            <h2 class="dl-section-title"><?= lang('App.dl_install_title') ?></h2>
            <p class="dl-section-sub"><?= lang('App.dl_install_subtitle') ?></p>
        </div>
        <ol class="dl-install__steps">
            <?php for ($i = 1; $i <= 4; $i++): ?>
            <li class="dl-step dl-fade-up">
                <div class="dl-step__num"><?= $i ?></div>
                <div class="dl-step__body">
                    <h3 class="dl-step__title"><?= lang('App.dl_install_' . $i . '_title') ?></h3>
                    <p class="dl-step__desc"><?= lang('App.dl_install_' . $i . '_desc') ?></p>
                </div>
            </li>
            <?php endfor; ?>
        </ol>
    </div>
</section>

<!-- ═══ REQUIREMENTS ═══ -->
<section class="dl-req">
    <div class="dl-wrap">
        <div class="dl-section-head">
            <h2 class="dl-section-title"><?= lang('App.dl_req_title') ?></h2>
            <p class="dl-section-sub"><?= lang('App.dl_req_subtitle') ?></p>
        </div>
        <div class="dl-req__grid">
            <div class="dl-req__col dl-fade-up">
                <div class="dl-req__col-head">
                    <span class="dl-req__badge"><?= lang('App.dl_req_min') ?></span>
                </div>
                <dl class="dl-req__list">
                    <div><dt><?= lang('App.dl_req_os') ?></dt><dd><?= lang('App.dl_req_min_os') ?></dd></div>
                    <div><dt><?= lang('App.dl_req_cpu') ?></dt><dd><?= lang('App.dl_req_min_cpu') ?></dd></div>
                    <div><dt><?= lang('App.dl_req_ram') ?></dt><dd><?= lang('App.dl_req_min_ram') ?></dd></div>
                    <div><dt><?= lang('App.dl_req_gpu') ?></dt><dd><?= lang('App.dl_req_min_gpu') ?></dd></div>
                    <div><dt><?= lang('App.dl_req_disk') ?></dt><dd><?= lang('App.dl_req_min_disk') ?></dd></div>
                    <div><dt><?= lang('App.dl_req_net') ?></dt><dd><?= lang('App.dl_req_min_net') ?></dd></div>
                </dl>
            </div>
            <div class="dl-req__col dl-req__col--rec dl-fade-up">
                <div class="dl-req__col-head">
                    <span class="dl-req__badge dl-req__badge--rec"><?= lang('App.dl_req_rec') ?></span>
                </div>
                <dl class="dl-req__list">
                    <div><dt><?= lang('App.dl_req_os') ?></dt><dd><?= lang('App.dl_req_rec_os') ?></dd></div>
                    <div><dt><?= lang('App.dl_req_cpu') ?></dt><dd><?= lang('App.dl_req_rec_cpu') ?></dd></div>
                    <div><dt><?= lang('App.dl_req_ram') ?></dt><dd><?= lang('App.dl_req_rec_ram') ?></dd></div>
                    <div><dt><?= lang('App.dl_req_gpu') ?></dt><dd><?= lang('App.dl_req_rec_gpu') ?></dd></div>
                    <div><dt><?= lang('App.dl_req_disk') ?></dt><dd><?= lang('App.dl_req_rec_disk') ?></dd></div>
                    <div><dt><?= lang('App.dl_req_net') ?></dt><dd><?= lang('App.dl_req_rec_net') ?></dd></div>
                </dl>
            </div>
        </div>
    </div>
</section>

<!-- ═══ SCREENSHOTS ═══ -->
<section class="dl-shots">
    <div class="dl-wrap">
        <div class="dl-section-head">
            <h2 class="dl-section-title"><?= lang('App.dl_shots_title') ?></h2>
            <p class="dl-section-sub"><?= lang('App.dl_shots_subtitle') ?></p>
        </div>
        <div class="dl-shots__grid">
            <?php for ($i = 1; $i <= 6; $i++): ?>
            <a href="/assets/img/download/screens/cs16-screen-<?= $i ?>.webp" class="dl-shot dl-fade-up" target="_blank" rel="noopener">
                <img src="/assets/img/download/screens/cs16-screen-<?= $i ?>.webp"
                     alt="<?= esc(lang('App.dl_shot_' . $i . '_alt'), 'attr') ?>"
                     loading="lazy" width="640" height="360">
            </a>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- ═══ FAQ ═══ -->
<section class="dl-faq">
    <div class="dl-wrap">
        <div class="dl-section-head">
            <h2 class="dl-section-title"><?= lang('App.dl_faq_title') ?></h2>
            <p class="dl-section-sub"><?= lang('App.dl_faq_subtitle') ?></p>
        </div>
        <div class="dl-faq__list">
            <?php for ($i = 1; $i <= 7; $i++): ?>
            <details class="dl-faq__item dl-fade-up">
                <summary class="dl-faq__q">
                    <span><?= lang('App.dl_faq_q' . $i) ?></span>
                    <svg class="dl-faq__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </summary>
                <div class="dl-faq__a"><?= lang('App.dl_faq_a' . $i) ?></div>
            </details>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- ═══ SEO TEXT ═══ -->
<section class="dl-seo">
    <div class="dl-wrap">
        <div class="dl-seo__inner">
            <h2><?= lang('App.dl_seo_h2_1') ?></h2>
            <p><?= lang('App.dl_seo_p1') ?></p>
            <p><?= lang('App.dl_seo_p2') ?></p>

            <h3><?= lang('App.dl_seo_h3_1') ?></h3>
            <p><?= lang('App.dl_seo_p3') ?></p>

            <h3><?= lang('App.dl_seo_h3_2') ?></h3>
            <p><?= lang('App.dl_seo_p4') ?></p>

            <h3><?= lang('App.dl_seo_h3_3') ?></h3>
            <p><?= lang('App.dl_seo_p5') ?></p>
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
