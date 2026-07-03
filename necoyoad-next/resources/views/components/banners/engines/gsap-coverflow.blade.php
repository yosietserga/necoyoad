{{--
    GSAP Coverflow Engine — 3D coverflow transition.
    Center slide scaled up, side slides tilted + scaled down via GSAP.
    Loaded via banner-loader.js → engines/gsap-coverflow-engine.js
--}}
@include('components.banners.wrapper', ['engine' => 'gsap-coverflow', 'config' => $config, 'slides' => $slides, 'banner' => $banner])
