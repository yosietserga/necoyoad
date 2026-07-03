/**
 * GSAP Flip Engine — 3D flip card transition.
 * Slide flips on Y axis revealing next slide via GSAP + CSS 3D transforms.
 */
export default async function initGsapFlip(el, config, slides, bannerId, eventBus) {
    let gsap;
    try {
        gsap = (await import('gsap')).default;
    } catch (e) {
        console.warn('[gsap-flip] GSAP not available, falling back to Swiper');
        const swiperModule = await import('./swiper-engine.js');
        return swiperModule.default(el, config, slides, bannerId, eventBus);
    }

    el.innerHTML = `
        <div class="gsap-flip-container" style="perspective:1200px;width:100%;height:400px;overflow:hidden;border-radius:8px;position:relative;">
            ${slides.map((slide, i) => `<div class="flip-card" data-index="${i}" data-slide-id="${slide.id}" style="
                position:absolute;width:100%;height:100%;
                backface-visibility:hidden;
                background-size:cover;background-position:center;
                background-image:url('/storage/${slide.image || ''}');
                ${i > 0 ? 'opacity:0;' : ''}
            "><div style="position:absolute;bottom:20px;left:20px;color:white;text-shadow:2px 2px 4px rgba(0,0,0,0.7);font-size:1.5rem;font-weight:700;">${slide.title || ''}</div></div>`).join('')}
        </div>
    `;

    const cards = el.querySelectorAll('.flip-card');
    let current = 0;

    function flipTo(nextIndex) {
        if (nextIndex === current) return;
        const currentCard = cards[current];
        const nextCard = cards[nextIndex];

        gsap.to(currentCard, {
            rotationY: 180,
            opacity: 0,
            duration: (config.transition_speed || 800) / 1000,
            ease: 'power2.inOut',
            onComplete: () => gsap.set(currentCard, { rotationY: 0 }),
        });

        gsap.fromTo(nextCard,
            { rotationY: -180, opacity: 0 },
            { rotationY: 0, opacity: 1, duration: (config.transition_speed || 800) / 1000, ease: 'power2.inOut' }
        );

        current = nextIndex;
        const slide = slides[current];
        eventBus.emit('slideChanged', { bannerId, slideIndex: current, slideId: slide?.id });
        eventBus.dispatchSlideChanged(bannerId, current, slide?.id, 'next');
    }

    let autoplayTimer;
    if (config.autoplay !== false) {
        autoplayTimer = setInterval(() => {
            flipTo((current + 1) % slides.length);
        }, config.autoplay_speed || 5000);
    }

    el.addEventListener('click', () => {
        flipTo((current + 1) % slides.length);
        eventBus.dispatchInteraction(bannerId, 'click', slides[current]?.id, slides[current]?.link);
    });

    return { destroy: () => clearInterval(autoplayTimer) };
}
