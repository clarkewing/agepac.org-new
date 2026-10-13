<?php

use App\Models\User;
use ClarkeWing\Handoff\Http\Middleware\RedirectToHandoff;
use Database\Factories\UserFactory;
use Illuminate\Routing\Route as RouteObject;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\InteractsWithStripe;

uses(InteractsWithStripe::class);

$publicPages = [
    'home' => ['public.home', 'AGEPAC – The ENAC Pilot Association'],
    'selection' => ['public.epl.selection', 'La sélection EPL | AGEPAC'],
    'training' => ['public.epl.training', 'La formation EPL | AGEPAC'],
    'about' => ['public.association.about', 'Qui sommes-nous ? | AGEPAC'],
    'team' => ['public.association.team', 'Notre équipe | AGEPAC'],
    'contact' => ['public.contact', 'Contact | AGEPAC'],
    'press' => ['public.press', 'Presse &amp; médias | AGEPAC'],
    'privacy' => ['public.privacy', 'Politique de Confidentialité | AGEPAC'],
    'terms' => ['public.terms', 'Conditions Générales d’Utilisation | AGEPAC'],
    'remembering' => ['public.remembering', 'En hommage à nos EPL disparus | AGEPAC'],
];

dataset('public pages', $publicPages);

dataset('app pages', [
    'dashboard' => ['dashboard', [], 'Dashboard', 'user'],
    'settings.profile' => ['settings.profile', [], 'Profile - Settings', 'user'],
    'settings.password' => ['settings.password', [], 'Password - Settings', 'user'],
    'settings.membership' => ['settings.membership', [], 'Membership - Settings', 'user'],
    'settings.appearance' => ['settings.appearance', [], 'Appearance - Settings', 'user'],
    'verification.notice' => ['verification.notice', [], 'Verify email', 'unverified'],
    'password.confirm' => ['password.confirm', [], 'Confirm password', 'user'],
    'password.request' => ['password.request', [], 'Forgot password', 'guest'],
    'password.reset' => ['password.reset', ['token' => 'reset-token'], 'Reset password', 'guest'],
]);

it('gives every public page an indexable title', function (string $name, string $title) {
    $this->get(route($name))
        ->assertOk()
        ->assertSeeHtml("<title>{$title}</title>")
        ->assertSeeHtml('<meta name="robots" content="all">');
})->with('public pages');

it('covers every public page', function () use ($publicPages) {
    $covered = collect($publicPages)->map(fn (array $page) => $page[0]);

    $registered = collect(Route::getRoutes())
        ->filter(fn (RouteObject $route) => str_starts_with((string) $route->getName(), 'public.')
                                            && in_array('GET', $route->methods())
                                            && ! str_contains($route->uri(), '{'))
        ->map(fn (RouteObject $route) => $route->getName());

    expect($registered->diff($covered)->values()->all())->toBe([]);
});

it('points the canonical URL at the current page, without its query string', function () {
    $this->get(route('public.association.team').'?utm_source=newsletter')
        ->assertOk()
        ->assertSeeHtml('<link rel="canonical" href="'.secure_url('/association/team').'">');
});

it('takes the legal page metadata from the markdown front matter', function () {
    $this->get(route('public.privacy'))
        ->assertOk()
        ->assertSeeHtml('<title>Politique de Confidentialité | AGEPAC</title>')
        ->assertSeeHtml('<meta name="description" content="Comment l’AGEPAC collecte, traite et protège vos données personnelles.">');

    $this->get(route('public.terms'))
        ->assertOk()
        ->assertSeeHtml('<title>Conditions Générales d’Utilisation | AGEPAC</title>')
        ->assertSeeHtml('<meta name="description" content="Les conditions encadrant juridiquement l’utilisation du site de l’AGEPAC et de ses services.">');
});

describe('app pages', function () {
    // The membership page prices its plans through Stripe. Faking the product
    // catalog keeps this walk offline, so it needs no credentials to run.
    beforeEach(function () {
        $this->fakeStripeMembershipProducts();
    });

    it('gives every app page a title and keeps it out of the index', function (string $name, array $parameters, string $title, string $visitor) {
        $this->withoutMiddleware(RedirectToHandoff::class);

        if ($visitor !== 'guest') {
            $this->actingAs(User::factory()
                ->when($visitor === 'unverified', fn (UserFactory $factory) => $factory->unverified())
                ->create());
        }

        $this->get(route($name, $parameters))
            ->assertOk()
            ->assertSeeHtml('<title>'.$title.' · '.config('app.name').'</title>')
            ->assertSeeHtml('<meta name="robots" content="noindex, follow">');
    })->with('app pages');

    it('translates app page titles', function () {
        $this->actingAs(User::factory()->create());

        $this->withSession(['locale' => 'fr'])
            ->get(route('settings.appearance'))
            ->assertOk()
            ->assertSeeHtml('<title>Apparence - Paramètres · '.config('app.name').'</title>');
    });
});
