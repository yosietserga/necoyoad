/**
 * Three.js Distortion Engine — WebGL image distortion transition.
 * Image dissolves/waves/liquid-distorts into next slide via GLSL shader.
 * Falls back to fade on no-WebGL browsers.
 */
export default async function initThreeDistort(el, config, slides, bannerId, eventBus) {
    let THREE;
    try {
        THREE = await import('three');
    } catch (e) {
        console.warn('[three-distort] Three.js not available, falling back to Swiper');
        const swiperModule = await import('./swiper-engine.js');
        return swiperModule.default(el, config, slides, bannerId, eventBus);
    }

    if (!window.WebGLRenderingContext) {
        console.warn('[three-distort] WebGL not supported, falling back to Swiper');
        const swiperModule = await import('./swiper-engine.js');
        return swiperModule.default(el, config, slides, bannerId, eventBus);
    }

    const width = el.clientWidth || 800;
    const height = 400;

    const scene = new THREE.Scene();
    const camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(width, height);
    el.innerHTML = '';
    el.appendChild(renderer.domElement);
    renderer.domElement.style.borderRadius = '8px';
    renderer.domElement.style.width = '100%';
    renderer.domElement.style.height = '400px';

    const loader = new THREE.TextureLoader();
    const textures = slides.filter(s => s.image).map(s => loader.load('/storage/' + s.image));

    if (textures.length === 0) {
        console.warn('[three-distort] No slide images, falling back to Swiper');
        const swiperModule = await import('./swiper-engine.js');
        return swiperModule.default(el, config, slides, bannerId, eventBus);
    }

    // GLSL shader: noise-based distortion transition between two textures
    const vertexShader = `
        varying vec2 vUv;
        void main() { vUv = uv; gl_Position = vec4(position, 1.0); }
    `;
    const fragmentShader = `
        uniform sampler2D texCurrent;
        uniform sampler2D texNext;
        uniform float progress;
        uniform float time;
        varying vec2 vUv;
        float random(vec2 st) {
            return fract(sin(dot(st.xy, vec2(12.9898,78.233))) * 43758.5453123);
        }
        void main() {
            vec2 uv = vUv;
            float noise = random(uv + time * 0.001) * 0.05;
            vec2 distortedUv = uv + vec2(sin(uv.y * 10.0 + time * 0.005) * 0.02 * progress, 0.0);
            vec4 colorCurrent = texture2D(texCurrent, distortedUv);
            vec4 colorNext = texture2D(texNext, distortedUv);
            float mixVal = smoothstep(0.0, 1.0, progress + noise);
            gl_FragColor = mix(colorCurrent, colorNext, mixVal);
        }
    `;

    let currentIndex = 0;
    const material = new THREE.ShaderMaterial({
        uniforms: {
            texCurrent: { value: textures[0] },
            texNext: { value: textures[0] },
            progress: { value: 0 },
            time: { value: 0 },
        },
        vertexShader,
        fragmentShader,
    });

    const geometry = new THREE.PlaneGeometry(2, 2);
    const mesh = new THREE.Mesh(geometry, material);
    scene.add(mesh);

    let transitionProgress = 0;
    let isTransitioning = false;

    function transitionTo(nextIndex) {
        if (isTransitioning) return;
        isTransitioning = true;
        transitionProgress = 0;
        material.uniforms.texCurrent.value = textures[currentIndex];
        material.uniforms.texNext.value = textures[nextIndex];
        currentIndex = nextIndex;

        const startTime = performance.now();
        const duration = config.transition_speed || 1200;

        function animate() {
            const elapsed = performance.now() - startTime;
            transitionProgress = Math.min(elapsed / duration, 1);
            material.uniforms.progress.value = transitionProgress;
            material.uniforms.time.value = elapsed;

            if (transitionProgress < 1) {
                requestAnimationFrame(animate);
            } else {
                isTransitioning = false;
                const slide = slides[currentIndex];
                eventBus.emit('slideChanged', { bannerId, slideIndex: currentIndex, slideId: slide?.id });
                eventBus.dispatchSlideChanged(bannerId, currentIndex, slide?.id, 'next');
            }
        }
        animate();
    }

    // Render loop
    function render(time) {
        material.uniforms.time.value = time;
        renderer.render(scene, camera);
        requestAnimationFrame(render);
    }
    render(0);

    let autoplayTimer;
    if (config.autoplay !== false) {
        autoplayTimer = setInterval(() => {
            transitionTo((currentIndex + 1) % textures.length);
        }, config.autoplay_speed || 5000);
    }

    el.addEventListener('click', () => {
        transitionTo((currentIndex + 1) % textures.length);
        eventBus.dispatchInteraction(bannerId, 'click', slides[currentIndex]?.id, slides[currentIndex]?.link);
    });

    return {
        destroy: () => {
            clearInterval(autoplayTimer);
            renderer.dispose();
            textures.forEach(t => t.dispose());
            geometry.dispose();
            material.dispose();
        },
    };
}
