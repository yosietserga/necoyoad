/**
 * Ken Burns Engine — cinematic slow pan + zoom on static images.
 * Uses GSAP for smooth animation. No transitions between slides — each
 * slide gets its own Ken Burns effect, then crossfades to the next.
 */
export default async function initKenBurns(el, config, slides, bannerId, eventBus) {
    let gsap;
    try {
        gsap = (await import('gsap')).default;
    } catch (e) {
        console.warn('[ken-burns] GSAP not available, falling back to Swiper');
        const swiperModule = await import('./swiper-engine.js');
        return swiperModule.default(el, config, slides, bannerId, eventBus);
    }

    const intensity = (config.ken_burns_intensity || 50) / 100;

    el.innerHTML = `
        <div class="ken-burns-container" style="width:100%;height:400px;overflow:hidden;border-radius:8px;position:relative;">
            ${slides.map((slide, i) => `<div class="kb-slide" data-index="${i}" data-slide-id="${slide.id}" style="
                position:absolute;width:100%;height:100%;
                background-size:cover;background-position:center;
                background-image:url('/storage/${slide.image || ''}');
                ${i > 0 ? 'opacity:0;' : ''}
            "><div style="position:absolute;bottom:20px;left:20px;color:white;text-shadow:2px 2px 4px rgba(0,0,0,0.7);font-size:1.5rem;font-weight:700;">${slide.title || ''}</div></div>`).join('')}
        </div>
    `;

    const slideEls = el.querySelectorAll('.kb-slide');
    let current = 0;

    function applyKenBurns(slideEl) {
        const directions = [
            { scale: 1 + intensity * 0.3, x: '0%', y: '0%' },           // zoom in
            { scale: 1 + intensity * 0.3, x: '-5%', y: '-5%' },          // zoom + pan
            { scale: 1 + intensity * 0.3, x: '5%', y: '-5%' },           // zoom + pan right
            { scale: 1 + intensity * 0.2, x: '-3%', y: '3%' },           // subtle pan
        ];
        const dir = directions[Math.floor(Math.random() * directions.length)];
        gsap.fromTo(slideEl,
            { scale: 1, x: '0%', y: '0%' },
            { ...dir, duration: (config.autoplay_speed || 5000) / 1000, ease: 'none' }
        );
    }

    function crossfadeTo(nextIndex) {
        const currentEl = slideEls[current];
        const nextEl = slideEls[nextIndex];

        gsap.to(currentEl, { opacity: 0, duration: 1.5, ease: 'power2.inOut' });
        gsap.fromTo(nextEl, { opacity: 0 }, { opacity: 1, duration: 1.5, ease: 'power2.inOut' });

        applyKenBurns(nextEl);
        current = nextIndex;

        const slide = slides[current];
        eventBus.emit('slideChanged', { bannerId, slideIndex: current, slideId: slide?.id });
        eventBus.dispatchSlideChanged(bannerId, current, slide?.id, 'next');
    }

    // Apply Ken Burns to first slide
    applyKenBurns(slideEls[0]);

    let autoplayTimer;
    if (config.autoplay !== false) {
        autoplayTimer = setInterval(() => {
            crossfadeTo((current + 1) % slides.length);
        }, config.autoplay_speed || 5000);
    }

    el.addEventListener('click', () => {
        crossfadeTo((current + 1) % slides.length);
        eventBus.dispatchInteraction(bannerId, 'click', slides[current]?.id, slides[current]?.link);
    });

    return { destroy: () => clearInterval(autoplayTimer) };
}
