import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const prefetched = new Set();

function sameOrigin(url) {
    try {
        return new URL(url, window.location.href).origin === window.location.origin;
    } catch {
        return false;
    }
}

function shouldPrefetch(anchor) {
    if (!anchor || !anchor.href || anchor.target || anchor.hasAttribute('download')) return false;
    if (!sameOrigin(anchor.href)) return false;
    if (anchor.href.includes('#') || anchor.href === window.location.href) return false;
    return true;
}

function prefetch(anchor) {
    if (!shouldPrefetch(anchor) || prefetched.has(anchor.href)) return;
    prefetched.add(anchor.href);

    const link = document.createElement('link');
    link.rel = 'prefetch';
    link.href = anchor.href;
    link.as = 'document';
    document.head.appendChild(link);
}

document.addEventListener('mouseover', (event) => {
    const anchor = event.target.closest('a');
    if (anchor) prefetch(anchor);
}, { passive: true });

document.addEventListener('touchstart', (event) => {
    const anchor = event.target.closest('a');
    if (anchor) prefetch(anchor);
}, { passive: true });

document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || form.dataset.noBusy === 'true') return;

    form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
        button.disabled = true;
        if (button.tagName === 'BUTTON') {
            // Prepend a spinner icon to the button, keeping original text
            if (!button.querySelector('.fa-spinner')) {
                const spinner = document.createElement('i');
                spinner.className = 'fas fa-spinner fa-spin mr-2';
                button.insertBefore(spinner, button.firstChild);
            }
        }
    });
});

// Particle effect initialization
document.addEventListener('DOMContentLoaded', function() {
    // Create particle container if it doesn't exist
    if (!document.querySelector('.particles-container')) {
        const particlesContainer = document.createElement('div');
        particlesContainer.className = 'particles-container';
        document.body.appendChild(particlesContainer);
        
        // Create 30 particles
        for (let i = 0; i < 30; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 20 + 's';
            particle.style.animationDuration = (20 + Math.random() * 10) + 's';
            particlesContainer.appendChild(particle);
        }
    }
});
