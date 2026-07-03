{{--
    GSAP Cube Engine — 3D cube rotation transition.
    Slides map to cube faces, rotation on X/Y axis via GSAP + CSS 3D transforms.
    Loaded via banner-loader.js → engines/gsap-cube-engine.js
--}}
@include('components.banners.wrapper', ['engine' => 'gsap-cube', 'config' => $config, 'slides' => $slides, 'banner' => $banner])
