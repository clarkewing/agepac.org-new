<?php

use App\Livewire\Settings\Appearance;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders the livewire component', function () {
    $this->get(route('settings.appearance'))
        ->assertOk()
        ->assertSeeLivewire(Appearance::class);
});

it('renders with correct title', function () {
    $this->get(route('settings.appearance'))
        ->assertSeeHtml('<title>'.__('navigation.settings.appearance').' - '.__('settings.title').'</title>');
});

it('mounts with current locale from session', function () {
    session()->put('locale', 'fr');

    Livewire::test(Appearance::class)
        ->assertSet('language', 'fr')
        ->assertSet('languageUpdated', false);
});

it('mounts with default locale when no session locale exists', function () {
    session()->forget('locale');

    Livewire::test(Appearance::class)
        ->assertSet('language', config('app.locale'));
});

it('sets locale and updates session', function () {
    expect(session()->has('locale'))->toBeFalse();
    expect(app()->getLocale())->toBe(config('app.locale'));

    Livewire::test(Appearance::class)
        ->set('language', 'fr')
        ->call('setLocale')
        ->assertSet('languageUpdated', true);

    expect(session('locale'))->toBe('fr');
    expect(app()->getLocale())->toBe('fr');
});

it('updates the languageUpdated flag when language is changed', function () {
    $component = Livewire::test(Appearance::class);

    $component->assertSet('languageUpdated', false);

    $component
        ->set('language', 'fr')
        ->call('setLocale')
        ->assertSet('languageUpdated', true);
});

describe('validation', function () {
    it('requires the language field', function () {
        Livewire::test(Appearance::class)
            ->set('language', '')
            ->call('setLocale')
            ->assertHasErrors(['language' => 'required']);
    });

    it('only accepts valid language values', function () {
        Livewire::test(Appearance::class)
            ->set('language', 'invalid-language')
            ->call('setLocale')
            ->assertHasErrors(['language' => 'in']);
    });

    it('accepts english as valid language', function () {
        Livewire::test(Appearance::class)
            ->set('language', 'en')
            ->call('setLocale')
            ->assertHasNoErrors('language');
    });

    it('accepts french as valid language', function () {
        Livewire::test(Appearance::class)
            ->set('language', 'fr')
            ->call('setLocale')
            ->assertHasNoErrors('language');
    });
});
