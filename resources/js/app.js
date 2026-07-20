

import Alpine from 'alpinejs';
import './firebase-messaging.js';

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
const spinnerSvg = `<svg style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:8px;animation:ge-spin 1s linear infinite;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle style="opacity:.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity:.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

// Global overlay element
let overlayEl = null;

function showOverlay() {
    if (overlayEl) return;
    overlayEl = document.createElement('div');
    overlayEl.id = 'global-loading-overlay';
    overlayEl.style.cssText = 'position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.15);display:flex;align-items:center;justify-content:center;pointer-events:none;backdrop-filter:blur(2px);';
    overlayEl.innerHTML = `<div style="background:white;border-radius:12px;padding:20px 28px;box-shadow:0 8px 30px rgba(0,0,0,0.12);display:flex;align-items:center;gap:12px;"><svg style="width:24px;height:24px;color:#16a34a;animation:ge-spin 1s linear infinite;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle style="opacity:.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity:.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span style="font-size:14px;font-weight:600;color:#1f2937;">Chargement...</span></div>`;
    document.body.appendChild(overlayEl);
}

function hideOverlay() {
    if (overlayEl) {
        overlayEl.remove();
        overlayEl = null;
    }
}

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

// Attach spinner to all submit buttons in a form
function attachFormSpinner(form) {
    if (form.dataset.spinnerAttached) return;
    form.dataset.spinnerAttached = 'true';

    const submitBtn = form.querySelector('button[type="submit"]');
    if (!submitBtn) return;

    form.addEventListener('submit', () => {
        setLoading(submitBtn, submitBtn.dataset.loadingText || 'Envoi...');
        showOverlay();
    });
}

// Attach spinner to clickable elements (links with data-loading, action buttons)
function attachClickSpinner(el) {
    if (el.dataset.spinnerAttached) return;
    el.dataset.spinnerAttached = 'true';

    el.addEventListener('click', (e) => {
        if (el.dataset.loading === 'true') return;
        if (el.tagName.toLowerCase() === 'a') {
            const href = el.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript:')) return;
            if (el.target === '_blank') return;
            setLoading(el, el.dataset.loadingText || 'Chargement...');
            showOverlay();
        } else if (el.type !== 'submit') {
            setLoading(el, el.dataset.loadingText || 'Chargement...');
        }
    });
}

// Initialize all spinners
function initLoadingSpinners() {
    // All forms
    document.querySelectorAll('form').forEach(attachFormSpinner);

    // Links with data-loading
    document.querySelectorAll('a[data-loading]').forEach(attachClickSpinner);

    // Action buttons (PATCH/DELETE forms often use inline buttons that submit forms)
    document.querySelectorAll('button[data-loading]').forEach(attachClickSpinner);

    // Navigation links: show overlay on page navigation (non-trivial links)
    document.querySelectorAll('a[href^="/"]:not([target="_blank"]):not([data-no-spinner])').forEach((link) => {
        if (link.dataset.navSpinnerAttached) return;
        link.dataset.navSpinnerAttached = 'true';
        const href = link.getAttribute('href');
        if (!href || href === '/' && window.location.pathname === '/') return;

        link.addEventListener('click', () => {
            // Skip if it's an Alpine.js handled click (has x-data or @click without real navigation)
            if (link.closest('[x-data]') && !link.getAttribute('href').startsWith('/')) return;
            // Only show overlay for real navigation
            if (link.getAttribute('href').startsWith('#')) return;
            showOverlay();
        });
    });
}

// Initialize on DOM ready or immediately
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLoadingSpinners);
} else {
    initLoadingSpinners();
}

// Watch for dynamically added forms/buttons (Alpine.js components, modals, etc.)
const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
            if (node.nodeType !== 1) return;
            if (node.tagName === 'FORM') {
                attachFormSpinner(node);
            }
            if ((node.tagName === 'A' || node.tagName === 'BUTTON') && node.hasAttribute('data-loading')) {
                attachClickSpinner(node);
            }
            node.querySelectorAll?.('form').forEach(attachFormSpinner);
            node.querySelectorAll?.('a[data-loading], button[data-loading]').forEach(attachClickSpinner);
        });
    });
});
observer.observe(document.body, { childList: true, subtree: true });

// Restore loading buttons and hide overlay on page show (back/forward cache)
window.addEventListener('pageshow', () => {
    document.querySelectorAll('[data-loading="true"]').forEach(restoreElement);
    hideOverlay();
});

// Hide overlay if page fails to load or takes too long (fallback)
window.addEventListener('error', hideOverlay);
setTimeout(() => { if (document.readyState === 'complete') hideOverlay(); }, 10000);

// Top progress bar (style GitHub/YouTube)
let progressBar = null;
let progressTimer = null;

function showProgress() {
    if (progressBar) return;
    progressBar = document.createElement('div');
    progressBar.id = 'nav-progress-bar';
    progressBar.style.cssText = 'position:fixed;top:0;left:0;height:3px;width:0%;z-index:10001;background:linear-gradient(90deg,#16a34a,#22c55e);box-shadow:0 0 8px rgba(34,197,94,0.5);transition:width 0.3s ease-out;border-radius:0 2px 2px 0;';
    document.body.appendChild(progressBar);

    // Animate to ~80% gradually
    let width = 0;
    progressTimer = setInterval(() => {
        if (width < 80) {
            width += Math.random() * 15;
            progressBar.style.width = Math.min(width, 80) + '%';
        }
    }, 200);
}

function completeProgress() {
    if (!progressBar) return;
    clearInterval(progressTimer);
    progressBar.style.width = '100%';
    setTimeout(() => {
        if (progressBar) {
            progressBar.remove();
            progressBar = null;
        }
    }, 300);
}

// Show progress bar on any navigation
document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href]');
    if (!link) return;
    const href = link.getAttribute('href');
    if (!href || href === '#' || href.startsWith('javascript:') || link.target === '_blank') return;
    if (href.startsWith('http') && !href.startsWith(window.location.origin)) return;
    if (link.hasAttribute('data-no-spinner')) return;
    showProgress();
});

// Show progress bar on form submissions
document.addEventListener('submit', () => showProgress());

// Complete progress when page loads
window.addEventListener('load', completeProgress);
window.addEventListener('pageshow', completeProgress);
