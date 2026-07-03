/**
 * SVG Morph Engine — shape transition via SVG path morphing.
 * One slide's SVG shape morphs into the next (vector, resolution-independent).
 * Uses flubber for path interpolation (loaded dynamically).
 */
export default async function initSvgMorph(el, config, slides, bannerId, eventBus) {
    let gsap, flubber;
    try {
        gsap = (await import('gsap')).default;
        flubber = await import('flubber');
    } catch (e) {
        console.warn('[svg-morph] GSAP or flubber not available, falling back to Swiper');
        const swiperModule = await import('./swiper-engine.js');
        return swiperModule.default(el, config, slides, bannerId, eventBus);
    }

    // Default SVG paths (circles) — in production these come from slide EAV
    const paths = slides.map((s, i) => {
        // Generate a circle path as default
        const r = 100;
        return `M${200 - r},${150} a${r},${r} 0 1,0 ${r * 2},0 a${r},${r} 0 1,0 ${-r * 2},0`;
    });

    el.innerHTML = `
        <div class="svg-morph-container" style="width:100%;height:400px;border-radius:8px;overflow:hidden;position:relative;background:linear-gradient(135deg,#1a365d,#2c5282);">
            <svg viewBox="0 0 400 300" style="width:100%;height:100%;">
                <path class="morph-path" d="${paths[0]}" fill="rgba(255,255,255,0.9)"/>
            </svg>
            ${slides.map((s, i) => `<div class="svg-morph-title" data-index="${i}" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:white;font-size:2rem;font-weight:700;${i > 0 ? 'opacity:0;' : ''}">${s.title || ''}</div>`).join('')}
        </div>
    `;

    const pathEl = el.querySelector('.morph-path');
    const titleEls = el.querySelectorAll('.svg-morph-title');
    let current = 0;

    function morphTo(nextIndex) {
        const interpolator = flubber.interpolate(paths[current], paths[nextIndex]);
        const obj = { t: 0 };

        gsap.to(obj, {
            t: 1,
            duration: (config.transition_speed || 800) / 1000,
            ease: 'power2.inOut',
            onUpdate: () => {
                pathEl.setAttribute('d', interpolator(obj.t));
            },
            onComplete: () => {
                current = nextIndex;
                titleEls.forEach((t, i) => gsap.to(t, { opacity: i === current ? 1 : 0, duration: 0.3 }));
                const slide = slides[current];
                eventBus.emit('slideChanged', { bannerId, slideIndex: current, slideId: slide?.id });
                eventBus.dispatchSlideChanged(bannerId, current, slide?.id, 'next');
            },
        });
    }

    let autoplayTimer;
    if (config.autoplay !== false) {
        autoplayTimer = setInterval(() => {
            morphTo((current + 1) % slides.length);
        }, config.autoplay_speed || 5000);
    }

    el.addEventListener('click', () => {
        morphTo((current + 1) % slides.length);
        eventBus.dispatchInteraction(bannerId, 'click', slides[current]?.id, slides[current]?.link);
    });

    return { destroy: () => clearInterval(autoplayTimer) };
}
