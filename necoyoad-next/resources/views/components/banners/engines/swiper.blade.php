{{--
    Swiper Engine — standard carousel/slider using Swiper 11.
    Default engine. Handles all standard slide + fade + coverflow transitions.
    Loaded via banner-loader.js → engines/swiper-engine.js
--}}
@include('components.banners.wrapper', ['engine' => 'swiper', 'config' => $config, 'slides' => $slides, 'banner' => $banner])
