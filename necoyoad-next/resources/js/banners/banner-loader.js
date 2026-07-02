/**
 * Necoyoad Banner Loader
 *
 * Finds all [data-banner-engine] elements on the page and dynamically imports
 * the correct engine JS module. Each engine initializes the banner, sets up
 * slide transitions, and wires the event bus to dispatch backend events.
 *
 * Engines are lazy-loaded via dynamic import() so only the needed engine
 * JS is downloaded (e.g., Three.js only loads if a three-distort banner exists).
 */

// Frontend event bus — dispatches to backend via POST /api/banner/event
const BannerEventBus = {
    async dispatchSlideChanged(bannerId, slideIndex, slideId, direction) {
        try {
            await fetch('/api/banner/event/slide-changed', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    banner_id: bannerId,
                    slide_index: slideIndex,
                    slide_id: slideId,
                    direction: direction,
                }),
                keepalive: true,
            });
        } catch (e) {
            // Silent fail — don't block the UI for analytics
            if (window.__necoyoadAudit) {
                window.__necoyoadAudit.queue({
                    type: 'banner_event_dispatch_error',
                    message: 'Failed to dispatch slide-changed: ' + e.message,
                });
            }
        }
    },

    async dispatchInteraction(bannerId, interactionType, slideId, linkUrl) {
        try {
            await fetch('/api/banner/event/interaction', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    banner_id: bannerId,
                    interaction_type: interactionType,
                    slide_id: slideId,
                    link_url: linkUrl,
                }),
                keepalive: true,
            });
        } catch (e) {
            if (window.__necoyoadAudit) {
                window.__necoyoadAudit.queue({
                    type: 'banner_event_dispatch_error',
                    message: 'Failed to dispatch interaction: ' + e.message,
                });
            }
        }
    },
};

// Alpine.js store for cross-widget banner state synchronization
// Other widgets can listen: Alpine.store('bannerBus').on('slideChanged', fn)
document.addEventListener('alpine:init', () => {
    if (!window.Alpine) return;
    Alpine.store('bannerBus', {
        listeners: {},
        on(event, callback) {
            if (!this.listeners[event]) this.listeners[event] = [];
            this.listeners[event].push(callback);
        },
        emit(event, data) {
            (this.listeners[event] || []).forEach((cb) => {
                try { cb(data); } catch (e) { console.error('Banner listener error:', e); }
            });
        },
    });
});

window.BannerEventBus = BannerEventBus;

// Main loader — runs on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    const banners = document.querySelectorAll('[data-banner-engine]');
    banners.forEach(async (el) => {
        const engine = el.dataset.bannerEngine;
        const config = JSON.parse(el.dataset.bannerConfig || '{}');
        const slides = JSON.parse(el.dataset.bannerSlides || '[]');
        const bannerId = parseInt(el.dataset.bannerId);

        try {
            // Dynamic import — only the needed engine JS downloads
            const module = await import(`./engines/${engine}-engine.js`);
            await module.default(el, config, slides, bannerId, BannerEventBus);
        } catch (e) {
            console.error(`Banner engine '${engine}' failed to load:`, e);
            if (window.__necoyoadAudit) {
                window.__necoyoadAudit.queue({
                    type: 'banner_engine_error',
                    message: `Failed to load banner engine '${engine}': ${e.message}`,
                });
            }
            // Fallback: render slides as a simple list (no silent failure)
            el.innerHTML = slides.map(s =>
                `<div class="banner-fallback"><img src="/storage/${s.image}" alt="${s.title || ''}" style="width:100%;"></div>`
            ).join('');
        }
    });
});

export { BannerEventBus };
