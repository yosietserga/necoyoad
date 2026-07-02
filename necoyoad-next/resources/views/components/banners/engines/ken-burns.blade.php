{{--
    Ken Burns Engine — cinematic slow pan + zoom on static images.
    Uses GSAP for smooth animation. Optional background audio per slide.
    Loaded via banner-loader.js → engines/ken-burns-engine.js
--}}
@include('components.banners.wrapper', ['engine' => 'ken-burns', 'config' => $config, 'slides' => $slides, 'banner' => $banner])
