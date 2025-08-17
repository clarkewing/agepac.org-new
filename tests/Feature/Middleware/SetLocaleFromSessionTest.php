<?php

use App\Http\Middleware\SetLocaleFromSession;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    config(['app.locale' => 'en']);

    Route::get('locale-check', fn () => __('testing.hello-world'))
        ->middleware(['web', SetLocaleFromSession::class]);

    Lang::addLines(['testing.hello-world' => 'Hello World'], 'en');
    Lang::addLines(['testing.hello-world' => 'Bonjour le monde'], 'fr');
});

it('sets locale from session when session has locale', function () {
    session()->put('locale', 'fr');

    $this->get('locale-check')
        ->assertSee('Bonjour le monde');

    expect(app()->getLocale())->toBe('fr');
});

it('uses fallback locale when session does not have locale', function () {
    expect(session()->has('locale'))->toBeFalse();

    $this->get('locale-check')
        ->assertSee('Hello World');

    expect(app()->getLocale())->toBe('en');
});
