<?php

namespace Tests;

use ClarkeWing\LegacySync\Facades\LegacySync;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\PreventsStrayStripeRequests;

abstract class TestCase extends BaseTestCase
{
    use PreventsStrayStripeRequests;

    protected function setUp(): void
    {
        parent::setUp();

        LegacySync::fake();

        Http::preventStrayRequests();
    }
}
