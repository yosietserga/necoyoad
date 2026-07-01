<?php

declare(strict_types=1);

describe('Storefront', function () {
    it('can access home page', function () {
        $response = $this->get('/');
        $response->assertStatus(200);
    });

    it('can access search page', function () {
        $response = $this->get('/search?q=test');
        $response->assertStatus(200);
    });
});
