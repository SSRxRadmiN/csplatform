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
