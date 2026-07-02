{{--
    Canvas Particles Engine — particle dissolve transition.
    Slide breaks into thousands of particles that fly/dissolve via Canvas 2D.
    Loaded via banner-loader.js → engines/canvas-particles-engine.js
--}}
@include('components.banners.wrapper', ['engine' => 'canvas-particles', 'config' => $config, 'slides' => $slides, 'banner' => $banner])
