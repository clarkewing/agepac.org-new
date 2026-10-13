<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rendering
|--------------------------------------------------------------------------
*/

it('renders the running slot with the initial duration and the done slot', function () {
    $this->freezeTime();
    $until = now()->addSeconds(42)->getTimestamp();

    $html = Blade::render(<<<'BLADE'
        <x-countdown :$until>
            Try again in :countdown.
            <x-slot:done>You can try again now.</x-slot:done>
        </x-countdown>
        BLADE, ['until' => $until]);

    expect($html)
        ->toContain("until: {$until}")
        ->toContain("locale: 'en'")
        ->toContain('Try again in <span x-text="countdown()">42 seconds</span>.')
        ->toContain('You can try again now.');
});

it('formats the initial duration in the current locale', function () {
    $this->freezeTime();
    app()->setLocale('fr');

    $html = Blade::render('<x-countdown :$until>:countdown</x-countdown>', [
        'until' => now()->addSeconds(42)->getTimestamp(),
    ]);

    expect($html)
        ->toContain("locale: 'fr'")
        ->toContain('>42 secondes</span>');
});

it('shows no time left once the moment has passed', function () {
    $this->freezeTime();

    $html = Blade::render('<x-countdown :$until>:countdown</x-countdown>', [
        'until' => now()->subSeconds(10)->getTimestamp(),
    ]);

    expect($html)->toContain('>0 seconds</span>');
});

it('renders no done slot when none is given', function () {
    $html = Blade::render('<x-countdown :$until>:countdown</x-countdown>', [
        'until' => now()->addMinute()->getTimestamp(),
    ]);

    expect($html)->not->toContain('x-show="remaining === 0"');
});

it('passes attributes through to its root element', function () {
    $html = Blade::render('<x-countdown :$until class="block" wire:key="resend-1">:countdown</x-countdown>', [
        'until' => now()->addMinute()->getTimestamp(),
    ]);

    expect($html)->toMatch('/<span[^>]*class="block"[^>]*wire:key="resend-1"/');
});

/*
|--------------------------------------------------------------------------
| Behavior
|--------------------------------------------------------------------------
*/

it('reveals the done slot once the countdown reaches zero', function () {
    countdownPage(now()->addSeconds(3)->getTimestamp());

    visit('/countdown')
        ->assertSee('seconds')
        ->assertDontSee('You can try again now.')
        ->assertSee('You can try again now.') // Automatically waits for the text to appear
        ->assertDontSee('seconds')
        ->assertNoJavaScriptErrors();
});

it('switches from minutes to seconds as it counts down', function () {
    // 62 seconds left reads "2 minutes"; a few ticks later, "59 seconds".
    countdownPage(now()->addSeconds(62)->getTimestamp());

    visit('/countdown')
        ->assertSee('2 minutes')
        ->assertSee('59 seconds') // Automatically waits for the text to appear
        ->assertNoJavaScriptErrors();
});

it('formats the live duration in the current locale', function () {
    countdownPage(now()->addSeconds(62)->getTimestamp(), locale: 'fr');

    visit('/countdown')
        ->assertSee('59 secondes')
        ->assertNoJavaScriptErrors();
});

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

/**
 * Serve a page with a countdown on it, inside the auth layout so Alpine and
 * the countdown component's script are loaded.
 */
function countdownPage(int $until, string $locale = 'en'): void
{
    Route::get('/countdown', function () use ($until, $locale): string {
        app()->setLocale($locale);

        return Blade::render(<<<'BLADE'
            <x-layouts::auth>
                <x-countdown :$until>
                    Try again in :countdown.
                    <x-slot:done>You can try again now.</x-slot:done>
                </x-countdown>
            </x-layouts::auth>
            BLADE, ['until' => $until]);
    })->middleware('web');
}
