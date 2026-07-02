

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Performance: start Alpine after DOM is fully ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => Alpine.start());
} else {
    Alpine.start();
}

// Performance: preload critical resources on hover
if ('IntersectionObserver' in window) {
    const preloadObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const link = entry.target;
                if (link.href && link.href.startsWith(window.location.origin)) {
                    const prefetch = document.createElement('link');
                    prefetch.rel = 'prefetch';
                    prefetch.href = link.href;
                    document.head.appendChild(prefetch);
                }
                preloadObserver.unobserve(link);
            }
        });
    }, { rootMargin: '200px' });

    document.querySelectorAll('a[href^="/"]').forEach((link) => {
        preloadObserver.observe(link);
    });
}

// Loading spinners for forms and async actions
const spinnerSvg = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

function setLoading(element, text = 'Chargement...') {
    if (!element || element.dataset.loading === 'true') return;
    element.dataset.loading = 'true';
    element.dataset.originalHtml = element.innerHTML;
    element.disabled = true;
    element.classList.add('opacity-75', 'cursor-not-allowed');
    element.innerHTML = spinnerSvg + ' <span class="align-middle">' + text + '</span>';
}

function restoreElement(element) {
    if (!element || element.dataset.loading !== 'true') return;
    element.innerHTML = element.dataset.originalHtml || element.innerHTML;
    element.disabled = false;
    element.classList.remove('opacity-75', 'cursor-not-allowed');
    element.dataset.loading = 'false';
}

// Forms: show spinner on submit
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLoadingSpinners);
} else {
    initLoadingSpinners();
}

function initLoadingSpinners() {
    document.querySelectorAll('form').forEach((form) => {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (!submitBtn) return;

        form.addEventListener('submit', () => {
            setLoading(submitBtn, submitBtn.dataset.loadingText || 'Envoi...');
        });
    });

    // Links/buttons that trigger data loading (add data-loading attribute)
    document.querySelectorAll('[data-loading]').forEach((el) => {
        el.addEventListener('click', (e) => {
            if (el.tagName.toLowerCase() === 'a' && el.href) {
                setLoading(el, el.dataset.loadingText || 'Chargement...');
            } else if (el.type !== 'submit') {
                setLoading(el, el.dataset.loadingText || 'Chargement...');
            }
        });
    });
}

// Restore loading buttons on page show (back/forward cache)
window.addEventListener('pageshow', () => {
    document.querySelectorAll('[data-loading="true"]').forEach(restoreElement);
});
