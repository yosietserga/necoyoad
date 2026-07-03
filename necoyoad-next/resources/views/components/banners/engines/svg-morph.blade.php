{{--
    SVG Morph Engine — shape transition via GSAP MorphSVG.
    One slide's SVG shape morphs into the next (vector, resolution-independent).
    Loaded via banner-loader.js → engines/svg-morph-engine.js
--}}
@include('components.banners.wrapper', ['engine' => 'svg-morph', 'config' => $config, 'slides' => $slides, 'banner' => $banner])
