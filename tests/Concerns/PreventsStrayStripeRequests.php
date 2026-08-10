<?php

namespace Tests\Concerns;

use RuntimeException;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;

/**
 * Fail loudly on any Stripe API request a test did not explicitly mock or allow.
 *
 * The Stripe SDK ships its own curl client, so Http::preventStrayRequests()
 * cannot catch these. Applied suite-wide through the base TestCase; the
 * setUp{TraitName} / tearDown{TraitName} methods are invoked automatically by
 * Laravel's test lifecycle. Mock endpoints with InteractsWithStripe::mockStripe().
 *
 * Real-API tests tagged ->group('stripe-api') are exempted by the global
 * beforeEach hook in Pest.php; allowStripeRequests() undoes the guard directly.
 */
trait PreventsStrayStripeRequests
{
    protected function setUpPreventsStrayStripeRequests(): void
    {
        ApiRequestor::setHttpClient(new class implements ClientInterface
        {
            public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null): array
            {
                throw new RuntimeException("Attempted stray Stripe request [$method $absUrl]. Mock it with mockStripe(), tag the test with group \"stripe-api\", or opt in with allowStripeRequests().");
            }
        });
    }

    protected function tearDownPreventsStrayStripeRequests(): void
    {
        $this->allowStripeRequests();
    }

    public function allowStripeRequests(): void
    {
        ApiRequestor::setHttpClient(null);
    }
}
