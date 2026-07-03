/**
 * Swiper Engine — standard carousel/slider using Swiper 11.
 *
 * This is the default engine. Handles all standard slide + fade transitions.
 * Initializes Swiper on the banner element, wires slide-change events to
 * the BannerEventBus, and dispatches backend events.
 */
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade, EffectCoverflow, EffectCube, EffectFlip } from 'swiper/modules';

// Import Swiper CSS
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';
import 'swiper/css/effect-coverflow';
import 'swiper/css/effect-cube';
import 'swiper/css/effect-flip';

export default function initSwiper(el, config, slides, bannerId, eventBus) {
    // Build slide HTML
    const slidesHtml = slides.map((slide, index) => {
        const bgVideo = slide.background_type === 'video' && slide.background_video
            ? `<video src="${slide.background_video}" autoplay muted loop playsinline style="position:absolute;width:100%;height:100%;object-fit:cover;"></video>`
            : '';
        const bgGradient = slide.background_type === 'gradient' && slide.background_gradient
            ? `<div style="position:absolute;width:100%;height:100%;background:${slide.background_gradient};"></div>`
            : '';
        const img = slide.image
            ? `<img src="/storage/${slide.image}" alt="${slide.title || ''}" class="swiper-slide-image" style="width:100%;height:100%;object-fit:cover;" loading="lazy">`
            : '';
        const link = slide.link
            ? `<a href="${slide.link}" target="${slide.link_target}" class="swiper-slide-link" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:10;"></a>`
            : '';
        const title = slide.title
            ? `<div class="swiper-slide-title" style="position:absolute;bottom:20px;left:20px;color:white;text-shadow:2px 2px 4px rgba(0,0,0,0.7);font-size:1.5rem;font-weight:700;z-index:20;">${slide.title}</div>`
            : '';
        return `<div class="swiper-slide" data-slide-id="${slide.id}" data-slide-index="${index}">${bgVideo}${bgGradient}${img}${link}${title}</div>`;
    }).join('');

    el.innerHTML = `
        <div class="swiper necoyoad-swiper" style="width:100%;height:400px;overflow:hidden;border-radius:8px;">
            <div class="swiper-wrapper">${slidesHtml}</div>
            ${config.show_navigation ? '<div class="swiper-button-next"></div><div class="swiper-button-prev"></div>' : ''}
            ${config.show_pagination ? '<div class="swiper-pagination"></div>' : ''}
        </div>
    `;

    const swiperEl = el.querySelector('.swiper');

    const swiper = new Swiper(swiperEl, {
        modules: [Navigation, Pagination, Autoplay, EffectFade, EffectCoverflow, EffectCube, EffectFlip],
        slidesPerView: 1,
        spaceBetween: 0,
        loop: config.loop !== false,
        speed: config.transition_speed || 800,
        autoplay: config.autoplay !== false ? {
            delay: config.autoplay_speed || 5000,
            disableOnInteraction: false,
        } : false,
        effect: 'slide',
        navigation: config.show_navigation ? {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        } : false,
        pagination: config.show_pagination ? {
            el: '.swiper-pagination',
            clickable: true,
        } : false,
        grabCursor: true,
        keyboard: true,
        a11y: true,
    });

    // Wire slide-change events
    swiper.on('slideChange', () => {
        const activeIndex = swiper.realIndex;
        const activeSlide = slides[activeIndex];
        eventBus.emit('slideChanged', { bannerId, slideIndex: activeIndex, slideId: activeSlide?.id });
        eventBus.dispatchSlideChanged(bannerId, activeIndex, activeSlide?.id, 'next');
    });

    // Wire interaction events (click, swipe)
    swiperEl.addEventListener('click', (e) => {
        const activeIndex = swiper.realIndex;
        const activeSlide = slides[activeIndex];
        eventBus.emit('interaction', { bannerId, type: 'click', slideId: activeSlide?.id, linkUrl: activeSlide?.link });
        eventBus.dispatchInteraction(bannerId, 'click', activeSlide?.id, activeSlide?.link);
    });

    return swiper;
}
