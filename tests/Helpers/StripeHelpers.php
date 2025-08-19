<?php

namespace Tests\Helpers;

use Mockery;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;

class StripeHelpers
{
    public static function mockStripeClientWithResponse(array $response, int $times = 1): void
    {
        $mock = Mockery::mock(ClientInterface::class);

        $mock->shouldReceive('request')
            ->times($times)
            ->andReturn([json_encode($response), 200, []]);

        ApiRequestor::setHttpClient($mock);
    }

    public static function cleanup(): void
    {
        Mockery::close();
        ApiRequestor::setHttpClient(null);
    }

    public static function stripeProductResponse(string $priceId): array
    {
        return [
            'id' => 'prod_agepac_123',
            'object' => 'product',
            'default_price' => [
                'id' => $priceId,
                'object' => 'price',
            ],
        ];
    }

    public static function stripePriceResponse(string $priceId, string $productId): array
    {
        return [
            'id' => $priceId,
            'object' => 'price',
            'product' => $productId,
        ];
    }
}
