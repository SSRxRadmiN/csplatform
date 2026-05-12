/* ============================================
   CS Headshot — Main JavaScript
   ============================================ */

// Scroll fade-in animation
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, { threshold: 0.15 });

document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

// Auto-hide flash messages
document.querySelectorAll('.flash-message').forEach(el => {
    setTimeout(() => el.remove(), 4000);
});

// Copy IP to clipboard on click
document.querySelectorAll('.status-item .value').forEach(el => {
    if (el.textContent.includes(':')) {
        el.style.cursor = 'pointer';
        el.title = 'Натисни щоб скопіювати';
        el.addEventListener('click', () => {
            navigator.clipboard.writeText(el.textContent.trim());
            el.textContent = 'Скопійовано!';
            setTimeout(() => location.reload(), 1000);
        });
    }
});

/* ═══ MOBILE BURGER MENU ═══ */
(function() {
    const burger = document.getElementById('burgerBtn');
    const drawer = document.getElementById('mobileDrawer');
    const overlay = document.getElementById('mobileOverlay');
    if (!burger || !drawer || !overlay) return;

    function openMenu() {
        drawer.classList.add('open');
        overlay.classList.add('open');
        burger.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        drawer.classList.remove('open');
        overlay.classList.remove('open');
        burger.classList.remove('open');
        document.body.style.overflow = '';
    }

    burger.addEventListener('click', function(e) {
        e.stopPropagation();
        if (drawer.classList.contains('open')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    overlay.addEventListener('click', closeMenu);

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && drawer.classList.contains('open')) {
            closeMenu();
        }
    });

    // Close on resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 900 && drawer.classList.contains('open')) {
            closeMenu();
        }
    });
})();

/* ═══ IMAGE LIGHTBOX ═══ */
function openLightbox(src) {
    if (!src) return;

    // Створюємо overlay
    const overlay = document.createElement('div');
    overlay.className = 'lightbox-overlay';

    const img = document.createElement('img');
    img.className = 'lightbox-img';
    img.src = src;
    img.alt = 'Preview';

    const closeBtn = document.createElement('button');
    closeBtn.className = 'lightbox-close';
    closeBtn.innerHTML = '&times;';
    closeBtn.onclick = closeLightbox;

    overlay.appendChild(closeBtn);
    overlay.appendChild(img);
    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';

    // Закриття по кліку на overlay (не на картинку)
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeLightbox();
    });

    // Закриття по Escape
    overlay._escHandler = function(e) {
        if (e.key === 'Escape') closeLightbox();
    };
    document.addEventListener('keydown', overlay._escHandler);

    // Анімація появи
    requestAnimationFrame(() => overlay.classList.add('open'));
}

function closeLightbox() {
    const overlay = document.querySelector('.lightbox-overlay');
    if (!overlay) return;

    if (overlay._escHandler) {
        document.removeEventListener('keydown', overlay._escHandler);
    }

    overlay.classList.remove('open');
    setTimeout(() => {
        overlay.remove();
        // Повертаємо скрол тільки якщо немає інших overlay
        if (!document.querySelector('.lightbox-overlay')) {
            document.body.style.overflow = '';
        }
    }, 250);
}

/* ═══ COUNTER COUNT-UP ANIMATION ═══ */
(function() {
    const counters = document.querySelectorAll('.counter-value[data-count]');
    if (!counters.length) return;

    const animateCounter = (el) => {
        const target = parseInt(el.dataset.count, 10);
        const duration = 1500;
        const start = performance.now();

        const tick = (now) => {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(eased * target);
            if (progress < 1) requestAnimationFrame(tick);
            else el.textContent = target;
        };

        requestAnimationFrame(tick);
    };

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(el => counterObserver.observe(el));
})();
/* ═══════════════════════════════════════════════════════════════
   НАШІ СЕРВЕРИ — копіювання IP по кліку.
   Додати в кінець app.js.
   ═══════════════════════════════════════════════════════════════ */

(function () {
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.srv-ip-btn');
        if (!btn) return;

        const ip = btn.dataset.copy;
        if (!ip) return;

        // Сучасний clipboard API з fallback
        const copy = (text) => {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text);
            }
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            try { document.execCommand('copy'); } catch (_) {}
            document.body.removeChild(ta);
            return Promise.resolve();
        };

        copy(ip).then(() => {
            btn.classList.add('copied');
            const code = btn.querySelector('code');
            const original = code.textContent;
            code.textContent = 'Скопійовано!';
            setTimeout(() => {
                btn.classList.remove('copied');
                code.textContent = original;
            }, 1200);
        });
    });
})();
/* ═══════════════════════════════════════════════════════════════
   МОДАЛКА СПИСКУ ГРАВЦІВ — клік на "X / Y" відкриває модалку
   з даними з /api/server/{id}/players (A2S_PLAYER через VPS API).
   Додати в кінець app.js.
   ═══════════════════════════════════════════════════════════════ */

(function () {
    const modal      = document.getElementById('players-modal');
    if (!modal) return;

    const titleEl    = document.getElementById('players-modal-title');
    const subtitleEl = document.getElementById('players-modal-server');
    const bodyEl     = document.getElementById('players-modal-body');
    const closeBtn   = modal.querySelector('.players-modal-close');

    let lastFocused = null;

    function open(serverId, serverName) {
        lastFocused = document.activeElement;
        titleEl.textContent    = 'Гравці на сервері';
        subtitleEl.textContent = serverName || '';
        bodyEl.innerHTML       = '<div class="players-modal-loader">Завантаження...</div>';

        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        closeBtn.focus();

        fetch('/api/server/' + encodeURIComponent(serverId) + '/players', {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
        })
            .then(r => r.ok ? r.json() : Promise.reject(new Error('HTTP ' + r.status)))
            .then(render)
            .catch(err => {
                bodyEl.innerHTML =
                    '<div class="players-modal-error">Не вдалося завантажити список: ' +
                    escapeHtml(err.message) + '</div>';
            });
    }

    function close() {
        modal.hidden = true;
        document.body.style.overflow = '';
        if (lastFocused && typeof lastFocused.focus === 'function') lastFocused.focus();
    }

    function render(data) {
        if (!data || data.success === false) {
            bodyEl.innerHTML =
                '<div class="players-modal-error">' +
                escapeHtml((data && data.error) || 'Помилка завантаження') +
                '</div>';
            return;
        }
        if (!data.is_online) {
            bodyEl.innerHTML =
                '<div class="players-modal-empty">Сервер офлайн</div>';
            return;
        }
        if (!data.players || data.players.length === 0) {
            bodyEl.innerHTML =
                '<div class="players-modal-empty">На сервері зараз нікого немає</div>';
            return;
        }

        // Оновлюємо заголовок
        titleEl.textContent = 'Гравці на сервері (' + data.count + ')';
        if (data.server && data.server.name) {
            subtitleEl.textContent = data.server.name +
                (data.server.ip ? ' — ' + data.server.ip : '');
        }

        let html = ''
            + '<table class="pm-players">'
            +   '<thead><tr>'
            +     '<th class="pm-num">#</th>'
            +     '<th>Гравець</th>'
            +     '<th class="pm-frags">Фраги</th>'
            +     '<th class="pm-time">Час</th>'
            +   '</tr></thead>'
            +   '<tbody>';

        data.players.forEach((p, i) => {
            const vipBadge = p.is_vip
                ? '<span class="pm-vip-badge">VIP</span>'
                : '';
            html += ''
                + '<tr>'
                +   '<td class="pm-num">' + (i + 1) + '</td>'
                +   '<td>'
                +     '<div class="pm-nick-wrap">'
                +       '<span class="pm-nick">' + escapeHtml(p.name) + '</span>'
                +       vipBadge
                +     '</div>'
                +   '</td>'
                +   '<td class="pm-frags">' + (p.frags | 0) + '</td>'
                +   '<td class="pm-time">'  + formatTime(p.time_seconds) + '</td>'
                + '</tr>';
        });

        html += '</tbody></table>';
        bodyEl.innerHTML = html;
    }

    function formatTime(seconds) {
        const s = parseInt(seconds || 0, 10);
        if (s < 60)   return s + ' с';
        if (s < 3600) return Math.floor(s / 60) + ' хв';
        const h = Math.floor(s / 3600);
        const m = Math.floor((s % 3600) / 60);
        return h + ' г ' + m + ' хв';
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // === Events ===

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-players-server]');
        if (!btn) return;
        e.preventDefault();
        open(btn.dataset.playersServer, btn.dataset.serverName);
    });

    closeBtn.addEventListener('click', close);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) close();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.hidden) close();
    });
})();
