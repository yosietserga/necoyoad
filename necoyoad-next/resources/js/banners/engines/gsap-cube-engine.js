/**
 * GSAP Cube Engine — 3D cube rotation transition via GSAP + CSS 3D transforms.
 *
 * Slides map to cube faces. Rotation on X/Y axis. GPU-accelerated.
 * Falls back to Swiper if GSAP isn't loaded yet (progressive enhancement).
 */
export default async function initGsapCube(el, config, slides, bannerId, eventBus) {
    let gsap;
    try {
        gsap = (await import('gsap')).default;
    } catch (e) {
        console.warn('[gsap-cube] GSAP not available, falling back to Swiper');
        const swiperModule = await import('./swiper-engine.js');
        return swiperModule.default(el, config, slides, bannerId, eventBus);
    }

    const slideCount = slides.length;
    const faceCount = Math.min(slideCount, 6); // Cube has 6 faces max

    el.innerHTML = `
        <div class="gsap-cube-container" style="perspective:1200px;width:100%;height:400px;overflow:hidden;border-radius:8px;position:relative;">
            <div class="gsap-cube" style="position:relative;width:100%;height:100%;transform-style:preserve-3d;transition:transform ${config.transition_speed || 800}ms ease;">
                ${slides.slice(0, 6).map((slide, i) => {
                    const rotation = i * 90;
                    return `<div class="cube-face" data-slide-id="${slide.id}" style="
                        position:absolute;width:100%;height:100%;
                        backface-visibility:hidden;
                        transform: rotateY(${rotation}deg) translateZ(50%);
                        background-size:cover;background-position:center;
                        background-image:url('/storage/${slide.image || ''}');
                    "><div style="position:absolute;bottom:20px;left:20px;color:white;text-shadow:2px 2px 4px rgba(0,0,0,0.7);font-size:1.5rem;font-weight:700;">${slide.title || ''}</div></div>`;
                }).join('')}
            </div>
        </div>
    `;

    let currentFace = 0;
    const cube = el.querySelector('.gsap-cube');

    function rotateTo(faceIndex) {
        const rotation = faceIndex * 90;
        gsap.to(cube, { rotationY: rotation, duration: (config.transition_speed || 800) / 1000, ease: 'power2.inOut' });
        const slide = slides[faceIndex];
        eventBus.emit('slideChanged', { bannerId, slideIndex: faceIndex, slideId: slide?.id });
        eventBus.dispatchSlideChanged(bannerId, faceIndex, slide?.id, 'next');
    }

    // Autoplay
    let autoplayTimer;
    if (config.autoplay !== false) {
        autoplayTimer = setInterval(() => {
            currentFace = (currentFace + 1) % faceCount;
            rotateTo(currentFace);
        }, config.autoplay_speed || 5000);
    }

    // Navigation clicks
    el.addEventListener('click', () => {
        currentFace = (currentFace + 1) % faceCount;
        rotateTo(currentFace);
        eventBus.dispatchInteraction(bannerId, 'click', slides[currentFace]?.id, slides[currentFace]?.link);
    });

    return { destroy: () => clearInterval(autoplayTimer) };
}
