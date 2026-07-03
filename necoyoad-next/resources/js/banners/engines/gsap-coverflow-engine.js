/**
 * GSAP Coverflow Engine — 3D coverflow transition.
 * Center slide scaled up, side slides tilted + scaled down via GSAP.
 */
export default async function initGsapCoverflow(el, config, slides, bannerId, eventBus) {
    let gsap;
    try {
        gsap = (await import('gsap')).default;
    } catch (e) {
        console.warn('[gsap-coverflow] GSAP not available, falling back to Swiper');
        const swiperModule = await import('./swiper-engine.js');
        return swiperModule.default(el, config, slides, bannerId, eventBus);
    }

    el.innerHTML = `
        <div class="gsap-coverflow-container" style="perspective:1200px;width:100%;height:400px;overflow:hidden;border-radius:8px;position:relative;display:flex;align-items:center;justify-content:center;">
            ${slides.map((slide, i) => `<div class="coverflow-slide" data-index="${i}" data-slide-id="${slide.id}" style="
                position:absolute;width:70%;height:100%;
                background-size:cover;background-position:center;
                background-image:url('/storage/${slide.image || ''}');
                border-radius:8px;box-shadow:0 10px 30px rgba(0,0,0,0.3);
                transform-style:preserve-3d;
            "><div style="position:absolute;bottom:20px;left:20px;color:white;text-shadow:2px 2px 4px rgba(0,0,0,0.7);font-size:1.5rem;font-weight:700;">${slide.title || ''}</div></div>`).join('')}
        </div>
    `;

    const slideEls = el.querySelectorAll('.coverflow-slide');
    let current = 0;

    function update() {
        slideEls.forEach((slideEl, i) => {
            const offset = i - current;
            const absOffset = Math.abs(offset);
            const zIndex = 100 - absOffset;
            gsap.to(slideEl, {
                x: offset * 60 + '%',
                rotationY: offset * -45,
                scale: absOffset === 0 ? 1 : 0.8,
                opacity: absOffset > 2 ? 0 : 1 - absOffset * 0.3,
                zIndex: zIndex,
                duration: (config.transition_speed || 800) / 1000,
                ease: 'power2.out',
            });
        });
        const slide = slides[current];
        eventBus.emit('slideChanged', { bannerId, slideIndex: current, slideId: slide?.id });
        eventBus.dispatchSlideChanged(bannerId, current, slide?.id, 'next');
    }

    update();

    let autoplayTimer;
    if (config.autoplay !== false) {
        autoplayTimer = setInterval(() => {
            current = (current + 1) % slides.length;
            update();
        }, config.autoplay_speed || 5000);
    }

    el.addEventListener('click', () => {
        current = (current + 1) % slides.length;
        update();
        eventBus.dispatchInteraction(bannerId, 'click', slides[current]?.id, slides[current]?.link);
    });

    return { destroy: () => clearInterval(autoplayTimer) };
}
