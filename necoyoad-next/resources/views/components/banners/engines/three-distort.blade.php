{{--
    Three.js Distortion Engine — WebGL image distortion transition.
    Image dissolves/waves/liquid-distorts into next slide via GLSL shader.
    Falls back to fade on no-WebGL browsers.
    Loaded via banner-loader.js → engines/three-distort-engine.js
--}}
@include('components.banners.wrapper', ['engine' => 'three-distort', 'config' => $config, 'slides' => $slides, 'banner' => $banner])
