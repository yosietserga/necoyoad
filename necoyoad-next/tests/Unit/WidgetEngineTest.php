<?php

declare(strict_types=1);

describe('Widget Engine', function () {
    it('can instantiate WidgetService', function () {
        $service = app(\App\Services\WidgetService::class);
        expect($service)->toBeInstanceOf(\App\Services\WidgetService::class);
    });

    it('can instantiate StoreContext', function () {
        $context = app(\App\Services\StoreContext::class);
        expect($context)->toBeInstanceOf(\App\Services\StoreContext::class);
    });

    it('can instantiate LanguageContext', function () {
        $context = app(\App\Services\LanguageContext::class);
        expect($context)->toBeInstanceOf(\App\Services\LanguageContext::class);
    });

    it('can instantiate AssetManifest', function () {
        $manifest = app(\App\Services\AssetManifest::class);
        expect($manifest)->toBeInstanceOf(\App\Services\AssetManifest::class);
    });
});

describe('Models', function () {
    it('has Product model with correct traits', function () {
        $product = new \App\Models\Product();
        expect(method_exists($product, 'descriptions'))->toBeTrue();
        expect(method_exists($product, 'properties'))->toBeTrue();
        expect(method_exists($product, 'stores'))->toBeTrue();
        expect(method_exists($product, 'seoUrls'))->toBeTrue();
        expect(method_exists($product, 'categories'))->toBeTrue();
    });

    it('has Widget model with component_name accessor', function () {
        $widget = new \App\Models\Widget(['module' => 'banner']);
        expect($widget->component_name)->toBe('widgets.banner');
    });

    it('has Post model with scopePosts and scopePages', function () {
        expect(method_exists(\App\Models\Post::class, 'scopePosts'))->toBeTrue();
        expect(method_exists(\App\Models\Post::class, 'scopePages'))->toBeTrue();
    });
});

describe('Filters (Hooks System)', function () {
    it('can apply filters', function () {
        $pipeline = app('filter');

        $pipeline->addFilter('test', fn ($value) => $value . ' modified');

        expect($pipeline->apply('test', 'original'))->toBe('original modified');
    });

    it('can run actions with short-circuit', function () {
        $pipeline = app('filter');

        $pipeline->addAction('test-action', fn () => 'stopped');

        expect($pipeline->run('test-action'))->toBe('stopped');
    });
});
