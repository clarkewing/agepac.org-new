<?php

use App\Services\Mailcoach\Facades\Mailcoach;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function () {
        Mailcoach::fake();

        // Real-API tests talk to Stripe directly instead of tripping the stray-request guard.
        if (in_array('stripe-api', $this->groups(), true)) {
            $this->allowStripeRequests();
        }
    })
    ->in('Feature');

pest()->extend(TestCase::class)
    ->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->intercept('toBe', Model::class, function (Model $expected) {
    /** @var Model $actual */
    $actual = $this->value;

    expect($actual->is($expected))->toBeTrue(
        sprintf(
            'Model %s[%s] does not match %s[%s].',
            get_class($actual),
            $actual->getKey(),
            get_class($expected),
            $expected->getKey(),
        )
    );
});

expect()->intercept('toEqual', Carbon::class, function (Carbon $expected) {
    expect($this->value->equalTo($expected))
        ->toBeTrue("Failed to assert [{$this->value->toDateTimeString('microsecond')}] is equal to [{$expected->toDateTimeString('microsecond')}].");
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function withEnvironment(string $env, callable $callback): void
{
    $originalEnv = $_ENV['APP_ENV'];

    // The framework may resolve the environment from either superglobal.
    $_ENV['APP_ENV'] = $_SERVER['APP_ENV'] = $env;
    invade(test())->refreshApplication();

    $callback();

    $_ENV['APP_ENV'] = $_SERVER['APP_ENV'] = $originalEnv;
    invade(test())->refreshApplication();
}
