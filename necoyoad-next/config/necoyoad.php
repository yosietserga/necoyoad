<?php

/**
 * New Necoyoad — Default Configuration
 *
 * These are the per-entity template fallbacks (level 2 of the 3-level
 * TemplateResolver, v8). The active theme folder is level 1 (checked first).
 *
 * @see v8 (per-entity template override — 3-level resolution)
 */

return [

    // Template defaults (used when no per-entity override exists)
    'defaults' => [
        'home' => 'content.home',
        'product' => 'store.product',
        'category' => 'store.category',
        'post' => 'content.post',
        'page' => 'content.page',
        'post_all' => 'content.posts',
        'category_all' => 'store.categories',
        'product_all' => 'store.products',
        'search' => 'store.search',
    ],

    // Widget cache TTL (seconds)
    'widget_cache_ttl' => env('WIDGET_CACHE_TTL', 300),

    // Default theme
    'default_theme' => 'choroni',

    // Default language
    'default_language' => env('DEFAULT_LANGUAGE', 'en'),

    // Default currency
    'default_currency' => env('DEFAULT_CURRENCY', 'USD'),

    // Default store
    'default_store_id' => env('DEFAULT_STORE_ID', 0),

    // Available banner slider plugins (for Filament select)
    'banner_plugins' => [
        'nivo-slider' => 'NivoSlider',
        'slick' => 'Slick Carousel',
        'camera' => 'Camera Slideshow',
        'fancybox-gallery' => 'Fancybox Gallery',
        'grid-gallery' => 'CSS Grid Gallery',
    ],

    // Available widget positions
    'widget_positions' => [
        'featuredContent' => 'Featured Content',
        'main' => 'Main Content',
        'featuredFooter' => 'Featured Footer',
        'column_left' => 'Left Column',
        'column_right' => 'Right Column',
        'header' => 'Header',
        'footer' => 'Footer',
    ],
];
