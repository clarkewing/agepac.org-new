<?php

namespace Tests\Concerns;

use Mockery;
use RuntimeException;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;
use Stripe\HttpClient\CurlClient;

/**
 * Install Stripe HTTP-client mocks from a test and have them torn down automatically.
 *
 *     uses(InteractsWithStripe::class);
 *
 *     it('does the thing', function () {
 *         $this->mockStripe([
 *             '/v1/checkout/sessions' => ['id' => 'cs_test_foobar', 'url' => '...'],
 *         ]);
 *         // ...
 *     });
 */
trait InteractsWithStripe
{
    protected bool $stripeMockInstalled = false;

    protected bool $stripePassthrough = false;

    /** @var array<string, array<string, mixed>|\Closure> */
    protected array $stripeResponses = [];

    /** @var list<array{method: string, url: string, params: ?array<string, mixed>}> */
    protected array $stripeRequests = [];

    protected function tearDownInteractsWithStripe(): void
    {
        if (! $this->stripeMockInstalled) {
            return;
        }

        Mockery::close();
        ApiRequestor::setHttpClient(null);
        $this->stripeMockInstalled = false;
        $this->stripePassthrough = false;
        $this->stripeResponses = [];
        $this->stripeRequests = [];
    }

    /**
     * Mock Stripe responses by URL pattern (Str::is).
     * A pattern like "/v1/prices/price_test_foobar" matches any URL ending with it.
     * A response may also be a closure receiving ($method, $url, $params) and
     * returning the payload — useful for responses that depend on the request.
     *
     * Calls are additive: each call merges its patterns into the already registered
     * ones (later registrations win on duplicate patterns), so helpers such as
     * fakeStripeMembershipProducts() can be chained with further mockStripe() calls.
     *
     * Unmatched URLs cause the mock to fail loudly, which surfaces missing pattern
     * entries — chain withStripePassthrough() to send them to the real Stripe API
     * instead.
     *
     * @param  array<string, array<string, mixed>|\Closure>  $responses  URL-pattern → response payload
     */
    public function mockStripe(array $responses): static
    {
        $this->stripeResponses = array_merge($this->stripeResponses, $responses);

        $this->installStripeMock();

        return $this;
    }

    /**
     * Send any request unmatched by mockStripe() patterns to the real Stripe API
     * instead of failing loudly.
     * The environment must provide a real STRIPE_SECRET for those requests to authenticate.
     */
    public function withStripePassthrough(): static
    {
        $this->stripePassthrough = true;

        $this->installStripeMock();

        return $this;
    }

    /**
     * The params sent with the latest intercepted request whose URL matches the
     * given pattern (Str::is), or null if none matched.
     *
     * @return ?array<string, mixed>
     */
    public function stripeRequestParams(string $pattern): ?array
    {
        foreach (array_reverse($this->stripeRequests) as $request) {
            if ($this->stripeUrlMatches($pattern, $request['url'])) {
                return $request['params'];
            }
        }

        return null;
    }

    /**
     * Fake the membership product catalog: pins config('cashier.products.membership')
     * to known ids and fakes their retrieval with a synthetic price, so any
     * view or action resolving Membership::stripePrice() works offline.
     */
    public function fakeStripeMembershipProducts(): static
    {
        config()->set('cashier.products.membership', $products = [
            'agepac' => 'prod_agepac',
            'agepac+alumni' => 'prod_agepac_alumni',
        ]);

        return $this->mockStripe(collect($products)
            ->mapWithKeys(fn (string $productId): array => ["/v1/products/$productId" => [
                'object' => 'product',
                'id' => $productId,
                'default_price' => [
                    'object' => 'price',
                    'id' => str_replace('prod_', 'price_', $productId),
                    'product' => $productId,
                    'unit_amount' => random_int(100, 50000),
                    'currency' => 'eur',
                ],
            ]])
            ->all());
    }

    /**
     * A minimal Stripe product payload with its default price expanded.
     */
    public function productResponse(string $productId, string $priceId): array
    {
        return [
            'object' => 'product',
            'id' => $productId,
            'default_price' => ['object' => 'price', 'id' => $priceId],
        ];
    }

    /**
     * A minimal Stripe price payload.
     */
    public function priceResponse(string $priceId, string $productId): array
    {
        return [
            'object' => 'price',
            'id' => $priceId,
            'product' => $productId,
        ];
    }

    /**
     * A minimal Stripe card payment-method payload.
     */
    public function paymentMethodResponse(string $paymentMethodId, string $brand): array
    {
        return [
            'object' => 'payment_method',
            'id' => $paymentMethodId,
            'type' => 'card',
            'card' => ['brand' => $brand, 'last4' => '4242'],
            'customer' => 'cus_test_foobar',
        ];
    }

    private function installStripeMock(): void
    {
        if ($this->stripeMockInstalled) {
            return;
        }

        if (blank(config('cashier.secret'))) {
            config()->set('cashier.secret', 'sk_test_foobar');
        }

        $mock = Mockery::mock(ClientInterface::class);
        $mock->shouldReceive('request')->andReturnUsing(function ($method, $url, $headers, $params, $hasFile) {
            $this->stripeRequests[] = ['method' => $method, 'url' => $url, 'params' => $params];

            foreach ($this->stripeResponses as $pattern => $response) {
                if ($this->stripeUrlMatches($pattern, $url)) {
                    if ($response instanceof \Closure) {
                        $response = $response($method, $url, $params);
                    }

                    return [json_encode($response), 200, []];
                }
            }

            if ($this->stripePassthrough) {
                return CurlClient::instance()->request($method, $url, $headers, $params, $hasFile);
            }

            throw new RuntimeException("InteractsWithStripe: no mock matched [$method $url]. Add a pattern to mockStripe() or enable passthrough.");
        });

        ApiRequestor::setHttpClient($mock);
        $this->stripeMockInstalled = true;
    }

    private function stripeUrlMatches(string $pattern, string $url): bool
    {
        return str($url)->is(str($pattern)->start('*'));
    }
}
