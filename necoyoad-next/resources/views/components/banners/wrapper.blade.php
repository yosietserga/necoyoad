{{--
    Banner Wrapper — common container for all banner engines.
    Emits data attributes that banner-loader.js reads to dynamically
    import the correct engine JS module.
--}}
@php
    $engine = $engine ?? 'swiper';
    $bannerId = $banner->id;
    $configJson = json_encode($config ?? []);
    $slidesJson = json_encode($slides ?? []);
@endphp
<div class="necoyoad-banner banner-engine-{{ str_replace('.', '-', $engine) }}"
     data-banner-id="{{ $bannerId }}"
     data-banner-engine="{{ $engine }}"
     data-banner-config="{{ htmlspecialchars($configJson, ENT_QUOTES, 'UTF-8') }}"
     data-banner-slides="{{ htmlspecialchars($slidesJson, ENT_QUOTES, 'UTF-8') }}"
     data-banner-name="{{ $banner->name ?? '' }}"
     nt-editable>
    <div class="banner-loading" style="min-height: 200px; display: flex; align-items: center; justify-content: center; background: #f0f0f0; color: #999;">
        Loading banner...
    </div>
</div>
