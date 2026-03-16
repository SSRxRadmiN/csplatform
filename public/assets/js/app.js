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
