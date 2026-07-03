{{--
    GSAP Flip Engine — 3D flip card transition.
    Slide flips on Y axis revealing next slide via GSAP + CSS 3D transforms.
    Loaded via banner-loader.js → engines/gsap-flip-engine.js
--}}
@include('components.banners.wrapper', ['engine' => 'gsap-flip', 'config' => $config, 'slides' => $slides, 'banner' => $banner])
