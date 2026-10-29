<?php

use App\Models\Passport\Client;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Stripe\Subscription as StripeSubscription;

describe('authorization', function () {
    beforeEach(function () {
        if (! file_exists(Passport::keyPath('oauth-private.key'))) {
            Artisan::call('passport:keys');
        }

        $this->client = Client::factory()->create([
            'name' => 'Perfos',
            'redirect_uris' => ['https://perfos.agepac.test/auth/callback'],
        ]);
    });

    function authorizeUrl(Client $client, array $parameters = []): string
    {
        return route('passport.authorizations.authorize', [
            'client_id' => $client->getKey(),
            'redirect_uri' => 'https://perfos.agepac.test/auth/callback',
            'response_type' => 'code',
            'state' => 'state-value',
            ...$parameters,
        ]);
    }

    it('signs an eligible member straight back in to our app', function () {
        $this->actingAs(User::factory()->withMembership()->create())
            ->get(authorizeUrl($this->client))
            ->assertRedirectContains('https://perfos.agepac.test/auth/callback?code=');
    });

    it('signs a member in to our app once they log in here', function () {
        $user = User::factory()->withMembership()->create();

        $this->get(authorizeUrl($this->client))
            ->assertRedirect(route('login'));

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(authorizeUrl($this->client));

        $this->get(authorizeUrl($this->client))
            ->assertRedirectContains('https://perfos.agepac.test/auth/callback?code=');
    });

    it('explains to an ineligible member what is missing', function (Closure $member, string $reason, string $route) {
        $this->actingAs($member())
            ->get(authorizeUrl($this->client))
            ->assertOk()
            ->assertSee(__('auth.authorize.ineligible.heading', ['app' => 'Perfos']))
            ->assertSee(__("auth.authorize.ineligible.reasons.{$reason}.text"))
            ->assertSee(route($route));
    })->with([
        'unverified email' => [fn () => User::factory()->unverified()->withMembership()->create(), 'unverified', 'verification.notice'],
        'pending approval' => [fn () => User::factory()->pendingApproval()->withMembership()->create(), 'pending-approval', 'pending-approval'],
        'no membership' => [fn () => User::factory()->create(), 'membership', 'settings.membership'],
    ]);

    it('signs an eligible member in to our app once they approve', function () {
        $this->actingAs(User::factory()->withMembership()->create())
            ->get(authorizeUrl($this->client, ['prompt' => 'consent']));

        $this->post(route('passport.authorizations.approve'), ['auth_token' => session('authToken')])
            ->assertRedirectContains('https://perfos.agepac.test/auth/callback?code=');
    });

    it('refuses an ineligible member who approves anyway', function (Closure $member) {
        $this->actingAs($member())
            ->get(authorizeUrl($this->client));

        $this->post(route('passport.authorizations.approve'), ['auth_token' => session('authToken')])
            ->assertRedirectContains('https://perfos.agepac.test/auth/callback?')
            ->assertRedirectContains('error=access_denied');

        $this->assertDatabaseEmpty(Passport::authCode());
    })->with([
        'unverified email' => [fn () => User::factory()->unverified()->withMembership()->create()],
        'pending approval' => [fn () => User::factory()->pendingApproval()->withMembership()->create()],
        'no membership' => [fn () => User::factory()->create()],
    ]);

    it('asks an eligible member for consent when the app requests it', function () {
        $this->actingAs(User::factory()->withMembership()->create())
            ->get(authorizeUrl($this->client, ['prompt' => 'consent']))
            ->assertOk()
            ->assertSee(__('auth.authorize.heading', ['app' => 'Perfos']))
            ->assertSee(__('auth.authorize.approve'));
    });

    it('lets an ineligible member go back to our app', function () {
        $this->actingAs(User::factory()->create())
            ->get(authorizeUrl($this->client))
            ->assertSee(__('auth.authorize.ineligible.back', ['app' => 'Perfos']));

        $this->delete(route('passport.authorizations.deny'), ['auth_token' => session('authToken')])
            ->assertRedirectContains('https://perfos.agepac.test/auth/callback?')
            ->assertRedirectContains('error=access_denied');
    });

    it('offers to sign in with another account', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get($url = authorizeUrl($this->client))
            ->assertSee(__('auth.authorize.signed-in-as', ['name' => $user->name]))
            ->assertSee(url()->query($url, ['prompt' => 'login']));
    });
});

describe('userinfo', function () {
    it('tells our app who the member is', function () {
        $user = User::factory()->withMembership()->create();
        Passport::actingAs($user, client: Client::factory()->create());

        $this->getJson(route('oauth.userinfo'))
            ->assertOk()
            ->assertExactJson([
                'sub' => (string) $user->id,
                'given_name' => $user->first_name,
                'family_name' => $user->last_name,
                'role' => 'member',
            ]);
    });

    it('makes a member holding the app’s permission an admin there', function () {
        $client = Client::factory()->create();
        config()->set('passport.first_party_apps.perfos', $client->getKey());
        Passport::actingAs(User::factory()->admin()->withMembership()->create(), client: $client);

        $this->getJson(route('oauth.userinfo'))
            ->assertJsonPath('role', 'admin');
    });

    it('keeps an admin a member in an app the permissions do not name', function () {
        Passport::actingAs(User::factory()->admin()->withMembership()->create(), client: Client::factory()->create());

        $this->getJson(route('oauth.userinfo'))
            ->assertJsonPath('role', 'member');
    });

    it('refuses a member who may no longer use our apps', function (Closure $member) {
        Passport::actingAs($member(), client: Client::factory()->create());

        $this->getJson(route('oauth.userinfo'))
            ->assertForbidden();
    })->with([
        'unverified email' => [fn () => User::factory()->unverified()->withMembership()->create()],
        'pending approval' => [fn () => User::factory()->pendingApproval()->withMembership()->create()],
        'no membership' => [fn () => User::factory()->create()],
    ]);

    it('refuses a request without an access token', function () {
        $this->getJson(route('oauth.userinfo'))
            ->assertUnauthorized();
    });
});

it('serves the OAuth server on the members host only', function () {
    $publicHost = str(uri(config('app.url'))->host())->after('squawk.');

    $this->get("http://{$publicHost}/oauth/authorize")
        ->assertNotFound();
});

describe('revocation', function () {
    beforeEach(function () {
        $this->user = User::factory()->withMembership()->create();

        $this->token = Passport::token()->forceCreate([
            'id' => Str::random(80),
            'user_id' => $this->user->id,
            'client_id' => Client::factory()->create()->getKey(),
            'revoked' => false,
            'expires_at' => now()->addHour(),
        ]);

        Passport::refreshToken()->forceCreate([
            'id' => Str::random(80),
            'access_token_id' => $this->token->id,
            'revoked' => false,
            'expires_at' => now()->addMonth(),
        ]);
    });

    it('revokes the tokens of a member whose membership ends', function () {
        $this->user->subscriptions()->first()->update([
            'stripe_status' => StripeSubscription::STATUS_CANCELED,
            'ends_at' => now(),
        ]);

        expect($this->token->fresh())->revoked->toBeTrue()
            ->and($this->token->refreshToken)->revoked->toBeTrue();
    });

    it('revokes the tokens of a member who may no longer use our apps', function (array $attributes) {
        $this->user->forceFill($attributes)->save();

        expect($this->token->fresh())->revoked->toBeTrue()
            ->and($this->token->refreshToken)->revoked->toBeTrue();
    })->with([
        'unverified email' => [['email_verified_at' => null]],
        'approval withdrawn' => [['approved_at' => null]],
    ]);

    it('keeps the tokens of a member who still may use our apps', function () {
        $this->user->subscriptions()->first()->update(['quantity' => 2]);
        $this->user->forceFill(['approved_at' => now()->subDay()])->save();

        expect($this->token->fresh())->revoked->toBeFalse()
            ->and($this->token->refreshToken)->revoked->toBeFalse();
    });
});
