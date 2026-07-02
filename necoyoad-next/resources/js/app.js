// New Necoyoad — Front-end JavaScript entry
// Alpine.js + Livewire 3 + ntPlugins equivalent + Browser Audit Logger

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import './audit-logger.js';

// ntPlugins equivalent: Alpine store for slider/component configs
Alpine.store('ntPlugins', []);

// Register ntPlugins loader (called by slider templates after DOM ready)
window.loadNTPlugins = function() {
    const plugins = Alpine.store('ntPlugins');
    if (!plugins) return;

    plugins.forEach((item, index) => {
        if (!item) return;

        const el = document.querySelector(item.id);
        if (el && (window.jQuery && (jQuery[item.plugin] || jQuery.fn[item.plugin]))) {
            jQuery(el)[item.plugin](item.config || {});
        }

        Alpine.store('ntPlugins')[index] = null;
    });
};

// Context object for front-end JS (equivalent to window.nt from original)
window.ntContext = {
    get sid() { return document.querySelector('meta[name="store-id"]')?.content || 0; },
    get httpHome() { return window.location.origin + '/'; },
    get isMobile() {
        return /android|iphone|ipod|ipad|windows phone|blackberry|mobile/i.test(navigator.userAgent);
    }
};

Alpine.plugin(focus);
window.Alpine = Alpine;
Alpine.start();
