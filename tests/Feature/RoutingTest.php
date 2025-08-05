<?php

use Illuminate\Support\Uri;

test('public pages do not have cookies', function () {
    $response = $this->get(route('home'))
        ->assertSuccessful();

    expect($response->headers->getCookies())->toBeEmpty();
});

test('auth routes are on squawk subdomain', function () {
    $uri = Uri::route('login');

    expect($uri->host())->toStartWith('squawk.');
});
