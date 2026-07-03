/**
 * Canvas Particles Engine — particle dissolve transition.
 * Slide breaks into thousands of particles that fly/dissolve via Canvas 2D.
 */
export default async function initCanvasParticles(el, config, slides, bannerId, eventBus) {
    const width = el.clientWidth || 800;
    const height = 400;

    el.innerHTML = `<canvas class="particle-canvas" style="width:100%;height:400px;border-radius:8px;display:block;"></canvas>`;
    const canvas = el.querySelector('.particle-canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');

    const particleCount = 3000;
    let particles = [];
    let currentImageIndex = 0;

    // Load images
    const images = await Promise.all(
        slides.filter(s => s.image).map(s => new Promise((resolve) => {
            const img = new Image();
            img.onload = () => resolve(img);
            img.onerror = () => resolve(null);
            img.src = '/storage/' + s.image;
        }))
    );

    if (images.length === 0 || images[0] === null) {
        console.warn('[canvas-particles] No images, falling back to Swiper');
        const swiperModule = await import('./swiper-engine.js');
        return swiperModule.default(el, config, slides, bannerId, eventBus);
    }

    function createParticles(img) {
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = width;
        tempCanvas.height = height;
        const tempCtx = tempCanvas.getContext('2d');
        tempCtx.drawImage(img, 0, 0, width, height);
        const imageData = tempCtx.getImageData(0, 0, width, height).data;

        particles = [];
        for (let i = 0; i < particleCount; i++) {
            const x = Math.random() * width;
            const y = Math.random() * height;
            const pixelIndex = (Math.floor(y) * width + Math.floor(x)) * 4;
            particles.push({
                x, y,
                originX: x, originY: y,
                targetX: x, targetY: y,
                r: imageData[pixelIndex],
                g: imageData[pixelIndex + 1],
                b: imageData[pixelIndex + 2],
                size: 2,
                vx: 0, vy: 0,
            });
        }
    }

    function drawParticles() {
        ctx.fillStyle = '#000';
        ctx.fillRect(0, 0, width, height);
        particles.forEach(p => {
            ctx.fillStyle = `rgb(${p.r},${p.g},${p.b})`;
            ctx.fillRect(p.x, p.y, p.size, p.size);
        });
    }

    function dissolveTo(nextIndex) {
        const nextImg = images[nextIndex];
        if (!nextImg) return;

        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = width;
        tempCanvas.height = height;
        const tempCtx = tempCanvas.getContext('2d');
        tempCtx.drawImage(nextImg, 0, 0, width, height);
        const imageData = tempCtx.getImageData(0, 0, width, height).data;

        particles.forEach((p, i) => {
            const x = Math.random() * width;
            const y = Math.random() * height;
            const pixelIndex = (Math.floor(y) * width + Math.floor(x)) * 4;
            p.targetX = x;
            p.targetY = y;
            p.r = imageData[pixelIndex];
            p.g = imageData[pixelIndex + 1];
            p.b = imageData[pixelIndex + 2];
            p.vx = (Math.random() - 0.5) * 20;
            p.vy = (Math.random() - 0.5) * 20;
        });

        const duration = config.transition_speed || 1500;
        const startTime = performance.now();

        function animate() {
            const elapsed = performance.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);

            particles.forEach(p => {
                if (progress < 0.5) {
                    // Explode outward
                    p.x += p.vx * (1 - progress * 2);
                    p.y += p.vy * (1 - progress * 2);
                } else {
                    // Converge to new target
                    p.x += (p.targetX - p.x) * 0.1;
                    p.y += (p.targetY - p.y) * 0.1;
                }
            });

            drawParticles();

            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                currentImageIndex = nextIndex;
                const slide = slides[currentImageIndex];
                eventBus.emit('slideChanged', { bannerId, slideIndex: currentImageIndex, slideId: slide?.id });
                eventBus.dispatchSlideChanged(bannerId, currentImageIndex, slide?.id, 'next');
            }
        }
        animate();
    }

    // Initialize with first image
    createParticles(images[0]);
    drawParticles();

    let autoplayTimer;
    if (config.autoplay !== false) {
        autoplayTimer = setInterval(() => {
            dissolveTo((currentImageIndex + 1) % images.length);
        }, config.autoplay_speed || 5000);
    }

    el.addEventListener('click', () => {
        dissolveTo((currentImageIndex + 1) % images.length);
        eventBus.dispatchInteraction(bannerId, 'click', slides[currentImageIndex]?.id, slides[currentImageIndex]?.link);
    });

    return { destroy: () => clearInterval(autoplayTimer) };
}
