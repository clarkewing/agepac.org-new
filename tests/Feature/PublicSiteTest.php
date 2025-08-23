<?php

use Illuminate\Routing\Route as RouteObject;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->routes = collect(Route::getRoutes())
        ->filter(fn (RouteObject $route) => str_starts_with($route->getName(), 'public.')
                                            && in_array('GET', $route->methods())
                                            && ! str_contains($route->uri(), '{'))
        ->map(fn (RouteObject $route) => '/'.ltrim($route->uri(), '/'))
        ->unique()
        ->values()
        ->toArray();
});

it('does not log anything to the console', function () {
    visit($this->routes)->assertNoSmoke();
});

it('does not change appearance', function () {
    visit($this->routes)->assertScreenshotMatches();
});
