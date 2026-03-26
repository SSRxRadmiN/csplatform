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
