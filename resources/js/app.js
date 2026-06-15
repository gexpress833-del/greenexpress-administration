

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
