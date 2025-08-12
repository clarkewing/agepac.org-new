<?php

namespace Tests;

use ClarkeWing\LegacySync\Facades\LegacySync;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        LegacySync::fake();
    }
}
