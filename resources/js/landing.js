async function switchLanguage(lang) {
    localStorage.setItem('preferredLanguage', lang);
    document.body.classList.add('language-switching');

    const localizedUrl = lang === 'ar' ? '/ar' : '/';

    try {
        const response = await fetch(localizedUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!response.ok) throw new Error('Language request failed');

        const localizedPage = new DOMParser().parseFromString(await response.text(), 'text/html');
        document.documentElement.lang = localizedPage.documentElement.lang;
        document.documentElement.dir = localizedPage.documentElement.dir || (lang === 'ar' ? 'rtl' : 'ltr');
        document.documentElement.classList.toggle('rtl', lang === 'ar');
        document.title = localizedPage.title;
        document.body.className = `${localizedPage.body.className} language-switching`;
        document.body.innerHTML = localizedPage.body.innerHTML;
        window.history.replaceState({}, '', localizedUrl);

        initializeLanguageSwitcher();
        initializeSmoothScrolling();
        initializeMobileMenu();

        requestAnimationFrame(() => {
            requestAnimationFrame(() => document.body.classList.remove('language-switching'));
        });
    } catch (error) {
        window.location.href = localizedUrl;
    }
}

function initializeLanguageSwitcher() {
    const toggles = [
        document.getElementById('lang-toggle-checkbox'),
        document.getElementById('mobile-lang-toggle-checkbox'),
        document.getElementById('lang-toggle-footer-checkbox')
    ].filter(Boolean);

    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            toggles.forEach(otherToggle => {
                otherToggle.checked = this.checked;
            });
        });
    });
}

function initializeSmoothScrolling() {
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

function initializeMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    const menuButton = document.querySelector('.mobile-menu-button');
    const closeButtons = document.querySelectorAll('[data-mobile-menu-close]');

    if (!menu || !menuButton) return;

    const setMenuState = (isOpen) => {
        menu.classList.toggle('is-open', isOpen);
        document.querySelector('.mobile-menu-backdrop')?.classList.toggle('is-open', isOpen);
        document.body.classList.toggle('mobile-menu-open', isOpen);
        menuButton.setAttribute('aria-expanded', String(isOpen));
        menu.setAttribute('aria-hidden', String(!isOpen));
    };

    menuButton.addEventListener('click', () => setMenuState(true));
    closeButtons.forEach(button => button.addEventListener('click', () => setMenuState(false)));
    menu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => setMenuState(false)));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') setMenuState(false);
    });
}

// Expose functions to global window object for inline onchange handlers
window.switchLanguage = switchLanguage;
window.initializeLanguageSwitcher = initializeLanguageSwitcher;
window.initializeSmoothScrolling = initializeSmoothScrolling;
window.initializeMobileMenu = initializeMobileMenu;

document.addEventListener('DOMContentLoaded', function() {
    initializeLanguageSwitcher();
    initializeSmoothScrolling();
    initializeMobileMenu();
});
